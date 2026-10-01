<?php

class Pasajero
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function guardar($nombre, $documento, $telefono, $clienteId)
    {
        try {
            $nombre = trim($nombre);
            $documento = trim($documento);
            $telefono = trim($telefono);
            $clienteId = filter_var($clienteId, FILTER_VALIDATE_INT);

            if ($clienteId === false || $clienteId < 1) {
                throw new InvalidArgumentException('El ID del cliente no es válido.');
            }

            $sql = 'INSERT INTO pasajeros (nombre, documento, telefono, cliente_id)
                    VALUES (:nombre, :documento, :telefono, :cliente_id)';
            $consulta = $this->conexion->prepare($sql);
            $consulta->bindParam(':nombre', $nombre);
            $consulta->bindParam(':documento', $documento);
            $consulta->bindParam(':telefono', $telefono);
            $consulta->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);

            if (!$consulta->execute()) {
                return false;
            }

            return $this->obtenerTodos();
        } catch (PDOException $e) {
            error_log('Error al guardar el pasajero: ' . $e->getMessage());
            return false;
        }
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


            return $pasajero;

        } catch (Exception $e) {

            echo "Error: " . $e->getMessage();

            return null;
        }
    }
}