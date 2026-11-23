<?php
$contenido = file_get_contents(__DIR__ . "/productos.json");

if ($contenido === false) {
    http_response_code(500);
    exit("No se pudo leer el archivo productos.json.");
}

$productos = json_decode($contenido, true);

if (!is_array($productos)) {
    http_response_code(500);
    exit("No se pudo convertir productos.json en un arreglo PHP.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
</head>
<body>
    <h1>Productos y precios</h1>
    <ul>
        <?php foreach ($productos as $producto): ?>
            <li>
                <?= htmlspecialchars($producto["nombre"], ENT_QUOTES, "UTF-8") ?>:
                $<?= number_format($producto["precio"], 0, ",", ".") ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Productos con precio superior a $100.000</h2>
    <ul>
        <?php foreach ($productos as $producto): ?>
            <?php if ($producto["precio"] > 100000): ?>
                <li>
                    <?= htmlspecialchars($producto["nombre"], ENT_QUOTES, "UTF-8") ?>:
                    $<?= number_format($producto["precio"], 0, ",", ".") ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</body>
</html>
