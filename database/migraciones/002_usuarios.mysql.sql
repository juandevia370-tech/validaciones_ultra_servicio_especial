-- Migración 002 (MySQL / MariaDB de XAMPP)
-- Tabla de usuarios para la autenticación propia del sistema.
-- Las contraseñas se guardan solo como hash bcrypt (password_hash de PHP).
-- Para crear usuarios: php database/crear_usuario.php

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(254) NOT NULL,
    contrasena_hash VARCHAR(255) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    intentos_fallidos INT(11) NOT NULL DEFAULT 0,
    bloqueado_hasta DATETIME NULL,
    ultimo_acceso DATETIME NULL,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY usuarios_correo_key (correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
