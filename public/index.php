<?php

declare(strict_types=1);

require_once __DIR__ . '/../apps/models/ServicioEspecial.php';

$servicio = new ServicioEspecial(
    'Atención preferencial',
    'Servicio especial para clientes que requieren atención prioritaria.',
    85000.0,
    60
);

$mensajesValidacion = [];

function probarValidacion(callable $setter, string $descripcion, array &$mensajesValidacion): void
{
    try {
        $setter();
        $mensajesValidacion[] = "Correcto: {$descripcion}";
    } catch (InvalidArgumentException $exception) {
        $mensajesValidacion[] = "Validación aplicada: {$exception->getMessage()}";
    }
}

// Prueba de setters con datos válidos.
$servicio->setNombre('Servicio premium de atención');
$servicio->setDescripcion('Atención prioritaria con seguimiento personalizado.');
$servicio->setPrecio(95000.0);
$servicio->setDuracionMinutos(90);

// Prueba de las validaciones con datos no permitidos.
probarValidacion(
    fn() => $servicio->setNombre(''),
    'el nombre fue aceptado',
    $mensajesValidacion
);
probarValidacion(
    fn() => $servicio->setDescripcion(''),
    'la descripción fue aceptada',
    $mensajesValidacion
);
probarValidacion(
    fn() => $servicio->setPrecio(0),
    'el precio fue aceptado',
    $mensajesValidacion
);
probarValidacion(
    fn() => $servicio->setDuracionMinutos(-10),
    'la duración fue aceptada',
    $mensajesValidacion
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validaciones del servicio especial</title>
</head>
<body>
    <h1>Servicio especial</h1>

    <h2>Información del objeto</h2>
    <ul>
        <li><strong>Nombre:</strong> <?= htmlspecialchars($servicio->getNombre(), ENT_QUOTES, 'UTF-8') ?></li>
        <li><strong>Descripción:</strong> <?= htmlspecialchars($servicio->getDescripcion(), ENT_QUOTES, 'UTF-8') ?></li>
        <li><strong>Precio:</strong> $<?= number_format($servicio->getPrecio(), 2, ',', '.') ?></li>
        <li><strong>Duración:</strong> <?= $servicio->getDuracionMinutos() ?> minutos</li>
    </ul>

    <h2>Resultado de las validaciones</h2>
    <ul>
        <?php foreach ($mensajesValidacion as $mensaje): ?>
            <li><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
