<?php
$e = static function ($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
};
?>
<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Editar cliente #<?= (int) $cliente['id'] ?></h2>

<form class="crud-form" method="post" action="<?= htmlspecialchars(app_url('/cliente/actualizar'), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id" value="<?= (int) $cliente['id'] ?>">

    <label for="cliente_nombre">Nombre</label>
    <input type="text" id="cliente_nombre" name="nombre" value="<?= $e($cliente['nombre']) ?>" placeholder="Nombre completo" required>

    <label for="cliente_documento">Documento</label>
    <input type="text" id="cliente_documento" name="documento" value="<?= $e($cliente['documento']) ?>" placeholder="Número de documento" required>

    <label for="cliente_correo">Correo</label>
    <input type="email" id="cliente_correo" name="correo" value="<?= $e($cliente['correo']) ?>" placeholder="correo@ejemplo.com" required>

    <label for="cliente_telefono">Teléfono</label>
    <input type="tel" id="cliente_telefono" name="telefono" value="<?= $e($cliente['telefono']) ?>" placeholder="Número de teléfono" required>

    <button type="submit">Guardar cambios</button>
    <a href="<?= htmlspecialchars(app_url('/'), ENT_QUOTES, 'UTF-8') ?>">Cancelar</a>
</form>
</main>