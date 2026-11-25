<?php

/**
 * Lee la configuración desde las variables de entorno del servidor o, en
 * desarrollo local, desde el archivo .env de la raíz del proyecto.
 * Las variables del servidor tienen prioridad sobre .env.
 */
if (!function_exists('app_env')) {
    function app_env($key, $default = '')
    {
        static $fileConfig = null;

        if ($fileConfig === null) {
            $fileConfig = [];
            $envPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';

            if (is_file($envPath)) {
                $parsedConfig = parse_ini_file($envPath, false, INI_SCANNER_RAW);
                if ($parsedConfig === false) {
                    error_log('No se pudo leer el archivo de configuración .env.');
                    http_response_code(500);
                    exit('La configuración del servidor no es válida.');
                }

                $fileConfig = $parsedConfig;
            }
        }

        // Algunos entornos (por ejemplo, funciones serverless) exponen las
        // variables solo en $_ENV o $_SERVER y no mediante getenv().
        foreach ([getenv($key), $_ENV[$key] ?? false, $_SERVER[$key] ?? false] as $environmentValue) {
            if (is_string($environmentValue) && $environmentValue !== '') {
                return $environmentValue;
            }
        }

        if (isset($fileConfig[$key]) && $fileConfig[$key] !== '') {
            return (string) $fileConfig[$key];
        }

        return $default;
    }
}
