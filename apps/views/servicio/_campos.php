<?php
// Campos compartidos por los formularios de crear y editar servicio.
// Variables disponibles: $vehiculos y, al editar, $servicio.
$datosServicio = isset($servicio) && is_array($servicio) ? $servicio : [];
$h = static function ($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
};
$vehiculoActual = (int) ($datosServicio['vehiculo_id'] ?? 0);
$tipoActual = (string) ($datosServicio['tipo_vehiculo'] ?? '');
$estadoActual = (string) ($datosServicio['estado'] ?? 'Pendiente');
$horaCorta = static function ($hora): string {
    return $hora ? substr((string) $hora, 0, 5) : '';
};
?>
<fieldset class="form-section">
    <legend>Vehículo</legend>

    <label for="servicio_tipo_vehiculo">Tipo de vehículo</label>
    <select id="servicio_tipo_vehiculo" data-filtra="servicio_vehiculo_id">
        <option value="">Todos los tipos</option>
        <?php foreach (Vehiculo::TIPOS as $tipo): ?>
            <option value="<?= $h($tipo) ?>" <?= $tipo === $tipoActual ? 'selected' : '' ?>><?= $h($tipo) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="servicio_vehiculo_id">Placa</label>
    <select id="servicio_vehiculo_id" name="vehiculo_id" required>
        <option value="">Selecciona la placa</option>
        <?php foreach ($vehiculos as $vehiculo): ?>
            <option
                value="<?= (int) $vehiculo['id'] ?>"
                data-tipo="<?= $h($vehiculo['tipo'] ?? '') ?>"
                <?= (int) $vehiculo['id'] === $vehiculoActual ? 'selected' : '' ?>
            ><?= $h($vehiculo['placa']) ?> · <?= $h($vehiculo['tipo'] ?? '') ?> (<?= $h($vehiculo['marca'] . ' ' . $vehiculo['modelo']) ?>)</option>
        <?php endforeach; ?>
    </select>
</fieldset>

<fieldset class="form-section">
    <legend>Salida y llegada</legend>
    <div class="form-grid">
        <div class="form-pair">
            <label for="servicio_fecha">Fecha de salida</label>
            <input type="date" id="servicio_fecha" name="fecha_servicio" value="<?= $h($datosServicio['fecha_servicio'] ?? '') ?>" required>
        </div>
        <div class="form-pair">
            <label for="servicio_hora">Hora de salida</label>
            <input type="time" id="servicio_hora" name="hora_servicio" value="<?= $h($horaCorta($datosServicio['hora_servicio'] ?? '')) ?>" required>
        </div>
        <div class="form-pair">
            <label for="servicio_fecha_llegada">Fecha de llegada</label>
            <input type="date" id="servicio_fecha_llegada" name="fecha_llegada" value="<?= $h($datosServicio['fecha_llegada'] ?? '') ?>" required>
        </div>
        <div class="form-pair">
            <label for="servicio_hora_llegada">Hora de llegada</label>
            <input type="time" id="servicio_hora_llegada" name="hora_llegada" value="<?= $h($horaCorta($datosServicio['hora_llegada'] ?? '')) ?>" required>
        </div>
    </div>
</fieldset>

<label for="servicio_estado">Estado</label>
<select id="servicio_estado" name="estado" required>
    <?php foreach (Servicio::ESTADOS as $estado): ?>
        <option value="<?= $h($estado) ?>" <?= $estado === $estadoActual ? 'selected' : '' ?>><?= $h($estado) ?></option>
    <?php endforeach; ?>
</select>
<p class="form-hint">Al guardar un servicio como <strong>Finalizado</strong> ya no se podrá editar.</p>
