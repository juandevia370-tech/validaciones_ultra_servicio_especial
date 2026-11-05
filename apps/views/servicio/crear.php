<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Formulario de servicio</h2>

<form class="crud-form" method="post">
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

    <label for="vehiculo_id">Vehículo</label>
    <select id="vehiculo_id" name="vehiculo_id" required>
        <option value="">Selecciona un vehículo</option>
        <?php foreach ($vehiculos as $vehiculo): ?>
            <option value="<?= (int) $vehiculo['id'] ?>"><?= htmlspecialchars($vehiculo['placa'] . ' - ' . $vehiculo['marca'] . ' ' . $vehiculo['modelo'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>

    <label for="destino_id">Destino</label>
    <select id="destino_id" name="destino_id" required>
        <option value="">Selecciona un destino</option>
        <?php foreach ($destinos as $destino): ?>
            <option value="<?= (int) $destino['id'] ?>"><?= htmlspecialchars($destino['ciudad_origen'] . ' - ' . $destino['ciudad_destino'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>

    <label for="fecha_servicio">Fecha del servicio</label>
    <input type="date" id="fecha_servicio" name="fecha_servicio" placeholder="Fecha del servicio" required>

    <label for="hora_servicio">Hora del servicio</label>
    <input type="time" id="hora_servicio" name="hora_servicio" placeholder="Hora del servicio" required>

    <label for="estado">Estado</label>
    <input type="text" id="estado" name="estado" placeholder="Estado del servicio" required>

    <button type="submit">Guardar servicio</button>
</form>
</main>