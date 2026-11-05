<?php

class Destino
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function guardar($ciudadOrigen, $ciudadDestino, $direccionDestino, $tarifa)
    {
        $ciudadOrigen = trim($ciudadOrigen);
        $ciudadDestino = trim($ciudadDestino);
        $direccionDestino = trim($direccionDestino);
        $tarifa = filter_var($tarifa, FILTER_VALIDATE_FLOAT);

        if ($ciudadOrigen === '' || $ciudadDestino === '' || $tarifa === false || $tarifa <= 0) {
            return false;
        }

        try {
            $consulta = $this->conexion->prepare(
                'INSERT INTO destinos (ciudad_origen, ciudad_destino, direccion_destino, tarifa)
                 VALUES (:ciudad_origen, :ciudad_destino, :direccion_destino, :tarifa)'
            );

            return $consulta->execute([
                ':ciudad_origen' => $ciudadOrigen,
                ':ciudad_destino' => $ciudadDestino,
                ':direccion_destino' => $direccionDestino !== '' ? $direccionDestino : null,
                ':tarifa' => $tarifa,
            ]);
        } catch (PDOException $e) {
            error_log('Error al guardar el destino: ' . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $ciudadOrigen, $ciudadDestino, $direccionDestino, $tarifa)
    {
        $ciudadOrigen = trim($ciudadOrigen);
        $ciudadDestino = trim($ciudadDestino);
        $direccionDestino = trim($direccionDestino);
        $tarifa = filter_var($tarifa, FILTER_VALIDATE_FLOAT);

        if ($ciudadOrigen === '' || $ciudadDestino === '' || $tarifa === false || $tarifa <= 0) {
            return false;
        }

        try {
            $consulta = $this->conexion->prepare(
                'UPDATE destinos
                 SET ciudad_origen = :ciudad_origen, ciudad_destino = :ciudad_destino,
                     direccion_destino = :direccion_destino, tarifa = :tarifa
                 WHERE id = :id'
            );

            return $consulta->execute([
                ':ciudad_origen' => $ciudadOrigen,
                ':ciudad_destino' => $ciudadDestino,
                ':direccion_destino' => $direccionDestino !== '' ? $direccionDestino : null,
                ':tarifa' => $tarifa,
                ':id' => (int) $id,
            ]);
        } catch (PDOException $e) {
            error_log('Error al actualizar el destino: ' . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id)
    {
        try {
            $consulta = $this->conexion->prepare(
                'DELETE FROM destinos WHERE id = :id'
            );
            $consulta->execute([':id' => (int) $id]);

            return $consulta->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Error al eliminar el destino: ' . $e->getMessage());
            return false;
        }
    }

    public function obtenerTodos()
    {
        try {
            $consulta = $this->conexion->query(
                'SELECT * FROM destinos ORDER BY id DESC'
            );

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error al obtener los destinos: ' . $e->getMessage());
            return [];
        }
    }

    public function getById($id)
    {
        try {
            $consulta = $this->conexion->prepare(
                'SELECT * FROM destinos WHERE id = :id'
            );
            $consulta->execute([':id' => (int) $id]);
            $destino = $consulta->fetch(PDO::FETCH_ASSOC);

            return $destino ?: null;
        } catch (PDOException $e) {
            error_log('Error al consultar el destino: ' . $e->getMessage());
            return null;
        }
    }
}
