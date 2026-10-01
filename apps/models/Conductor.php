<?php

class Conductor
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function guardar($nombre, $documento, $licencia, $telefono)
    {
        try {
            $nombre = trim($nombre);
            $documento = trim($documento);
            $licencia = trim($licencia);
            $telefono = trim($telefono);

        $sql = 'INSERT INTO conductores (nombre, documento, licencia, telefono)
                VALUES (:nombre, :documento, :licencia, :telefono)';
        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(':nombre', $nombre);
        $consulta->bindParam(':documento', $documento);
        $consulta->bindParam(':licencia', $licencia);
        $consulta->bindParam(':telefono', $telefono);

            if (!$consulta->execute()) {
                return false;
            }

            return $this->obtenerTodos();
        }catch (PDOException $e) {
            echo "Error al guardar el conductor: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerTodos()
    {
        try {

            $sql = "SELECT * FROM conductores";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            echo "Error al obtener conductores: "
                . $e->getMessage();

            return [];
        }
    }

    public function getById($id)
    {
        try {

            if ($id <= 0) {
                throw new Exception(
                    "El ID del conductor no es válido."
                );
            }

            $sql = "SELECT * FROM conductores
                    WHERE id = :id";

            $consulta = $this->conexion->prepare($sql);

            $consulta->bindParam(
                ':id',
                $id,
                PDO::PARAM_INT
            );

            $consulta->execute();

            $conductor = $consulta->fetch(
                PDO::FETCH_ASSOC
            );

            if (!$conductor) {
                throw new Exception(
                    "Conductor no encontrado."
                );
            }

            return $conductor;

        } catch (Exception $e) {

            echo "Error: " . $e->getMessage();

            return null;
        }
    }
}