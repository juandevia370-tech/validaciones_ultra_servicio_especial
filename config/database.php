<?php

require_once __DIR__ . '/env.php';

// Conexión a MySQL / MariaDB: XAMPP en local y el servidor MySQL del hosting
// en producción. Los datos de conexión vienen de .env o de variables de entorno.

$appEnvironment = app_env('APP_ENV', 'local');
if (!in_array($appEnvironment, ['local', 'production'], true)) {
    error_log('El valor de APP_ENV no es válido.');
    http_response_code(500);
    exit('La configuración del servidor no es válida.');
}

$isProduction = $appEnvironment === 'production';
$host = app_env('DB_HOST', $isProduction ? '' : 'localhost');
$port = app_env('DB_PORT', '3306');
$dbname = app_env('DB_NAME', $isProduction ? '' : 'sistema_transporte');
$usuario = app_env('DB_USER', $isProduction ? '' : 'root');
$password = app_env('DB_PASSWORD');

if (
    $host === ''
    || $dbname === ''
    || $usuario === ''
    || !ctype_digit((string) $port)
    || (int) $port < 1
    || (int) $port > 65535
) {
    error_log('La configuración de la base de datos no está completa o no es válida.');
    http_response_code(500);
    exit('La configuración de la base de datos no es válida.');
}

// Conexión cifrada (TLS) cuando la base está en otro servidor, como en Vercel.
$opciones = [PDO::ATTR_TIMEOUT => 10];
if (filter_var(app_env('DB_SSL', 'false'), FILTER_VALIDATE_BOOLEAN)) {
    $certificadoCa = app_env('DB_SSL_CA', '');
    if ($certificadoCa === '') {
        foreach ([
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/ssl/cert.pem',
        ] as $ruta) {
            if (is_readable($ruta)) {
                $certificadoCa = $ruta;
                break;
            }
        }
    }
    if ($certificadoCa === '' || !is_readable($certificadoCa)) {
        error_log('DB_SSL está activo, pero no se encontró el certificado CA (DB_SSL_CA).');
        http_response_code(500);
        exit('La configuración de la base de datos no es válida.');
    }
    $opciones[PDO::MYSQL_ATTR_SSL_CA] = $certificadoCa;
    $opciones[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
}

try {
    $conexion = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $usuario,
        $password,
        $opciones
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Las fechas de control (accesos, bloqueos) se manejan en UTC.
    $conexion->exec("SET time_zone = '+00:00'");
} catch (PDOException $e) {
    error_log('Error de conexión a la base de datos: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Revisa la configuración del servidor.');
}
