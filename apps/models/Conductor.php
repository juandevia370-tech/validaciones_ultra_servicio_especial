<?php

class Conductor
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
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