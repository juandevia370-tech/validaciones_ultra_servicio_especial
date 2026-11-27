<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Formulario de servicio</h2>

<form class="crud-form" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="entidad" value="servicio">

    <label for="cliente_id">Cliente</label>
    <select id="cliente_id" name="cliente_id" required>
        <option value="">Selecciona un cliente</option>
        <?php foreach ($clientes as $cliente): ?>
            <option value="<?= (int) $cliente['id'] ?>"><?= htmlspecialchars($cliente['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>

    <label for="pasajero_id">Pasajero</label>
    <select id="pasajero_id" name="pasajero_id" required>
        <option value="">Selecciona un pasajero</option>
        <?php foreach ($pasajeros as $pasajero): ?>
            <option value="<?= (int) $pasajero['id'] ?>"><?= htmlspecialchars($pasajero['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>

    <label for="conductor_id">Conductor</label>
    <select id="conductor_id" name="conductor_id" required>
        <option value="">Selecciona un conductor</option>
        <?php foreach ($conductores as $conductor): ?>
            <option value="<?= (int) $conductor['id'] ?>"><?= htmlspecialchars($conductor['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>

    <label for="destino_id">Destino</label>
    <select id="destino_id" name="destino_id" required>
        <option value="">Selecciona un destino</option>
        <?php foreach ($destinos as $destino): ?>
            <option value="<?= (int) $destino['id'] ?>"><?= htmlspecialchars($destino['ciudad_origen'] . ' - ' . $destino['ciudad_destino'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>

    <?php require __DIR__ . '/_campos.php'; ?>

    <button type="submit">Guardar servicio</button>
</form>
</main>
<script src="<?= htmlspecialchars(app_url('/js/app.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>
