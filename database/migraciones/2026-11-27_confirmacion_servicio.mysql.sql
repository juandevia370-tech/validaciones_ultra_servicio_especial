-- Migración 2026-11-27 (MySQL / MariaDB de XAMPP)
-- Servicios: campos de la confirmación que se envía al conductor.
-- Conductores: observaciones de la empresa.
-- No elimina tablas ni registros. Se puede ejecutar más de una vez.

SET NAMES utf8mb4;

ALTER TABLE servicios
    ADD COLUMN IF NOT EXISTS telefono_contacto VARCHAR(20) NULL AFTER destino_id,
    ADD COLUMN IF NOT EXISTS ciudad_origen VARCHAR(100) NULL AFTER telefono_contacto,
    ADD COLUMN IF NOT EXISTS direccion_origen VARCHAR(150) NULL AFTER ciudad_origen,
    ADD COLUMN IF NOT EXISTS ciudad_destino VARCHAR(100) NULL AFTER direccion_origen,
    ADD COLUMN IF NOT EXISTS direccion_destino VARCHAR(150) NULL AFTER ciudad_destino,
    ADD COLUMN IF NOT EXISTS ticket VARCHAR(30) NULL AFTER hora_servicio,
    ADD COLUMN IF NOT EXISTS flete DECIMAL(12, 2) NULL AFTER ticket,
    ADD COLUMN IF NOT EXISTS observaciones TEXT NULL AFTER estado;

-- Completa los servicios existentes con los datos de su ruta y su pasajero.
UPDATE servicios s
JOIN destinos d ON d.id = s.destino_id
SET s.ciudad_origen = COALESCE(s.ciudad_origen, d.ciudad_origen),
    s.ciudad_destino = COALESCE(s.ciudad_destino, d.ciudad_destino),
    s.direccion_destino = COALESCE(s.direccion_destino, d.direccion_destino),
    s.flete = COALESCE(s.flete, d.tarifa);

UPDATE servicios s
JOIN pasajeros p ON p.id = s.pasajero_id
SET s.telefono_contacto = COALESCE(s.telefono_contacto, p.telefono);

ALTER TABLE conductores
    ADD COLUMN IF NOT EXISTS observaciones TEXT NULL AFTER telefono;
