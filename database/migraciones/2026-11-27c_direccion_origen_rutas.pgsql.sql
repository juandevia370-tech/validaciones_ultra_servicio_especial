-- Migración 2026-11-27c (PostgreSQL / Supabase). Ejecutar en Supabase > SQL Editor.
-- Rutas (destinos): dirección de origen, para precargarla en los servicios.
-- No elimina tablas ni registros. Se puede ejecutar más de una vez.

ALTER TABLE transporte.destinos
    ADD COLUMN IF NOT EXISTS direccion_origen varchar(150);

-- Verificación
SELECT id, ciudad_origen, direccion_origen, ciudad_destino, direccion_destino
FROM transporte.destinos
ORDER BY id;
