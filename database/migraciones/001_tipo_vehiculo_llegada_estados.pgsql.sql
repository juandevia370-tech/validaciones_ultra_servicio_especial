-- Migración 001 (PostgreSQL / Supabase). Ejecutar en Supabase > SQL Editor.
-- 1. Tipo de vehículo en "vehiculos".
-- 2. Fecha y hora de llegada en "servicios" (fecha_servicio/hora_servicio = salida).
-- 3. Estados de servicio limitados a: Pendiente, En proceso, Finalizado.
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

ALTER TABLE transporte.servicios ADD COLUMN IF NOT EXISTS fecha_llegada date;
ALTER TABLE transporte.servicios ADD COLUMN IF NOT EXISTS hora_llegada time;

UPDATE transporte.servicios
SET estado = CASE
    WHEN LOWER(TRIM(estado)) LIKE 'final%' THEN 'Finalizado'
    WHEN LOWER(TRIM(estado)) LIKE '%proceso%' OR LOWER(TRIM(estado)) LIKE '%curso%' THEN 'En proceso'
    ELSE 'Pendiente'
END
WHERE estado NOT IN ('Pendiente', 'En proceso', 'Finalizado');

ALTER TABLE transporte.servicios DROP CONSTRAINT IF EXISTS chk_servicios_estado;
ALTER TABLE transporte.servicios ADD CONSTRAINT chk_servicios_estado
    CHECK (estado IN ('Pendiente', 'En proceso', 'Finalizado'));

ALTER TABLE transporte.servicios DROP CONSTRAINT IF EXISTS chk_servicios_llegada;
ALTER TABLE transporte.servicios ADD CONSTRAINT chk_servicios_llegada
    CHECK (fecha_llegada IS NULL OR fecha_llegada >= fecha_servicio);

COMMIT;

-- Verificación
SELECT 'vehículos por tipo' AS detalle, tipo AS valor, COUNT(*) AS cantidad
FROM transporte.vehiculos GROUP BY tipo
UNION ALL
SELECT 'servicios por estado', estado, COUNT(*)
FROM transporte.servicios GROUP BY estado
ORDER BY 1, 2;
