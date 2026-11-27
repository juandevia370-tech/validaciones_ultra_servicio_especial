<?php

require_once __DIR__ . '/../models/Usuario.php';

/**
 * Autenticación propia del sistema: usuarios en la base de datos,
 * contraseñas con bcrypt y sesión PHP en el servidor.
 */
class Autenticacion
{
    public const MAX_INTENTOS = 5;
    public const MINUTOS_BLOQUEO = 15;
    public const INACTIVIDAD_MAXIMA = 7200;   // 2 horas sin actividad
    public const DURACION_MAXIMA = 43200;     // 12 horas desde el inicio de sesión

    // Hash bcrypt de una contraseña ficticia: se verifica cuando el correo no
    // existe para que la respuesta tarde lo mismo y no revele qué correos existen.
    private const HASH_FICTICIO = '$2y$10$gXC1wRi9jhuEXYMkYG2qKOQlSptXnf7.F1OcGpTCs8L.2X1X8hfnu';

    private $usuarios;

    public function __construct(Usuario $usuarios)
    {
        $this->usuarios = $usuarios;
    }

    private static function marcaTiempo($valor)
    {
        if (empty($valor)) {
            return 0;
        }

        try {
            return (new DateTime((string) $valor, new DateTimeZone('UTC')))->getTimestamp();
        } catch (Exception $e) {
            return 0;
        }
    }

    public static function huella($hash)
    {
        // Cambia cuando cambia la contraseña: invalida las sesiones abiertas.
        return hash('sha256', (string) $hash);
    }

    /**
     * @return array{ok: bool, motivo: string}
     *         motivo: credenciales | bloqueado | inactivo | ''
     */
    public function iniciarSesion($correo, $contrasena)
    {
        $usuario = $this->usuarios->buscarPorCorreoParaLogin($correo);

        if ($usuario === null) {
            password_verify((string) $contrasena, self::HASH_FICTICIO);
            return ['ok' => false, 'motivo' => 'credenciales'];
        }

        if (self::marcaTiempo($usuario['bloqueado_hasta']) > time()) {
            password_verify((string) $contrasena, self::HASH_FICTICIO);
            return ['ok' => false, 'motivo' => 'bloqueado'];
        }

        if (!password_verify((string) $contrasena, $usuario['contrasena_hash'])) {
            $intentos = (int) $usuario['intentos_fallidos'] + 1;
            if ($intentos >= self::MAX_INTENTOS) {
                $this->usuarios->registrarFallo(
                    $usuario['id'],
                    0,
                    gmdate('Y-m-d H:i:s', time() + self::MINUTOS_BLOQUEO * 60)
                );
                return ['ok' => false, 'motivo' => 'bloqueado'];
            }
            $this->usuarios->registrarFallo($usuario['id'], $intentos, null);
            return ['ok' => false, 'motivo' => 'credenciales'];
        }

        if (!Usuario::esActivo($usuario['activo'])) {
            return ['ok' => false, 'motivo' => 'inactivo'];
        }

        // Actualiza hashes antiguos (por ejemplo, los traídos de otro sistema).
        $hash = $usuario['contrasena_hash'];
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            $hash = password_hash((string) $contrasena, PASSWORD_DEFAULT);
            $this->usuarios->actualizarHash($usuario['id'], $hash);
        }
        $this->usuarios->registrarAcceso($usuario['id']);

        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int) $usuario['id'];
        $_SESSION['usuario_huella'] = self::huella($hash);
        $_SESSION['sesion_inicio'] = time();
        $_SESSION['ultima_actividad'] = time();
        csrf_regenerate();

        return ['ok' => true, 'motivo' => ''];
    }

    /**
     * Devuelve el usuario de la sesión actual o null si no hay sesión válida.
     */
    public function usuarioActual()
    {
        $id = (int) ($_SESSION['usuario_id'] ?? 0);
        if ($id < 1) {
            return null;
        }

        $ahora = time();
        if (
            $ahora - (int) ($_SESSION['ultima_actividad'] ?? 0) > self::INACTIVIDAD_MAXIMA
            || $ahora - (int) ($_SESSION['sesion_inicio'] ?? 0) > self::DURACION_MAXIMA
        ) {
            return null;
        }

        $usuario = $this->usuarios->buscarPorIdParaSesion($id);
        if (
            $usuario === null
            || !Usuario::esActivo($usuario['activo'])
            || !hash_equals((string) ($_SESSION['usuario_huella'] ?? ''), self::huella($usuario['contrasena_hash']))
        ) {
            return null;
        }

        $_SESSION['ultima_actividad'] = $ahora;

        return [
            'id' => (int) $usuario['id'],
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'email' => $usuario['correo'],
        ];
    }
}
