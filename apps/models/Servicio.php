<?php

class Servicio
{
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

    public function guardar($clienteId, $pasajeroId, $conductorId, $vehiculoId, $destinoId, $fecha, $hora, $estado)
    {
        $ids = [
            'cliente' => &$clienteId,
            'pasajero' => &$pasajeroId,
            'conductor' => &$conductorId,
            'vehículo' => &$vehiculoId,
            'destino' => &$destinoId
        ];

        foreach ($ids as $nombre => &$id) {
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if ($id === false || $id < 1) {
                throw new InvalidArgumentException('El ID de ' . $nombre . ' no es válido.');
            }
        }
        unset($id);

        $fecha = trim($fecha);
        $hora = trim($hora);
        $estado = trim($estado);
        if ($fecha === '' || $hora === '' || $estado === '') {
            throw new InvalidArgumentException('Completa la fecha, hora y estado del servicio.');
        }

        try {
            $sql = 'INSERT INTO servicios
                        (cliente_id, pasajero_id, conductor_id, vehiculo_id, destino_id,
                         fecha_servicio, hora_servicio, estado)
                    VALUES
                        (:cliente_id, :pasajero_id, :conductor_id, :vehiculo_id, :destino_id,
                         :fecha_servicio, :hora_servicio, :estado)';
            $consulta = $this->conexion->prepare($sql);
            $consulta->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
            $consulta->bindParam(':pasajero_id', $pasajeroId, PDO::PARAM_INT);
            $consulta->bindParam(':conductor_id', $conductorId, PDO::PARAM_INT);
            $consulta->bindParam(':vehiculo_id', $vehiculoId, PDO::PARAM_INT);
            $consulta->bindParam(':destino_id', $destinoId, PDO::PARAM_INT);
            $consulta->bindParam(':fecha_servicio', $fecha);
            $consulta->bindParam(':hora_servicio', $hora);
            $consulta->bindParam(':estado', $estado);

            if (!$consulta->execute()) {
                return false;
            }

            return $this->obtenerTodos();
        } catch (PDOException $e) {
            error_log('Error al guardar el servicio: ' . $e->getMessage());
            return false;
        }
    }


    public function obtenerTodos()
    {
        try {

            $sql = "SELECT
                        servicios.id,

                        clientes.nombre AS cliente,

                        pasajeros.nombre AS pasajero,

                        conductores.nombre AS conductor,

                        CONCAT(
                            vehiculos.marca,
                            ' ',
                            vehiculos.modelo,
                            ' - ',
                            vehiculos.placa
                        ) AS vehiculo,

                        CONCAT(
                            destinos.ciudad_origen,
                            ' - ',
                            destinos.ciudad_destino
                        ) AS destino,

                        destinos.tarifa,

                        servicios.fecha_servicio,
                        servicios.hora_servicio,
                        servicios.estado

                    FROM servicios

                    LEFT JOIN clientes
                        ON servicios.cliente_id = clientes.id

                    LEFT JOIN pasajeros
                        ON servicios.pasajero_id = pasajeros.id

                    LEFT JOIN conductores
                        ON servicios.conductor_id = conductores.id

                    LEFT JOIN vehiculos
                        ON servicios.vehiculo_id = vehiculos.id

                    LEFT JOIN destinos
                        ON servicios.destino_id = destinos.id

                    ORDER BY servicios.id DESC";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            echo "Error al obtener los servicios: "
                . $e->getMessage();

            return [];
        }
    }


    public function getById($id)
    {
        try {

            if ($id <= 0) {

                throw new Exception(
                    "El ID del servicio no es válido."
                );
            }

            $sql = "SELECT
                        servicios.id,

                        clientes.nombre AS cliente,

                        pasajeros.nombre AS pasajero,

                        conductores.nombre AS conductor,

                        CONCAT(
                            vehiculos.marca,
                            ' ',
                            vehiculos.modelo,
                            ' - ',
                            vehiculos.placa
                        ) AS vehiculo,

                        CONCAT(
                            destinos.ciudad_origen,
                            ' - ',
                            destinos.ciudad_destino
                        ) AS destino,

                        destinos.tarifa,

                        servicios.fecha_servicio,
                        servicios.hora_servicio,
                        servicios.estado

                    FROM servicios

                    LEFT JOIN clientes
                        ON servicios.cliente_id = clientes.id

                    LEFT JOIN pasajeros
                        ON servicios.pasajero_id = pasajeros.id

                    LEFT JOIN conductores
                        ON servicios.conductor_id = conductores.id

                    LEFT JOIN vehiculos
                        ON servicios.vehiculo_id = vehiculos.id

                    LEFT JOIN destinos
                        ON servicios.destino_id = destinos.id

                    WHERE servicios.id = :id";

            $consulta = $this->conexion->prepare($sql);

            $consulta->bindParam(
                ':id',
                $id,
                PDO::PARAM_INT
            );

            $consulta->execute();

            $servicio = $consulta->fetch(
                PDO::FETCH_ASSOC
            );

            if (!$servicio) {

                throw new Exception(
                    "Servicio no encontrado."
                );
            }

            return $servicio;

        } catch (Exception $e) {

            echo "Error: " . $e->getMessage();

            return null;
        }
    }
}
?>
