<?php
$e = static function ($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
};
?>
<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Editar pasajero #<?= (int) $pasajero['id'] ?></h2>

<form class="crud-form" method="post" action="<?= htmlspecialchars(app_url('/pasajero/actualizar'), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id" value="<?= (int) $pasajero['id'] ?>">

    <label for="pasajero_nombre">Nombre</label>
    <input type="text" id="pasajero_nombre" name="nombre" value="<?= $e($pasajero['nombre']) ?>" required>

    <label for="pasajero_documento">Documento</label>
    <input type="text" id="pasajero_documento" name="documento" value="<?= $e($pasajero['documento']) ?>" required>

    <label for="pasajero_telefono">Teléfono</label>
    <input type="tel" id="pasajero_telefono" name="telefono" value="<?= $e($pasajero['telefono']) ?>" required>

    <label for="pasajero_cliente_id">Cliente responsable</label>
    <select id="pasajero_cliente_id" name="cliente_id" required>
        <option value="">Selecciona un cliente</option>
        <?php foreach ($clientes as $cliente): ?>
            <option value="<?= (int) $cliente['id'] ?>" <?= (int) $cliente['id'] === (int) $pasajero['cliente_id'] ? 'selected' : '' ?>>
                <?= $e($cliente['nombre']) ?> (ID: <?= (int) $cliente['id'] ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Guardar cambios</button>
    <a href="<?= htmlspecialchars(app_url('/'), ENT_QUOTES, 'UTF-8') ?>">Cancelar</a>
</form>
</main>
