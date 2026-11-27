<?php

class Vehiculo
{
    public const TIPOS = ['Automóvil', 'Camioneta', 'Van', 'Buseta', 'Microbús', 'Bus'];

    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function guardar($placa, $tipo, $marca, $modelo, $capacidad, $conductorId)
    {
        $placa = strtoupper(trim($placa));
        $tipo = trim($tipo);
        $marca = trim($marca);
        $modelo = trim($modelo);
        $capacidad = filter_var($capacidad, FILTER_VALIDATE_INT);
        $conductorId = filter_var($conductorId, FILTER_VALIDATE_INT);

        if (
            $placa === '' || $marca === '' || $modelo === ''
            || !in_array($tipo, self::TIPOS, true)
            || $capacidad === false || $capacidad < 1
            || $conductorId === false || $conductorId < 1
        ) {
            return false;
        }

        try {
            $consulta = $this->conexion->prepare(
                'INSERT INTO vehiculos (placa, tipo, marca, modelo, capacidad, conductor_id)
                 VALUES (:placa, :tipo, :marca, :modelo, :capacidad, :conductor_id)'
            );

            return $consulta->execute([
                ':placa' => $placa,
                ':tipo' => $tipo,
                ':marca' => $marca,
                ':modelo' => $modelo,
                ':capacidad' => $capacidad,
                ':conductor_id' => $conductorId,
            ]);
        } catch (PDOException $e) {
            error_log('Error al guardar el vehículo: ' . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $placa, $tipo, $marca, $modelo, $capacidad, $conductorId)
    {
        $placa = strtoupper(trim($placa));
        $tipo = trim($tipo);
        $marca = trim($marca);
        $modelo = trim($modelo);
        $capacidad = filter_var($capacidad, FILTER_VALIDATE_INT);
        $conductorId = filter_var($conductorId, FILTER_VALIDATE_INT);

        if (
            $placa === '' || $marca === '' || $modelo === ''
            || !in_array($tipo, self::TIPOS, true)
            || $capacidad === false || $capacidad < 1
            || $conductorId === false || $conductorId < 1
        ) {
            return false;
        }

        try {
            $consulta = $this->conexion->prepare(
                'UPDATE vehiculos
                 SET placa = :placa, tipo = :tipo, marca = :marca, modelo = :modelo,
                     capacidad = :capacidad, conductor_id = :conductor_id
                 WHERE id = :id'
            );

            return $consulta->execute([
                ':placa' => $placa,
                ':tipo' => $tipo,
                ':marca' => $marca,
                ':modelo' => $modelo,
                ':capacidad' => $capacidad,
                ':conductor_id' => $conductorId,
                ':id' => (int) $id,
            ]);
        } catch (PDOException $e) {
            error_log('Error al actualizar el vehículo: ' . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id)
    {
        try {
            $consulta = $this->conexion->prepare(
                'DELETE FROM vehiculos WHERE id = :id'
            );
            $consulta->execute([':id' => (int) $id]);

            return $consulta->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Error al eliminar el vehículo: ' . $e->getMessage());
            return false;
        }
    }

    public function obtenerTodos()
    {
        try {
            $consulta = $this->conexion->query(
                'SELECT vehiculos.*, conductores.nombre AS conductor
                 FROM vehiculos
                 LEFT JOIN conductores ON vehiculos.conductor_id = conductores.id
                 ORDER BY vehiculos.id DESC'
            );

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error al obtener los vehículos: ' . $e->getMessage());
            return [];
        }
    }

    public function getById($id)
    {
        try {
            $consulta = $this->conexion->prepare(
                'SELECT * FROM vehiculos WHERE id = :id'
            );
            $consulta->execute([':id' => (int) $id]);
            $vehiculo = $consulta->fetch(PDO::FETCH_ASSOC);

            return $vehiculo ?: null;
        } catch (PDOException $e) {
            error_log('Error al consultar el vehículo: ' . $e->getMessage());
            return null;
        }
    }
}
