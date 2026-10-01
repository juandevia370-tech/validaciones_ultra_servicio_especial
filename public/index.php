<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../apps/controllers/ClienteController.php';
require_once __DIR__ . '/../apps/controllers/ConductorController.php';
require_once __DIR__ . '/../apps/controllers/PasajeroController.php';
require_once __DIR__ . '/../apps/controllers/ServicioControoller.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($method === 'GET' && $uri === '/') {
    ?>
    <a href="/cliente/crear">Formulario de Cliente</a>
    <a href="/conductor/crear">Formulario de Conductor</a>
    <a href="/pasajero/crear">Formulario de Pasajero</a>
    <a href="/servicio/crear">Formulario de Servicio</a>
    <?php
    exit;
}

if ($uri === '/cliente/crear') {
    $controlador = new ClienteController($conexion);
} elseif ($uri === '/conductor/crear') {
    $controlador = new ConductorController($conexion);
} elseif ($uri === '/pasajero/crear') {
    $controlador = new PasajeroController($conexion);
} elseif ($uri === '/servicio/crear') {
    $controlador = new ServicioController($conexion);
} else {
    http_response_code(404);
    exit('Página no encontrada.');
}

if ($method === 'GET') {
    $controlador->crear();
} elseif ($method === 'POST') {
    $controlador->resultado($controlador->guardar());
} else {
    http_response_code(405);
}


