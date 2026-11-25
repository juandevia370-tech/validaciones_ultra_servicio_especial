<?php
$productos = [
    ["id" => 1, "nombre" => "Mouse inalámbrico", "precio" => 35000, "stock" => 12, "disponible" => true],
    ["id" => 2, "nombre" => "Teclado mecánico", "precio" => 165000, "stock" => 8, "disponible" => true],
    ["id" => 3, "nombre" => "Monitor", "precio" => 780000, "stock" => 6, "disponible" => true]
];
$productos[] = ["id" => 4, "nombre" => "Cable USB-C", "precio" => 25000, "stock" => 30, "disponible" => true];
$jsonProductos = json_encode($productos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if ($jsonProductos === false) exit("No se pudo convertir el arreglo a JSON.");

$contenido = file_get_contents(__DIR__ . "/productos.json");
if ($contenido === false) exit("No se pudo leer productos.json.");
$productosJson = json_decode($contenido, true);
if (!is_array($productosJson)) exit("productos.json no contiene un arreglo válido.");

$productosActualizados = $productosJson;
$idBuscado = 3;
$nuevoPrecio = 80000;
$indiceProducto = null;
foreach ($productosActualizados as $indice => $producto) {
    if ($producto["id"] === $idBuscado) {
        $indiceProducto = $indice;
        $productosActualizados[$indice]["precio"] = $nuevoPrecio;
        break;
    }
}
if ($indiceProducto === null) exit("No se encontró el producto.");
$precioVerificado = $productosActualizados[$indiceProducto]["precio"] === $nuevoPrecio;
$jsonActualizado = json_encode($productosActualizados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if ($jsonActualizado === false) exit("No se pudo convertir el arreglo actualizado a JSON.");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicios de JSON</title>
</head>
<body>
    <h1>Ejercicios de JSON y PHP</h1>

    <section>
        <h2>1. Convertir un arreglo PHP a JSON</h2>
        <pre><?= htmlspecialchars($jsonProductos, ENT_QUOTES, "UTF-8") ?></pre>
    </section>

    <section>
        <h2>2. Leer productos.json</h2>
        <h3>Todos los productos</h3>
        <ul>
            <?php foreach ($productosJson as $producto): ?>
                <li><?= htmlspecialchars($producto["nombre"], ENT_QUOTES, "UTF-8") ?>: $<?= number_format($producto["precio"], 0, ",", ".") ?></li>
            <?php endforeach; ?>
        </ul>
        <h3>Productos con precio superior a $100.000</h3>
        <ul>
            <?php foreach ($productosJson as $producto): ?>
                <?php if ($producto["precio"] > 100000): ?>
                    <li><?= htmlspecialchars($producto["nombre"], ENT_QUOTES, "UTF-8") ?>: $<?= number_format($producto["precio"], 0, ",", ".") ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </section>

    <section>
        <h2>3. Modificar el precio de un producto</h2>
        <p>Producto ID <?= $idBuscado ?>: $<?= number_format($productosActualizados[$indiceProducto]["precio"], 0, ",", ".") ?> — <?= $precioVerificado ? "cambio verificado" : "cambio no verificado" ?></p>
        <pre><?= htmlspecialchars($jsonActualizado, ENT_QUOTES, "UTF-8") ?></pre>
    </section>
</body>
</html>
