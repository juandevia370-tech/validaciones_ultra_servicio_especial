-- Importa este archivo después de seleccionar la base de datos destino.
-- No contiene usuarios, contraseñas ni datos personales.

CREATE TABLE IF NOT EXISTS clientes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(120) NOT NULL,
    documento VARCHAR(40) NOT NULL,
    correo VARCHAR(254) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conductores (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(120) NOT NULL,
    documento VARCHAR(40) NOT NULL,
    licencia VARCHAR(50) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS destinos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ciudad_origen VARCHAR(120) NOT NULL,
    ciudad_destino VARCHAR(120) NOT NULL,
    direccion_destino VARCHAR(255) NULL,
    tarifa DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pasajeros (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(120) NOT NULL,
    documento VARCHAR(40) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    cliente_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY idx_pasajeros_cliente_id (cliente_id),
    CONSTRAINT fk_pasajeros_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vehiculos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    placa VARCHAR(20) NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    marca VARCHAR(80) NOT NULL,
    modelo VARCHAR(80) NOT NULL,
    capacidad INT UNSIGNED NOT NULL,
    conductor_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY idx_vehiculos_conductor_id (conductor_id),
    CONSTRAINT chk_vehiculos_tipo CHECK (tipo IN ('Automóvil', 'Camioneta', 'Van', 'Buseta', 'Microbús', 'Bus')),
    CONSTRAINT fk_vehiculos_conductor
        FOREIGN KEY (conductor_id) REFERENCES conductores (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS servicios (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    cliente_id INT UNSIGNED NOT NULL,
    pasajero_id INT UNSIGNED NOT NULL,
    conductor_id INT UNSIGNED NOT NULL,
    vehiculo_id INT UNSIGNED NOT NULL,
    destino_id INT UNSIGNED NOT NULL,
    fecha_servicio DATE NOT NULL,
    hora_servicio TIME NOT NULL,
    fecha_llegada DATE NULL,
    hora_llegada TIME NULL,
    estado VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_servicios_cliente_id (cliente_id),
    KEY idx_servicios_pasajero_id (pasajero_id),
    KEY idx_servicios_conductor_id (conductor_id),
    KEY idx_servicios_vehiculo_id (vehiculo_id),
    KEY idx_servicios_destino_id (destino_id),
    CONSTRAINT chk_servicios_estado CHECK (estado IN ('Pendiente', 'En proceso', 'Finalizado')),
    CONSTRAINT chk_servicios_llegada CHECK (fecha_llegada IS NULL OR fecha_llegada >= fecha_servicio),
    CONSTRAINT fk_servicios_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_servicios_pasajero
        FOREIGN KEY (pasajero_id) REFERENCES pasajeros (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_servicios_conductor
        FOREIGN KEY (conductor_id) REFERENCES conductores (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_servicios_vehiculo
        FOREIGN KEY (vehiculo_id) REFERENCES vehiculos (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_servicios_destino
        FOREIGN KEY (destino_id) REFERENCES destinos (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
