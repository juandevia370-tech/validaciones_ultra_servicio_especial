-- Migración 2026-11-27b (PostgreSQL / Supabase). Ejecutar en Supabase > SQL Editor.
-- Vehículos: tipo de vehículo. Servicios: hora de llegada
-- (hora_servicio pasa a mostrarse como "Hora de salida").
-- No elimina tablas ni registros. Corre en una transacción y se puede repetir.

BEGIN;

ALTER TABLE transporte.vehiculos ADD COLUMN IF NOT EXISTS tipo varchar(30);

UPDATE transporte.vehiculos
SET tipo = CASE
    WHEN LOWER(modelo) IN ('duster', 'tracker') THEN 'Camioneta'
    WHEN LOWER(modelo) IN ('hiace', 'carnival', 'sprinter') THEN 'Van'
    WHEN LOWER(modelo) LIKE '%lo 915%' OR LOWER(modelo) LIKE '%microb%' THEN 'Microbús'
    WHEN LOWER(modelo) LIKE '%buseta%' THEN 'Buseta'
    ELSE 'Automóvil'
END
WHERE tipo IS NULL OR tipo = '';

ALTER TABLE transporte.vehiculos ALTER COLUMN tipo SET NOT NULL;
ALTER TABLE transporte.vehiculos DROP CONSTRAINT IF EXISTS chk_vehiculos_tipo;
ALTER TABLE transporte.vehiculos ADD CONSTRAINT chk_vehiculos_tipo
    CHECK (tipo IN ('Automóvil', 'Camioneta', 'Van', 'Buseta', 'Microbús', 'Bus'));

ALTER TABLE transporte.servicios ADD COLUMN IF NOT EXISTS hora_llegada time;

COMMIT;

-- Verificación
SELECT placa, tipo FROM transporte.vehiculos ORDER BY id;
