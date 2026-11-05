<?php

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$publicDirectory = realpath(__DIR__);
$file = realpath(
    $publicDirectory
    . DIRECTORY_SEPARATOR
    . ltrim(str_replace('/', DIRECTORY_SEPARATOR, (string) $path), DIRECTORY_SEPARATOR)
);

if (
    $path !== '/'
    && $file !== false
    && strpos($file, $publicDirectory . DIRECTORY_SEPARATOR) === 0
    && is_file($file)
) {
    return false;
}

require __DIR__ . '/index.php';
