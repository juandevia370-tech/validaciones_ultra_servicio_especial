-- Migración 003 (MySQL / MariaDB)
-- Tabla para guardar las sesiones PHP en la base de datos.
-- Necesaria en Vercel (serverless, SESSION_DRIVER=database). En XAMPP no se usa,
-- pero no estorba. Las fechas se guardan en UTC.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS sesiones (
    id VARCHAR(128) NOT NULL,
    datos MEDIUMTEXT NOT NULL,
    expira DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_sesiones_expira (expira)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
