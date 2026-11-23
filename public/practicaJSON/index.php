<?php
$contenido = file_get_contents(__DIR__ . "/productos.json");
$productos = $contenido === false ? null : json_decode($contenido, true);
if (!is_array($productos)) exit("No se pudo leer productos.json.");

$idProducto = 3;
$nuevoPrecio = 80000;
$indiceProducto = null;
foreach ($productos as $indice => $producto) {
    if ($producto["id"] === $idProducto) {
        $indiceProducto = $indice;
        break;
    }
}
if ($indiceProducto === null) exit("Producto no encontrado.");
$productos[$indiceProducto]["precio"] = $nuevoPrecio;
$precioVerificado = $productos[$indiceProducto]["precio"] === $nuevoPrecio;
$jsonActualizado = json_encode($productos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if ($jsonActualizado === false) exit("No se pudo convertir a JSON.");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 3</title>
</head>
<body>
    <h1>Ejercicio 3: modificar precio</h1>
    <p><?= $precioVerificado ? "Precio actualizado y verificado: $" . number_format($nuevoPrecio, 0, ",", ".") : "No se pudo verificar el cambio." ?></p>
    <pre><?= htmlspecialchars($jsonActualizado, ENT_QUOTES, "UTF-8") ?></pre>
</body>
</html>
