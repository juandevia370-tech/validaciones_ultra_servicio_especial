<?php
$e = static function ($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
};
?>
<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Editar destino #<?= (int) $destino['id'] ?></h2>

<form class="crud-form" method="post" action="<?= htmlspecialchars(app_url('/destino/actualizar'), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id" value="<?= (int) $destino['id'] ?>">

    <label for="destino_ciudad_origen">Ciudad de origen</label>
    <input type="text" id="destino_ciudad_origen" name="ciudad_origen" maxlength="100" value="<?= $e($destino['ciudad_origen']) ?>" required>

    <label for="destino_ciudad_destino">Ciudad de destino</label>
    <input type="text" id="destino_ciudad_destino" name="ciudad_destino" maxlength="100" value="<?= $e($destino['ciudad_destino']) ?>" required>

    <label for="destino_direccion">Dirección de destino (opcional)</label>
    <input type="text" id="destino_direccion" name="direccion_destino" maxlength="150" value="<?= $e($destino['direccion_destino']) ?>">

    <label for="destino_tarifa">Tarifa</label>
    <input type="number" id="destino_tarifa" name="tarifa" min="0.01" step="0.01" value="<?= $e($destino['tarifa']) ?>" required>

    <button type="submit">Guardar cambios</button>
    <a href="<?= htmlspecialchars(app_url('/'), ENT_QUOTES, 'UTF-8') ?>">Cancelar</a>
</form>
</main>
