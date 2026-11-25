<?php

/**
 * Punto de entrada para Vercel (runtime vercel-php).
 * Vercel ejecuta las funciones desde /api; aquí se simula que el script es
 * public/index.php en la raíz del dominio para que el enrutador genere las
 * mismas URL que en XAMPP o Docker (/login, /cliente/crear, ...).
 */
$publicDirectory = realpath(__DIR__ . '/../public');
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $publicDirectory . '/index.php';
$_SERVER['DOCUMENT_ROOT'] = $publicDirectory;

require $publicDirectory . '/index.php';
