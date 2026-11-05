<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Formulario de destino</h2>

<form class="crud-form" method="post">
    <label for="destino_ciudad_origen">Ciudad de origen</label>
    <input type="text" id="destino_ciudad_origen" name="ciudad_origen" maxlength="100" required>

    <label for="destino_ciudad_destino">Ciudad de destino</label>
    <input type="text" id="destino_ciudad_destino" name="ciudad_destino" maxlength="100" required>

    <label for="destino_direccion">Dirección de destino (opcional)</label>
    <input type="text" id="destino_direccion" name="direccion_destino" maxlength="150">

    <label for="destino_tarifa">Tarifa</label>
    <input type="number" id="destino_tarifa" name="tarifa" min="0.01" step="0.01" required>

    <button type="submit">Guardar destino</button>
    <a href="<?= htmlspecialchars(app_url('/'), ENT_QUOTES, 'UTF-8') ?>">Volver</a>
</form>
</main>
