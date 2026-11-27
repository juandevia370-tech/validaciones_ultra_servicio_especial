-- Migración 2026-11-27 (PostgreSQL / Supabase). Ejecutar en Supabase > SQL Editor.
-- Servicios: campos de la confirmación que se envía al conductor.
-- Conductores: observaciones de la empresa.
-- No elimina tablas ni registros. Corre en una transacción y se puede repetir.

BEGIN;

ALTER TABLE transporte.servicios
    ADD COLUMN IF NOT EXISTS telefono_contacto varchar(20),
    ADD COLUMN IF NOT EXISTS ciudad_origen     varchar(100),
    ADD COLUMN IF NOT EXISTS direccion_origen  varchar(150),
    ADD COLUMN IF NOT EXISTS ciudad_destino    varchar(100),
    ADD COLUMN IF NOT EXISTS direccion_destino varchar(150),
    ADD COLUMN IF NOT EXISTS ticket            varchar(30),
    ADD COLUMN IF NOT EXISTS flete             numeric(12, 2),
    ADD COLUMN IF NOT EXISTS observaciones     text;

-- Completa los servicios existentes con los datos de su ruta y su pasajero.
UPDATE transporte.servicios s
SET ciudad_origen = COALESCE(s.ciudad_origen, d.ciudad_origen),
    ciudad_destino = COALESCE(s.ciudad_destino, d.ciudad_destino),
    direccion_destino = COALESCE(s.direccion_destino, d.direccion_destino),
    flete = COALESCE(s.flete, d.tarifa)
FROM transporte.destinos d
WHERE d.id = s.destino_id;

UPDATE transporte.servicios s
SET telefono_contacto = COALESCE(s.telefono_contacto, p.telefono)
FROM transporte.pasajeros p
WHERE p.id = s.pasajero_id;

ALTER TABLE transporte.conductores
    ADD COLUMN IF NOT EXISTS observaciones text;

COMMIT;

-- Verificación
SELECT id, ciudad_origen, ciudad_destino, flete, ticket
FROM transporte.servicios
ORDER BY id;
