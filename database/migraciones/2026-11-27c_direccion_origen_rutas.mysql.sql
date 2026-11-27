-- Migración 2026-11-27c (MySQL / MariaDB de XAMPP)
-- Rutas (destinos): dirección de origen, para precargarla en los servicios.
-- No elimina tablas ni registros. Se puede ejecutar más de una vez.

SET NAMES utf8mb4;

ALTER TABLE destinos
    ADD COLUMN IF NOT EXISTS direccion_origen VARCHAR(150) NULL AFTER ciudad_origen;
