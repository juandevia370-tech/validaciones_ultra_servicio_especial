<?php

/**
 * Guarda las sesiones PHP en la base de datos (tabla "sesiones").
 *
 * Necesario en plataformas serverless como Vercel, donde cada petición puede
 * ejecutarse en una instancia distinta y los archivos de sesión en disco se
 * pierden. Se activa con la variable de entorno SESSION_DRIVER=database.
 */
class SesionBaseDatos implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface
{
    private $conexion;
    private $tiempoVida;

    public function __construct(PDO $conexion, $tiempoVida = 7200)
    {
        $this->conexion = $conexion;
        $this->tiempoVida = (int) $tiempoVida;
    }

    public function open($path, $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    #[\ReturnTypeWillChange]
    public function read($id)
    {
        try {
            $consulta = $this->conexion->prepare(
                'SELECT datos FROM sesiones WHERE id = :id AND expira > NOW()'
            );
            $consulta->execute([':id' => $id]);
            $datos = $consulta->fetchColumn();

            return $datos === false ? '' : (string) $datos;
        } catch (PDOException $e) {
            error_log('Error al leer la sesión: ' . $e->getMessage());
            return '';
        }
    }

    public function write($id, $data): bool
    {
        try {
            $consulta = $this->conexion->prepare(
                "INSERT INTO sesiones (id, datos, expira)
                 VALUES (:id, :datos, NOW() + CAST(:vida AS integer) * INTERVAL '1 second')
                 ON CONFLICT (id) DO UPDATE
                 SET datos = EXCLUDED.datos, expira = EXCLUDED.expira"
            );

            return $consulta->execute([
                ':id' => $id,
                ':datos' => $data,
                ':vida' => $this->tiempoVida,
            ]);
        } catch (PDOException $e) {
            error_log('Error al guardar la sesión: ' . $e->getMessage());
            return false;
        }
    }

    public function destroy($id): bool
    {
        try {
            return $this->conexion->prepare('DELETE FROM sesiones WHERE id = :id')
                ->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Error al eliminar la sesión: ' . $e->getMessage());
            return false;
        }
    }

    #[\ReturnTypeWillChange]
    public function gc($maxLifetime)
    {
        try {
            $consulta = $this->conexion->query('DELETE FROM sesiones WHERE expira <= NOW()');

            return $consulta->rowCount();
        } catch (PDOException $e) {
            error_log('Error al limpiar sesiones: ' . $e->getMessage());
            return false;
        }
    }

    public function validateId($id): bool
    {
        try {
            $consulta = $this->conexion->prepare(
                'SELECT 1 FROM sesiones WHERE id = :id AND expira > NOW()'
            );
            $consulta->execute([':id' => $id]);

            return $consulta->fetchColumn() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateTimestamp($id, $data): bool
    {
        try {
            return $this->conexion->prepare(
                "UPDATE sesiones SET expira = NOW() + CAST(:vida AS integer) * INTERVAL '1 second' WHERE id = :id"
            )->execute([':id' => $id, ':vida' => $this->tiempoVida]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
