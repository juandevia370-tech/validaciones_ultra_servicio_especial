-- Migración 2026-11-27b (MySQL / MariaDB de XAMPP)
-- Vehículos: tipo de vehículo. Servicios: hora de llegada
-- (hora_servicio pasa a mostrarse como "Hora de salida").
-- No elimina tablas ni registros. Se puede ejecutar más de una vez.

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

ALTER TABLE servicios ADD COLUMN IF NOT EXISTS hora_llegada TIME NULL AFTER hora_servicio;
