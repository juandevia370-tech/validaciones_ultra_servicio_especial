<?php

require_once __DIR__ . '/env.php';

$appEnvironment = app_env('APP_ENV', 'local');
if (!in_array($appEnvironment, ['local', 'production'], true)) {
    error_log('El valor de APP_ENV no es válido.');
    http_response_code(500);
    exit('La configuración del servidor no es válida.');
}

$isProduction = $appEnvironment === 'production';

// mysql: XAMPP / MariaDB local. pgsql: PostgreSQL de Supabase en producción.
$driver = app_env('DB_DRIVER', 'mysql');
if (!in_array($driver, ['mysql', 'pgsql'], true)) {
    error_log('El valor de DB_DRIVER no es válido.');
    http_response_code(500);
    exit('La configuración de la base de datos no es válida.');
}

$host = app_env('DB_HOST', $isProduction ? '' : 'localhost');
$port = app_env('DB_PORT', $driver === 'pgsql' ? '5432' : '3306');
$dbname = app_env('DB_NAME', $isProduction ? '' : 'sistema_transporte');
$usuario = app_env('DB_USER', $isProduction ? '' : 'root');
$password = app_env('DB_PASSWORD');
$schema = app_env('DB_SCHEMA', 'transporte');
$sslMode = app_env('DB_SSLMODE', $isProduction ? 'require' : 'prefer');

if (
    $host === ''
    || $dbname === ''
    || $usuario === ''
    || !ctype_digit((string) $port)
    || (int) $port < 1
    || (int) $port > 65535
    || !preg_match('/^[a-z_][a-z0-9_]{0,62}$/', $schema)
    || !in_array($sslMode, ['disable', 'allow', 'prefer', 'require', 'verify-ca', 'verify-full'], true)
) {
    error_log('La configuración de la base de datos no está completa o no es válida.');
    http_response_code(500);
    exit('La configuración de la base de datos no es válida.');
}

try {
    if ($driver === 'pgsql') {
        $conexion = new PDO(
            "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=$sslMode",
            $usuario,
            $password,
            [PDO::ATTR_TIMEOUT => 10]
        );
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Las tablas viven en un esquema privado que la API pública de Supabase no expone.
        $conexion->exec('SET search_path TO ' . $schema);
    } else {
        $conexion = new PDO(
            "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
            $usuario,
            $password
        );
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
} catch (PDOException $e) {
    error_log('Error de conexión a la base de datos: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Revisa la configuración del servidor.');
}
