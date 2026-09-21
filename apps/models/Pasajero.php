<?php

class Pasajero
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
                        pasajeros.id,
                        pasajeros.nombre,
                        pasajeros.documento,
                        pasajeros.telefono,
                        clientes.nombre AS cliente
                    FROM pasajeros
                    LEFT JOIN clientes
                        ON pasajeros.cliente_id = clientes.id";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            echo "Error al obtener pasajeros: "
                . $e->getMessage();

            return [];
        }
    }

    public function getById($id)
    {
        try {

            if ($id <= 0) {
                throw new Exception(
                    "El ID del pasajero no es válido."
                );
            }

            $sql = "SELECT
                        pasajeros.id,
                        pasajeros.nombre,
                        pasajeros.documento,
                        pasajeros.telefono,
                        clientes.nombre AS cliente
                    FROM pasajeros
                    LEFT JOIN clientes
                        ON pasajeros.cliente_id = clientes.id
                    WHERE pasajeros.id = :id";

            $consulta = $this->conexion->prepare($sql);

            $consulta->bindParam(
                ':id',
                $id,
                PDO::PARAM_INT
            );

            $consulta->execute();

            $pasajero = $consulta->fetch(
                PDO::FETCH_ASSOC
            );

            if (!$pasajero) {
                throw new Exception(
                    "Pasajero no encontrado."
                );
            }

            return $pasajero;

        } catch (Exception $e) {

            echo "Error: " . $e->getMessage();

            return null;
        }
    }
}