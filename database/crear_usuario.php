<?php

/**
 * Crea un usuario del sistema desde la consola.
 *
 *   php database/crear_usuario.php
 *       Crea el usuario en la base configurada en .env (por ejemplo, XAMPP).
 *
 *   php database/crear_usuario.php --sql
 *       No toca ninguna base: imprime una instrucción SQL (MySQL) con la
 *       contraseña ya cifrada, para ejecutarla en la pestaña SQL de phpMyAdmin
 *       de la base de producción. Si el correo ya existe, actualiza su nombre
 *       y su contraseña.
 *
 * La contraseña se escribe oculta (en Windows mediante PowerShell) y nunca se
 * guarda en texto plano: solo su hash bcrypt.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../apps/models/Usuario.php';

$soloSql = in_array('--sql', $argv, true);

$preguntar = static function ($texto) {
    fwrite(STDOUT, $texto);
    return trim((string) fgets(STDIN));
};

$preguntarOculto = static function ($texto) {
    if (DIRECTORY_SEPARATOR === '\\') {
        $comando = 'powershell -NoProfile -Command "$p = Read-Host -AsSecureString \''
            . str_replace("'", "''", rtrim($texto, ': ')) . '\'; '
            . '[Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($p))"';
        $valor = shell_exec($comando);
        if (is_string($valor)) {
            return rtrim($valor, "\r\n");
        }
    } elseif (trim((string) shell_exec('command -v stty')) !== '') {
        fwrite(STDOUT, $texto);
        shell_exec('stty -echo');
        $valor = rtrim((string) fgets(STDIN), "\r\n");
        shell_exec('stty echo');
        fwrite(STDOUT, PHP_EOL);
        return $valor;
    }

    fwrite(STDOUT, '(La contraseña será visible) ' . $texto);
    return rtrim((string) fgets(STDIN), "\r\n");
};

fwrite(STDOUT, "=== Crear usuario del sistema de transporte ===\n");
$nombre = $preguntar('Nombre completo: ');
$correo = strtolower($preguntar('Correo electrónico: '));
$contrasena = $preguntarOculto('Contraseña: ');
$confirmacion = $preguntarOculto('Confirmar contraseña: ');

try {
    if ($nombre === '' || mb_strlen($nombre) > 100) {
        throw new InvalidArgumentException('El nombre es obligatorio (máximo 100 caracteres).');
    }
    if (strlen($correo) > 254 || filter_var($correo, FILTER_VALIDATE_EMAIL) === false) {
        throw new InvalidArgumentException('El correo electrónico no es válido.');
    }
    Usuario::validarContrasena($contrasena, $confirmacion);
} catch (InvalidArgumentException $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

if ($soloSql) {
    $literal = static function ($valor) {
        return "'" . str_replace(['\\', "'"], ['\\\\', "''"], $valor) . "'";
    };
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);
    fwrite(STDOUT, PHP_EOL . "-- Copia y ejecuta esto en phpMyAdmin (pestaña SQL) de la base de producción:" . PHP_EOL);
    fwrite(STDOUT, 'INSERT INTO usuarios (nombre, correo, contrasena_hash, activo, creado_en) VALUES ('
        . $literal($nombre) . ', ' . $literal($correo) . ', ' . $literal($hash) . ', 1, UTC_TIMESTAMP())'
        . ' ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), contrasena_hash = VALUES(contrasena_hash),'
        . ' activo = 1, intentos_fallidos = 0, bloqueado_hasta = NULL;' . PHP_EOL);
    exit(0);
}

require __DIR__ . '/../config/database.php';

try {
    $creado = (new Usuario($conexion))->crear($nombre, $correo, $contrasena, $confirmacion);
} catch (InvalidArgumentException $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

if (!$creado) {
    fwrite(STDERR, "No se pudo crear el usuario. Revisa que exista la tabla usuarios (migración 002).\n");
    exit(1);
}

fwrite(STDOUT, "Usuario creado: $correo\n");
