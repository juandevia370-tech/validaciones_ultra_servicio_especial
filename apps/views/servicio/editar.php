<?php
$e = static function ($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
};
?>
<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Editar servicio #<?= (int) $servicio['id'] ?></h2>

<form class="crud-form" method="post" action="<?= htmlspecialchars(app_url('/servicio/actualizar'), ENT_QUOTES, 'UTF-8') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int) $servicio['id'] ?>">

    <label for="servicio_cliente_id">Cliente</label>
    <select id="servicio_cliente_id" name="cliente_id" required>
        <option value="">Selecciona un cliente</option>
        <?php foreach ($clientes as $cliente): ?>
            <option value="<?= (int) $cliente['id'] ?>" <?= (int) $cliente['id'] === (int) $servicio['cliente_id'] ? 'selected' : '' ?>>
                <?= $e($cliente['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="servicio_pasajero_id">Pasajero</label>
    <select id="servicio_pasajero_id" name="pasajero_id" required>
        <option value="">Selecciona un pasajero</option>
        <?php foreach ($pasajeros as $pasajero): ?>
            <option value="<?= (int) $pasajero['id'] ?>" <?= (int) $pasajero['id'] === (int) $servicio['pasajero_id'] ? 'selected' : '' ?>>
                <?= $e($pasajero['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="servicio_conductor_id">Conductor</label>
    <select id="servicio_conductor_id" name="conductor_id" required>
        <option value="">Selecciona un conductor</option>
        <?php foreach ($conductores as $conductor): ?>
            <option value="<?= (int) $conductor['id'] ?>" <?= (int) $conductor['id'] === (int) $servicio['conductor_id'] ? 'selected' : '' ?>>
                <?= $e($conductor['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="servicio_destino_id">Destino</label>
    <select id="servicio_destino_id" name="destino_id" required>
        <option value="">Selecciona un destino</option>
        <?php foreach ($destinos as $destino): ?>
            <option value="<?= (int) $destino['id'] ?>" <?= (int) $destino['id'] === (int) $servicio['destino_id'] ? 'selected' : '' ?>>
                <?= $e($destino['ciudad_origen'] . ' - ' . $destino['ciudad_destino']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php require __DIR__ . '/_campos.php'; ?>

    <button type="submit">Guardar cambios</button>
    <a href="<?= htmlspecialchars(app_url('/'), ENT_QUOTES, 'UTF-8') ?>">Cancelar</a>
</form>
</main>
<script src="<?= htmlspecialchars(app_url('/js/app.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>
