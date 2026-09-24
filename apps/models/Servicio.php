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
