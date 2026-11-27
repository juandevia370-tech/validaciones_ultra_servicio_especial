<?php

/**
 * Genera un script SQL para PostgreSQL (Supabase) con el esquema y TODOS los
 * registros de la base MySQL/MariaDB local, conservando los ID originales.
 *
 * Uso (desde la raíz del proyecto, con XAMPP/MySQL encendido):
 *   php database/exportar_a_supabase.php
 *
 * El archivo resultante queda en database/backups/ (excluido de Git porque
 * contiene datos personales). Se ejecuta en Supabase > SQL Editor. Todo corre
 * dentro de una transacción: si algo falla, no se guarda nada.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../config/database.php';

if ($conexion->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'mysql') {
    fwrite(STDERR, "Este script debe leer la base MySQL local (DB_DRIVER=mysql).\n");
    exit(1);
}

// Orden que respeta las claves foráneas.
$tablas = [
    'clientes' => ['id', 'nombre', 'documento', 'correo', 'telefono'],
    'conductores' => ['id', 'nombre', 'documento', 'licencia', 'telefono', 'observaciones'],
    'destinos' => ['id', 'ciudad_origen', 'direccion_origen', 'ciudad_destino', 'direccion_destino', 'tarifa'],
    'pasajeros' => ['id', 'nombre', 'documento', 'telefono', 'cliente_id'],
    'vehiculos' => ['id', 'placa', 'tipo', 'marca', 'modelo', 'capacidad', 'conductor_id'],
    'servicios' => ['id', 'cliente_id', 'pasajero_id', 'conductor_id', 'vehiculo_id', 'destino_id', 'telefono_contacto', 'ciudad_origen', 'direccion_origen', 'ciudad_destino', 'direccion_destino', 'fecha_servicio', 'hora_servicio', 'hora_llegada', 'ticket', 'flete', 'estado', 'observaciones'],
];

$literal = static function ($valor) {
    if ($valor === null) {
        return 'NULL';
    }

    return "'" . str_replace("'", "''", (string) $valor) . "'";
};

$salida = [];
$salida[] = '-- Migración MySQL -> Supabase Postgres generada el ' . date('Y-m-d H:i:s');
$salida[] = '-- Origen: ' . $conexion->query('SELECT DATABASE()')->fetchColumn()
    . ' (' . $conexion->getAttribute(PDO::ATTR_SERVER_VERSION) . ')';
$salida[] = 'BEGIN;';
$salida[] = '';
$salida[] = file_get_contents(__DIR__ . '/schema.pgsql.sql');

$conteos = [];
foreach ($tablas as $tabla => $columnas) {
    $filas = $conexion->query('SELECT ' . implode(', ', $columnas) . " FROM `$tabla` ORDER BY id")
        ->fetchAll(PDO::FETCH_ASSOC);
    $conteos[$tabla] = count($filas);

    $salida[] = "-- $tabla: " . count($filas) . ' registros';
    foreach ($filas as $fila) {
        $salida[] = "INSERT INTO transporte.$tabla (" . implode(', ', $columnas) . ') VALUES ('
            . implode(', ', array_map($literal, array_values($fila))) . ');';
    }

    // Conserva el siguiente AUTO_INCREMENT de MySQL para no reutilizar ID borrados.
    $consulta = $conexion->prepare(
        'SELECT AUTO_INCREMENT FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :tabla'
    );
    $consulta->execute([':tabla' => $tabla]);
    $siguiente = max((int) $consulta->fetchColumn(), 1);
    $salida[] = "SELECT setval(pg_get_serial_sequence('transporte.$tabla', 'id'), "
        . "GREATEST($siguiente, (SELECT COALESCE(MAX(id), 0) + 1 FROM transporte.$tabla)), false);";
    $salida[] = '';
}

$salida[] = 'COMMIT;';
$salida[] = '';
$salida[] = '-- Verificación: debe coincidir con los conteos de MySQL';
$salida[] = "SELECT 'clientes' AS tabla, COUNT(*) AS registros FROM transporte.clientes"
    . " UNION ALL SELECT 'conductores', COUNT(*) FROM transporte.conductores"
    . " UNION ALL SELECT 'destinos', COUNT(*) FROM transporte.destinos"
    . " UNION ALL SELECT 'pasajeros', COUNT(*) FROM transporte.pasajeros"
    . " UNION ALL SELECT 'vehiculos', COUNT(*) FROM transporte.vehiculos"
    . " UNION ALL SELECT 'servicios', COUNT(*) FROM transporte.servicios"
    . " UNION ALL SELECT 'servicios con todas sus relaciones', COUNT(*) FROM transporte.servicios s"
    . ' JOIN transporte.clientes c ON c.id = s.cliente_id'
    . ' JOIN transporte.pasajeros p ON p.id = s.pasajero_id'
    . ' JOIN transporte.conductores d ON d.id = s.conductor_id'
    . ' JOIN transporte.vehiculos v ON v.id = s.vehiculo_id'
    . ' JOIN transporte.destinos t ON t.id = s.destino_id;';
$salida[] = '';

$directorio = __DIR__ . '/backups';
if (!is_dir($directorio) && !mkdir($directorio, 0700, true)) {
    fwrite(STDERR, "No se pudo crear $directorio\n");
    exit(1);
}

$archivo = $directorio . '/migracion_supabase_' . date('Ymd_His') . '.sql';
file_put_contents($archivo, implode("\n", $salida));

echo "Archivo generado: $archivo\n";
foreach ($conteos as $tabla => $total) {
    echo str_pad($tabla, 12) . " $total registros\n";
}
