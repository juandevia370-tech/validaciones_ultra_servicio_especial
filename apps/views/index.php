<?php
$escapar = static function ($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
};
$tablas = [
    [
        'titulo' => 'Clientes',
        'registros' => $clientes,
        'columnas' => ['ID' => 'id', 'Nombre' => 'nombre', 'Documento' => 'documento', 'Correo' => 'correo', 'Teléfono' => 'telefono']
    ],
    [
        'titulo' => 'Conductores',
        'registros' => $conductores,
        'columnas' => ['ID' => 'id', 'Nombre' => 'nombre', 'Documento' => 'documento', 'Licencia' => 'licencia', 'Teléfono' => 'telefono']
    ],
    [
        'titulo' => 'Pasajeros',
        'registros' => $pasajeros,
        'columnas' => ['ID' => 'id', 'Nombre' => 'nombre', 'Documento' => 'documento', 'Teléfono' => 'telefono', 'Cliente' => 'cliente']
    ],
    [
        'titulo' => 'Servicios',
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
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tabla['registros'] as $registro): ?>
                    <tr>
                        <?php foreach ($tabla['columnas'] as $campo): ?>
                            <td><?= $escapar($registro[$campo] ?? '') ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</body>
</html>



