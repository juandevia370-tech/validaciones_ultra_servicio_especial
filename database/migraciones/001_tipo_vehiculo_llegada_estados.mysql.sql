-- Migración 001 (MySQL / MariaDB 10.4+ de XAMPP)
-- 1. Tipo de vehículo en "vehiculos".
-- 2. Fecha y hora de llegada en "servicios" (fecha_servicio/hora_servicio = salida).
-- 3. Estados de servicio limitados a: Pendiente, En proceso, Finalizado.
-- No elimina tablas ni registros. Haz un respaldo antes de ejecutarla.

SET NAMES utf8mb4;

ALTER TABLE vehiculos ADD COLUMN IF NOT EXISTS tipo VARCHAR(30) NULL AFTER placa;

UPDATE vehiculos
SET tipo = CASE
    WHEN LOWER(modelo) IN ('duster', 'tracker') THEN 'Camioneta'
    WHEN LOWER(modelo) IN ('hiace', 'carnival', 'sprinter') THEN 'Van'
    WHEN LOWER(modelo) LIKE '%lo 915%' OR LOWER(modelo) LIKE '%microb%' THEN 'Microbús'
    WHEN LOWER(modelo) LIKE '%buseta%' THEN 'Buseta'
    ELSE 'Automóvil'
END
WHERE tipo IS NULL OR tipo = '';

ALTER TABLE vehiculos MODIFY tipo VARCHAR(30) NOT NULL;
ALTER TABLE vehiculos DROP CONSTRAINT IF EXISTS chk_vehiculos_tipo;
ALTER TABLE vehiculos ADD CONSTRAINT chk_vehiculos_tipo
    CHECK (tipo IN ('Automóvil', 'Camioneta', 'Van', 'Buseta', 'Microbús', 'Bus'));

ALTER TABLE servicios ADD COLUMN IF NOT EXISTS fecha_llegada DATE NULL AFTER hora_servicio;
ALTER TABLE servicios ADD COLUMN IF NOT EXISTS hora_llegada TIME NULL AFTER fecha_llegada;

UPDATE servicios
SET estado = CASE
    WHEN LOWER(TRIM(estado)) LIKE 'final%' THEN 'Finalizado'
    WHEN LOWER(TRIM(estado)) LIKE '%proceso%' OR LOWER(TRIM(estado)) LIKE '%curso%' THEN 'En proceso'
    ELSE 'Pendiente'
END
WHERE estado NOT IN ('Pendiente', 'En proceso', 'Finalizado');

ALTER TABLE servicios DROP CONSTRAINT IF EXISTS chk_servicios_estado;
ALTER TABLE servicios ADD CONSTRAINT chk_servicios_estado
    CHECK (estado IN ('Pendiente', 'En proceso', 'Finalizado'));

ALTER TABLE servicios DROP CONSTRAINT IF EXISTS chk_servicios_llegada;
ALTER TABLE servicios ADD CONSTRAINT chk_servicios_llegada
    CHECK (fecha_llegada IS NULL OR fecha_llegada >= fecha_servicio);
