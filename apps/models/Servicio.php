<?php

class Servicio
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
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