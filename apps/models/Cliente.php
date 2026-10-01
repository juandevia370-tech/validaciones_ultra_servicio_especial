<?php

class Cliente
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function guardar($nombre, $documento, $correo, $telefono)
    {
        try {
            $nombre = trim($nombre);
            $documento = trim($documento);
            $correo = trim($correo);
            $telefono = trim($telefono);

            $sql = 'INSERT INTO clientes (nombre, documento, correo, telefono)
                    VALUES (:nombre, :documento, :correo, :telefono)';
            $consulta = $this->conexion->prepare($sql);
            $consulta->bindParam(':nombre', $nombre);
            $consulta->bindParam(':documento', $documento);
            $consulta->bindParam(':correo', $correo);
            $consulta->bindParam(':telefono', $telefono);

            if (!$consulta->execute()) {
                return false;
            }

            return $this->obtenerTodos();
        } catch (PDOException $e) {
            error_log('Error al guardar el cliente: ' . $e->getMessage());
            return false;
        }
    }

    public function obtenerTodos()
    {
        try {

            $sql = "SELECT * FROM clientes";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            echo "Error al obtener clientes: "
                . $e->getMessage();

            return [];
        }
    }

    public function getById($id)
    {
        try {

            if ($id <= 0) {
                throw new Exception(
                    "El ID del cliente no es válido."
                );
            }

            $sql = "SELECT * FROM clientes
                    WHERE id = :id";

            $consulta = $this->conexion->prepare($sql);

            $consulta->bindParam(
                ':id',
                $id,
                PDO::PARAM_INT
            );

            $consulta->execute();

            $cliente = $consulta->fetch(
                PDO::FETCH_ASSOC
            );

            if (!$cliente) {
                throw new Exception(
                    "Cliente no encontrado."
                );
            }

            return $cliente;

        } catch (Exception $e) {

            echo "Error: " . $e->getMessage();

            return null;
        }
    }
}