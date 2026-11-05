<?php
$e = static function ($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
};
?>
<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Editar vehículo #<?= (int) $vehiculo['id'] ?></h2>

<form class="crud-form" method="post" action="<?= htmlspecialchars(app_url('/vehiculo/actualizar'), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id" value="<?= (int) $vehiculo['id'] ?>">

    <label for="vehiculo_placa">Placa</label>
    <input type="text" id="vehiculo_placa" name="placa" maxlength="10" value="<?= $e($vehiculo['placa']) ?>" required>

    <label for="vehiculo_marca">Marca</label>
    <input type="text" id="vehiculo_marca" name="marca" maxlength="50" value="<?= $e($vehiculo['marca']) ?>" required>

    <label for="vehiculo_modelo">Modelo</label>
    <input type="text" id="vehiculo_modelo" name="modelo" maxlength="50" value="<?= $e($vehiculo['modelo']) ?>" required>

    <label for="vehiculo_capacidad">Capacidad de pasajeros</label>
    <input type="number" id="vehiculo_capacidad" name="capacidad" min="1" value="<?= (int) $vehiculo['capacidad'] ?>" required>

    <label for="vehiculo_conductor_id">Conductor asignado</label>
    <select id="vehiculo_conductor_id" name="conductor_id" required>
        <option value="">Selecciona un conductor</option>
        <?php foreach ($conductores as $conductor): ?>
            <option value="<?= (int) $conductor['id'] ?>" <?= (int) $conductor['id'] === (int) $vehiculo['conductor_id'] ? 'selected' : '' ?>>
                <?= $e($conductor['nombre']) ?> (ID: <?= (int) $conductor['id'] ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Guardar cambios</button>
    <a href="<?= htmlspecialchars(app_url('/'), ENT_QUOTES, 'UTF-8') ?>">Cancelar</a>
</form>
</main>
