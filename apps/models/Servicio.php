<?php

class Servicio
{
    public const ESTADOS = ['Pendiente', 'En proceso', 'Finalizado'];
    public const ESTADO_FINALIZADO = 'Finalizado';

    private $conexion;

    private $nombre;
    private $descripcion;
    private $precio;
    private $duracionMinutos;

    public function __construct(
        $conexion,
        $nombre = '',
        $descripcion = '',
        $precio = 0,
        $duracionMinutos = 0
    ) {
        $this->conexion = $conexion;

        if ($nombre !== '') {
            $this->setNombre($nombre);
        }

        if ($descripcion !== '') {
            $this->setDescripcion($descripcion);
        }

        if ($precio > 0) {
            $this->setPrecio($precio);
        }

        if ($duracionMinutos > 0) {
            $this->setDuracionMinutos($duracionMinutos);
        }
    }

    public function setNombre($nombre)
    {
        if (trim($nombre) === '') {
            throw new InvalidArgumentException(
                'El nombre no puede estar vacío.'
            );
        }

        $this->nombre = $nombre;
    }

    public function getNombre()
    {
        return $this->nombre;
    }


    public function setDescripcion($descripcion)
    {
        if (trim($descripcion) === '') {
            throw new InvalidArgumentException(
                'La descripción no puede estar vacía.'
            );
        }

        $this->descripcion = $descripcion;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }


    public function setPrecio($precio)
    {
        if ($precio <= 0) {
            throw new InvalidArgumentException(
                'El precio debe ser mayor que cero.'
            );
        }

        $this->precio = $precio;
    }

    public function getPrecio()
    {
        return $this->precio;
    }


    public function setDuracionMinutos($duracionMinutos)
    {
        if ($duracionMinutos <= 0) {
            throw new InvalidArgumentException(
                'La duración debe ser mayor que cero.'
            );
        }

        $this->duracionMinutos = $duracionMinutos;
    }

    public function getDuracionMinutos()
    {
        return $this->duracionMinutos;
    }

    /**
     * Valida y normaliza los datos de un servicio. Lanza
     * InvalidArgumentException con un mensaje claro si algo no es válido.
     */
    private function validarDatos(array $datos)
    {
        $ids = [
            'cliente_id' => 'cliente',
            'pasajero_id' => 'pasajero',
            'conductor_id' => 'conductor',
            'vehiculo_id' => 'vehículo',
            'destino_id' => 'destino',
        ];
        $limpios = [];

        foreach ($ids as $campo => $nombre) {
            $valor = filter_var($datos[$campo] ?? null, FILTER_VALIDATE_INT);
            if ($valor === false || $valor < 1) {
                throw new InvalidArgumentException('El ID de ' . $nombre . ' no es válido.');
            }
            $limpios[$campo] = $valor;
        }

        $fechaSalida = trim((string) ($datos['fecha_servicio'] ?? ''));
        $horaSalida = trim((string) ($datos['hora_servicio'] ?? ''));
        $fechaLlegada = trim((string) ($datos['fecha_llegada'] ?? ''));
        $horaLlegada = trim((string) ($datos['hora_llegada'] ?? ''));
        $estado = trim((string) ($datos['estado'] ?? ''));

        $salida = $this->crearFechaHora($fechaSalida, $horaSalida);
        $llegada = $this->crearFechaHora($fechaLlegada, $horaLlegada);
        if ($salida === null) {
            throw new InvalidArgumentException('Completa una fecha y hora de salida válidas.');
        }
        if ($llegada === null) {
            throw new InvalidArgumentException('Completa una fecha y hora de llegada válidas.');
        }
        if ($llegada < $salida) {
            throw new InvalidArgumentException('La llegada no puede ser anterior a la salida.');
        }
        if (!in_array($estado, self::ESTADOS, true)) {
            throw new InvalidArgumentException('El estado del servicio no es válido.');
        }

        $limpios[':fecha_servicio'] = $salida->format('Y-m-d');
        $limpios[':hora_servicio'] = $salida->format('H:i:s');
        $limpios[':fecha_llegada'] = $llegada->format('Y-m-d');
        $limpios[':hora_llegada'] = $llegada->format('H:i:s');
        $limpios[':estado'] = $estado;

        return [
            ':cliente_id' => $limpios['cliente_id'],
            ':pasajero_id' => $limpios['pasajero_id'],
            ':conductor_id' => $limpios['conductor_id'],
            ':vehiculo_id' => $limpios['vehiculo_id'],
            ':destino_id' => $limpios['destino_id'],
            ':fecha_servicio' => $limpios[':fecha_servicio'],
            ':hora_servicio' => $limpios[':hora_servicio'],
            ':fecha_llegada' => $limpios[':fecha_llegada'],
            ':hora_llegada' => $limpios[':hora_llegada'],
            ':estado' => $limpios[':estado'],
        ];
    }

    private function crearFechaHora($fecha, $hora)
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $hora)) {
            return null;
        }

        $formato = strlen($hora) === 5 ? 'Y-m-d H:i' : 'Y-m-d H:i:s';
        $fechaHora = DateTime::createFromFormat('!' . $formato, $fecha . ' ' . $hora);
        $errores = DateTime::getLastErrors();
        if ($fechaHora === false || (is_array($errores) && ($errores['warning_count'] > 0 || $errores['error_count'] > 0))) {
            return null;
        }

        return $fechaHora;
    }

    public function guardar($datos)
    {
        $parametros = $this->validarDatos($datos);

        try {
            $sql = 'INSERT INTO servicios
                        (cliente_id, pasajero_id, conductor_id, vehiculo_id, destino_id,
                         fecha_servicio, hora_servicio, fecha_llegada, hora_llegada, estado)
                    VALUES
                        (:cliente_id, :pasajero_id, :conductor_id, :vehiculo_id, :destino_id,
                         :fecha_servicio, :hora_servicio, :fecha_llegada, :hora_llegada, :estado)';

            return $this->conexion->prepare($sql)->execute($parametros);
        } catch (PDOException $e) {
            error_log('Error al guardar el servicio: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Indica si el servicio ya está finalizado (y por lo tanto bloqueado).
     */
    public function estaFinalizado($id)
    {
        try {
            $consulta = $this->conexion->prepare('SELECT estado FROM servicios WHERE id = :id');
            $consulta->execute([':id' => (int) $id]);

            return $consulta->fetchColumn() === self::ESTADO_FINALIZADO;
        } catch (PDOException $e) {
            error_log('Error al consultar el estado del servicio: ' . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $datos)
    {
        $parametros = $this->validarDatos($datos);
        $parametros[':id'] = (int) $id;
        $parametros[':finalizado'] = self::ESTADO_FINALIZADO;

        try {
            // La condición sobre el estado impide modificar servicios finalizados
            // aunque alguien envíe el formulario directamente.
            $sql = 'UPDATE servicios
                    SET cliente_id = :cliente_id, pasajero_id = :pasajero_id,
                        conductor_id = :conductor_id, vehiculo_id = :vehiculo_id,
                        destino_id = :destino_id, fecha_servicio = :fecha_servicio,
                        hora_servicio = :hora_servicio, fecha_llegada = :fecha_llegada,
                        hora_llegada = :hora_llegada, estado = :estado
                    WHERE id = :id AND estado <> :finalizado';

            return $this->conexion->prepare($sql)->execute($parametros);
        } catch (PDOException $e) {
            error_log('Error al actualizar el servicio: ' . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id)
    {
        try {
            $consulta = $this->conexion->prepare(
                'DELETE FROM servicios WHERE id = :id'
            );
            $consulta->execute([':id' => (int) $id]);

            return $consulta->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Error al eliminar el servicio: ' . $e->getMessage());
            return false;
        }
    }

    private function consultaBase()
    {
        return "SELECT
                    servicios.id,
                    servicios.cliente_id,
                    servicios.pasajero_id,
                    servicios.conductor_id,
                    servicios.vehiculo_id,
                    servicios.destino_id,
                    clientes.nombre AS cliente,
                    pasajeros.nombre AS pasajero,
                    conductores.nombre AS conductor,
                    vehiculos.tipo AS tipo_vehiculo,
                    vehiculos.placa AS placa,
                    CONCAT(vehiculos.marca, ' ', vehiculos.modelo) AS vehiculo,
                    CONCAT(destinos.ciudad_origen, ' - ', destinos.ciudad_destino) AS destino,
                    destinos.tarifa,
                    servicios.fecha_servicio,
                    servicios.hora_servicio,
                    servicios.fecha_llegada,
                    servicios.hora_llegada,
                    servicios.estado
                FROM servicios
                LEFT JOIN clientes ON servicios.cliente_id = clientes.id
                LEFT JOIN pasajeros ON servicios.pasajero_id = pasajeros.id
                LEFT JOIN conductores ON servicios.conductor_id = conductores.id
                LEFT JOIN vehiculos ON servicios.vehiculo_id = vehiculos.id
                LEFT JOIN destinos ON servicios.destino_id = destinos.id";
    }

    private function agregarFechasLegibles(array $servicio)
    {
        $unir = static function ($fecha, $hora) {
            if (empty($fecha)) {
                return '—';
            }

            return $fecha . ($hora ? ' ' . substr((string) $hora, 0, 5) : '');
        };

        $servicio['salida'] = $unir($servicio['fecha_servicio'] ?? '', $servicio['hora_servicio'] ?? '');
        $servicio['llegada'] = $unir($servicio['fecha_llegada'] ?? '', $servicio['hora_llegada'] ?? '');

        return $servicio;
    }

    public function obtenerTodos()
    {
        try {
            $consulta = $this->conexion->prepare($this->consultaBase() . ' ORDER BY servicios.id DESC');
            $consulta->execute();

            return array_map([$this, 'agregarFechasLegibles'], $consulta->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            error_log('Error al obtener los servicios: ' . $e->getMessage());
            return [];
        }
    }

    public function getById($id)
    {
        try {
            if ($id <= 0) {
                throw new Exception('El ID del servicio no es válido.');
            }

            $consulta = $this->conexion->prepare($this->consultaBase() . ' WHERE servicios.id = :id');
            $consulta->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $consulta->execute();
            $servicio = $consulta->fetch(PDO::FETCH_ASSOC);

            if (!$servicio) {
                throw new Exception('Servicio no encontrado.');
            }

            return $this->agregarFechasLegibles($servicio);
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
            return null;
        }
    }
}
