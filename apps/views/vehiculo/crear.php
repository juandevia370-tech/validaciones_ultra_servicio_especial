<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Formulario de vehículo</h2>

<form class="crud-form" method="post">
    <label for="vehiculo_placa">Placa</label>
    <input type="text" id="vehiculo_placa" name="placa" maxlength="10" required>

    <label for="vehiculo_marca">Marca</label>
    <input type="text" id="vehiculo_marca" name="marca" maxlength="50" required>

    <label for="vehiculo_modelo">Modelo</label>
    <input type="text" id="vehiculo_modelo" name="modelo" maxlength="50" required>

    <label for="vehiculo_capacidad">Capacidad de pasajeros</label>
    <input type="number" id="vehiculo_capacidad" name="capacidad" min="1" required>

    <label for="vehiculo_conductor_id">Conductor asignado</label>
    <select id="vehiculo_conductor_id" name="conductor_id" required>
        <option value="">Selecciona un conductor</option>
        <?php foreach ($conductores as $conductor): ?>
            <option value="<?= (int) $conductor['id'] ?>">
                <?= htmlspecialchars($conductor['nombre'], ENT_QUOTES, 'UTF-8') ?>
                (ID: <?= (int) $conductor['id'] ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Guardar vehículo</button>
    <a href="<?= htmlspecialchars(app_url('/'), ENT_QUOTES, 'UTF-8') ?>">Volver</a>
</form>
</main>
