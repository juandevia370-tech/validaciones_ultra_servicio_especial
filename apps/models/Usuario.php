<?php

/**
 * Usuarios del sistema (autenticación propia, sin servicios externos).
 * Las contraseñas solo se guardan como hash bcrypt (password_hash).
 */
class Usuario
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Reglas de contraseña: 8 a 72 caracteres, con al menos una letra y un número.
     * (72 es el máximo que bcrypt tiene en cuenta.)
     */
    public static function validarContrasena($contrasena, $confirmacion)
    {
        $contrasena = (string) $contrasena;
        if (strlen($contrasena) < 8 || strlen($contrasena) > 72) {
            throw new InvalidArgumentException('La contraseña debe tener entre 8 y 72 caracteres.');
        }
        if (!preg_match('/\pL/u', $contrasena) || !preg_match('/\d/', $contrasena)) {
            throw new InvalidArgumentException('La contraseña debe incluir al menos una letra y un número.');
        }
        if (!hash_equals($contrasena, (string) $confirmacion)) {
            throw new InvalidArgumentException('Las contraseñas no coinciden.');
        }
    }

    private static function normalizarCorreo($correo)
    {
        $correo = strtolower(trim((string) $correo));
        if (strlen($correo) > 254 || filter_var($correo, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('El correo electrónico no es válido.');
        }

        return $correo;
    }

    private static function normalizarNombre($nombre)
    {
        $nombre = trim((string) $nombre);
        if ($nombre === '' || mb_strlen($nombre) > 100) {
            throw new InvalidArgumentException('El nombre es obligatorio (máximo 100 caracteres).');
        }

        return $nombre;
    }

    public static function esActivo($valor)
    {
        return $valor === true || $valor === 1 || $valor === '1' || $valor === 't' || $valor === 'true';
    }

    private static function fechaLegible($valor)
    {
        if (empty($valor)) {
            return '—';
        }

        try {
            $fecha = new DateTime((string) $valor, new DateTimeZone('UTC'));
            $fecha->setTimezone(new DateTimeZone('America/Bogota'));

            return $fecha->format('Y-m-d H:i');
        } catch (Exception $e) {
            return '—';
        }
    }

    private function esCorreoDuplicado(PDOException $e)
    {
        return in_array($e->getCode(), ['23000', '23505'], true);
    }

    // ----- Consultas para la autenticación (incluyen el hash; no van a las vistas) -----

    public function buscarPorCorreoParaLogin($correo)
    {
        $consulta = $this->conexion->prepare(
            'SELECT id, nombre, correo, contrasena_hash, activo, intentos_fallidos, bloqueado_hasta
             FROM usuarios WHERE correo = :correo'
        );
        $consulta->execute([':correo' => strtolower(trim((string) $correo))]);

        return $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function buscarPorIdParaSesion($id)
    {
        $consulta = $this->conexion->prepare(
            'SELECT id, nombre, correo, contrasena_hash, activo FROM usuarios WHERE id = :id'
        );
        $consulta->execute([':id' => (int) $id]);

        return $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function registrarFallo($id, $intentos, $bloqueadoHasta)
    {
        $this->conexion->prepare(
            'UPDATE usuarios SET intentos_fallidos = :intentos, bloqueado_hasta = :bloqueado WHERE id = :id'
        )->execute([
            ':intentos' => (int) $intentos,
            ':bloqueado' => $bloqueadoHasta,
            ':id' => (int) $id,
        ]);
    }

    public function registrarAcceso($id)
    {
        $this->conexion->prepare(
            'UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL, ultimo_acceso = :ahora WHERE id = :id'
        )->execute([':ahora' => gmdate('Y-m-d H:i:s'), ':id' => (int) $id]);
    }

    public function actualizarHash($id, $hash)
    {
        $this->conexion->prepare('UPDATE usuarios SET contrasena_hash = :hash WHERE id = :id')
            ->execute([':hash' => $hash, ':id' => (int) $id]);
    }

    // ----- Administración (sin hash) -----

    public function obtenerTodos()
    {
        try {
            $filas = $this->conexion->query(
                'SELECT id, nombre, correo, activo, ultimo_acceso, creado_en FROM usuarios ORDER BY id'
            )->fetchAll(PDO::FETCH_ASSOC);

            return array_map(static function ($fila) {
                $fila['estado'] = Usuario::esActivo($fila['activo']) ? 'Activo' : 'Inactivo';
                $fila['ultimo_acceso_legible'] = Usuario::fechaLegible($fila['ultimo_acceso']);

                return $fila;
            }, $filas);
        } catch (PDOException $e) {
            error_log('Error al obtener los usuarios: ' . $e->getMessage());
            return [];
        }
    }

    public function getById($id)
    {
        try {
            $consulta = $this->conexion->prepare(
                'SELECT id, nombre, correo, activo FROM usuarios WHERE id = :id'
            );
            $consulta->execute([':id' => (int) $id]);
            $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
            if ($usuario) {
                $usuario['activo'] = self::esActivo($usuario['activo']);
            }

            return $usuario ?: null;
        } catch (PDOException $e) {
            error_log('Error al consultar el usuario: ' . $e->getMessage());
            return null;
        }
    }

    public function contarActivos()
    {
        $filas = $this->conexion->query('SELECT activo FROM usuarios')->fetchAll(PDO::FETCH_COLUMN);

        return count(array_filter($filas, [self::class, 'esActivo']));
    }

    public function crear($nombre, $correo, $contrasena, $confirmacion)
    {
        $nombre = self::normalizarNombre($nombre);
        $correo = self::normalizarCorreo($correo);
        self::validarContrasena($contrasena, $confirmacion);

        try {
            $consulta = $this->conexion->prepare(
                'INSERT INTO usuarios (nombre, correo, contrasena_hash, activo, creado_en)
                 VALUES (:nombre, :correo, :hash, :activo, :creado)'
            );
            $consulta->bindValue(':nombre', $nombre);
            $consulta->bindValue(':correo', $correo);
            $consulta->bindValue(':hash', password_hash($contrasena, PASSWORD_DEFAULT));
            $consulta->bindValue(':activo', true, PDO::PARAM_BOOL);
            $consulta->bindValue(':creado', gmdate('Y-m-d H:i:s'));

            return $consulta->execute();
        } catch (PDOException $e) {
            if ($this->esCorreoDuplicado($e)) {
                throw new InvalidArgumentException('Ya existe un usuario con ese correo.');
            }
            error_log('Error al crear el usuario: ' . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $nombre, $correo, $activo, $contrasena = '', $confirmacion = '')
    {
        $nombre = self::normalizarNombre($nombre);
        $correo = self::normalizarCorreo($correo);
        $cambiarContrasena = (string) $contrasena !== '';
        if ($cambiarContrasena) {
            self::validarContrasena($contrasena, $confirmacion);
        }

        try {
            $sql = 'UPDATE usuarios SET nombre = :nombre, correo = :correo, activo = :activo'
                . ($cambiarContrasena ? ', contrasena_hash = :hash, intentos_fallidos = 0, bloqueado_hasta = NULL' : '')
                . ' WHERE id = :id';
            $consulta = $this->conexion->prepare($sql);
            $consulta->bindValue(':nombre', $nombre);
            $consulta->bindValue(':correo', $correo);
            $consulta->bindValue(':activo', (bool) $activo, PDO::PARAM_BOOL);
            $consulta->bindValue(':id', (int) $id, PDO::PARAM_INT);
            if ($cambiarContrasena) {
                $consulta->bindValue(':hash', password_hash($contrasena, PASSWORD_DEFAULT));
            }

            return $consulta->execute();
        } catch (PDOException $e) {
            if ($this->esCorreoDuplicado($e)) {
                throw new InvalidArgumentException('Ya existe un usuario con ese correo.');
            }
            error_log('Error al actualizar el usuario: ' . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id)
    {
        try {
            $consulta = $this->conexion->prepare('DELETE FROM usuarios WHERE id = :id');
            $consulta->execute([':id' => (int) $id]);

            return $consulta->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Error al eliminar el usuario: ' . $e->getMessage());
            return false;
        }
    }
}
