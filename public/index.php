<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../apps/controllers/ClienteController.php';
require_once __DIR__ . '/../apps/controllers/ConductorController.php';
require_once __DIR__ . '/../apps/controllers/PasajeroController.php';
require_once __DIR__ . '/../apps/controllers/ServicioControoller.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($method === 'GET' && $uri === '/') {
    $clientes    = (new Cliente($conexion))->obtenerTodos();
    $conductores = (new Conductor($conexion))->obtenerTodos();
    $pasajeros   = (new Pasajero($conexion))->obtenerTodos();
    $servicios   = (new Servicio($conexion))->obtenerTodos();

    $mensajes = [
        'editado' => 'Registro actualizado correctamente.',
        'error'   => 'No se pudo actualizar el registro.',
    ];
    $mensaje = $mensajes[$_GET['msg'] ?? ''] ?? '';

    $enlacesFormularios =
        '<a href="/cliente/crear">Formulario de Cliente</a> '
      . '<a href="/conductor/crear">Formulario de Conductor</a> '
      . '<a href="/pasajero/crear">Formulario de Pasajero</a> '
      . '<a href="/servicio/crear">Formulario de Servicio</a>';

    require __DIR__ . '/../apps/views/index.php';
    exit;
}

if ($uri === '/cliente/editar') {
    $controlador = new ClienteController($conexion);

    if ($method === 'POST') {
        $controlador->actualizar();
    } else {
        $controlador->editar();
    }
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
    $controlador->guardar();   // ya no usa resultado()
    header('Location: /');     // vuelve al index con las tablas
    exit;
} else {
    http_response_code(405);
}


$escapar = static function ($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
};
$tablas = [
    [
        'titulo' => 'Clientes',
        'ruta' => 'cliente',
        'registros' => $clientes,
        'columnas' => ['ID' => 'id', 'Nombre' => 'nombre', 'Documento' => 'documento', 'Correo' => 'correo', 'Teléfono' => 'telefono']
    ],
    [
        'titulo' => 'Conductores',
        'ruta' => 'conductor',
        'registros' => $conductores,
        'columnas' => ['ID' => 'id', 'Nombre' => 'nombre', 'Documento' => 'documento', 'Licencia' => 'licencia', 'Teléfono' => 'telefono']
    ],
    [
        'titulo' => 'Pasajeros',
        'ruta' => 'pasajero',
        'registros' => $pasajeros,
        'columnas' => ['ID' => 'id', 'Nombre' => 'nombre', 'Documento' => 'documento', 'Teléfono' => 'telefono', 'Cliente' => 'cliente']
    ],
    [
        'titulo' => 'Servicios',
        'ruta' => 'servicio',
        'registros' => $servicios,
        'columnas' => ['ID' => 'id', 'Cliente' => 'cliente', 'Pasajero' => 'pasajero', 'Conductor' => 'conductor', 'Vehículo' => 'vehiculo', 'Destino' => 'destino', 'Fecha' => 'fecha_servicio', 'Hora' => 'hora_servicio', 'Estado' => 'estado']
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de servicios</title>
</head>
<body>
    <?= $enlacesFormularios ?>
    <h1>Gestión de servicios especiales</h1>

    <?php if ($mensaje !== ''): ?>
        <p><?= $escapar($mensaje) ?></p>
    <?php endif; ?>

    <?php foreach ($tablas as $tabla): ?>
        <h2><?= $escapar($tabla['titulo']) ?></h2>
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <?php foreach ($tabla['columnas'] as $titulo => $campo): ?>
                        <th><?= $escapar($titulo) ?></th>
                    <?php endforeach; ?>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tabla['registros'] as $registro): ?>
                    <tr>
                        <?php foreach ($tabla['columnas'] as $campo): ?>
                            <td><?= $escapar($registro[$campo] ?? '') ?></td>
                        <?php endforeach; ?>
                        <td>
                            <?php if ($tabla['ruta'] === 'cliente'): ?>
                                <a href="/cliente/editar?id=<?= (int) $registro['id'] ?>">Editar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</body>
</html>