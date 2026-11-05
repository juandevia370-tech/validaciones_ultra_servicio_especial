<?php
$e = static function ($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
};
?>
<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Editar conductor #<?= (int) $conductor['id'] ?></h2>

<form class="crud-form" method="post" action="<?= htmlspecialchars(app_url('/conductor/actualizar'), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id" value="<?= (int) $conductor['id'] ?>">

    <label for="conductor_nombre">Nombre</label>
    <input type="text" id="conductor_nombre" name="nombre" value="<?= $e($conductor['nombre']) ?>" required>

    <label for="conductor_documento">Documento</label>
    <input type="text" id="conductor_documento" name="documento" value="<?= $e($conductor['documento']) ?>" required>

    <label for="conductor_licencia">Licencia</label>
    <input type="text" id="conductor_licencia" name="licencia" value="<?= $e($conductor['licencia']) ?>" required>

    <label for="conductor_telefono">Teléfono</label>
    <input type="tel" id="conductor_telefono" name="telefono" value="<?= $e($conductor['telefono']) ?>" required>

    <button type="submit">Guardar cambios</button>
    <a href="<?= htmlspecialchars(app_url('/'), ENT_QUOTES, 'UTF-8') ?>">Cancelar</a>
</form>
</main>
