<?php
// Campos compartidos por los formularios de crear y editar servicio.
// Siguen el orden de la confirmación que se envía al conductor.
// Variables: $clientes, $pasajeros, $conductores, $vehiculos, $destinos y, al editar, $servicio.
$s = isset($servicio) && is_array($servicio) ? $servicio : [];
$h = static function ($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
};
$sel = static function ($a, $b): string {
    return (int) $a === (int) $b && (int) $a > 0 ? 'selected' : '';
};
?>
<fieldset class="form-section">
    <legend>Cliente y pasajero</legend>

    <label for="cliente_id">Cliente</label>
    <select id="cliente_id" name="cliente_id" required>
        <option value="">Selecciona un cliente</option>
        <?php foreach ($clientes as $cliente): ?>
            <option value="<?= (int) $cliente['id'] ?>" <?= $sel($cliente['id'], $s['cliente_id'] ?? 0) ?>><?= $h($cliente['nombre']) ?></option>
        <?php endforeach; ?>
    </select>

    <div class="form-grid">
        <div class="form-pair">
            <label for="pasajero_id">Nombre (pasajero)</label>
            <select id="pasajero_id" name="pasajero_id" required>
                <option value="">Selecciona un pasajero</option>
                <?php foreach ($pasajeros as $pasajero): ?>
                    <option value="<?= (int) $pasajero['id'] ?>" data-telefono="<?= $h($pasajero['telefono'] ?? '') ?>" <?= $sel($pasajero['id'], $s['pasajero_id'] ?? 0) ?>><?= $h($pasajero['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-pair">
            <label for="telefono_contacto">Teléfono</label>
            <input type="tel" id="telefono_contacto" name="telefono_contacto" maxlength="20" value="<?= $h($s['telefono_contacto'] ?? '') ?>" placeholder="Teléfono de contacto" required>
        </div>
    </div>
</fieldset>

<fieldset class="form-section">
    <legend>Recorrido</legend>

    <label for="destino_id">Ruta registrada</label>
    <select id="destino_id" name="destino_id" required>
        <option value="">Selecciona una ruta</option>
        <?php foreach ($destinos as $destino): ?>
            <option
                value="<?= (int) $destino['id'] ?>"
                data-origen="<?= $h($destino['ciudad_origen']) ?>"
                data-destino="<?= $h($destino['ciudad_destino']) ?>"
                data-direccion="<?= $h($destino['direccion_destino'] ?? '') ?>"
                data-tarifa="<?= $h($destino['tarifa']) ?>"
                <?= $sel($destino['id'], $s['destino_id'] ?? 0) ?>
            ><?= $h($destino['ciudad_origen'] . ' - ' . $destino['ciudad_destino']) ?></option>
        <?php endforeach; ?>
    </select>
    <p class="form-hint">Al elegir la ruta se completan las ciudades, la dirección de destino y el flete; puedes ajustarlos.</p>

    <div class="form-grid">
        <div class="form-pair">
            <label for="ciudad_origen">Ciudad de origen</label>
            <input type="text" id="ciudad_origen" name="ciudad_origen" maxlength="100" value="<?= $h($s['ciudad_origen'] ?? '') ?>" required>
        </div>
        <div class="form-pair">
            <label for="direccion_origen">Dirección de origen</label>
            <input type="text" id="direccion_origen" name="direccion_origen" maxlength="150" value="<?= $h($s['direccion_origen'] ?? '') ?>" placeholder="Dirección de recogida" required>
        </div>
        <div class="form-pair">
            <label for="ciudad_destino">Ciudad de destino</label>
            <input type="text" id="ciudad_destino" name="ciudad_destino" maxlength="100" value="<?= $h($s['ciudad_destino'] ?? '') ?>" required>
        </div>
        <div class="form-pair">
            <label for="direccion_destino">Dirección de destino</label>
            <input type="text" id="direccion_destino" name="direccion_destino" maxlength="150" value="<?= $h($s['direccion_destino'] ?? '') ?>" required>
        </div>
    </div>
</fieldset>

<fieldset class="form-section">
    <legend>Servicio</legend>
    <div class="form-grid">
        <div class="form-pair">
            <label for="fecha_servicio">Fecha del servicio</label>
            <input type="date" id="fecha_servicio" name="fecha_servicio" value="<?= $h($s['fecha_servicio'] ?? '') ?>" required>
        </div>
        <div class="form-pair">
            <label for="hora_servicio">Hora de salida</label>
            <input type="time" id="hora_servicio" name="hora_servicio" value="<?= $h(isset($s['hora_servicio']) ? substr((string) $s['hora_servicio'], 0, 5) : '') ?>" required>
        </div>
        <div class="form-pair">
            <label for="hora_llegada">Hora de llegada</label>
            <input type="time" id="hora_llegada" name="hora_llegada" value="<?= $h(!empty($s['hora_llegada']) ? substr((string) $s['hora_llegada'], 0, 5) : '') ?>" required>
        </div>
        <div class="form-pair">
            <label for="ticket">Ticket</label>
            <input type="text" id="ticket" name="ticket" maxlength="30" value="<?= $h($s['ticket'] ?? '') ?>" placeholder="Número de ticket (opcional)">
        </div>
        <div class="form-pair">
            <label for="flete">Flete</label>
            <input type="number" id="flete" name="flete" min="0" step="0.01" value="<?= $h($s['flete'] ?? '') ?>" placeholder="Valor del servicio" required>
        </div>
    </div>
</fieldset>

<fieldset class="form-section">
    <legend>Asignación</legend>
    <div class="form-grid">
        <div class="form-pair">
            <label for="conductor_id">Conductor</label>
            <select id="conductor_id" name="conductor_id" required>
                <option value="">Selecciona un conductor</option>
                <?php foreach ($conductores as $conductor): ?>
                    <option value="<?= (int) $conductor['id'] ?>" <?= $sel($conductor['id'], $s['conductor_id'] ?? 0) ?>><?= $h($conductor['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-pair">
            <label for="tipo_vehiculo">Tipo de vehículo</label>
            <select id="tipo_vehiculo" data-filtra="vehiculo_id">
                <option value="">Todos los tipos</option>
                <?php foreach (Vehiculo::TIPOS as $tipo): ?>
                    <option value="<?= $h($tipo) ?>" <?= ($s['tipo_vehiculo'] ?? '') === $tipo ? 'selected' : '' ?>><?= $h($tipo) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <label for="estado">Estado</label>
    <input type="text" id="estado" name="estado" maxlength="30" value="<?= $h($s['estado'] ?? '') ?>" placeholder="Estado del servicio" required>
</fieldset>

<fieldset class="form-section">
    <legend>Placa</legend>
    <label for="vehiculo_id">Placa del vehículo</label>
    <select id="vehiculo_id" name="vehiculo_id" required>
        <option value="">Selecciona la placa</option>
        <?php foreach ($vehiculos as $vehiculo): ?>
            <option value="<?= (int) $vehiculo['id'] ?>" data-tipo="<?= $h($vehiculo['tipo'] ?? '') ?>" <?= $sel($vehiculo['id'], $s['vehiculo_id'] ?? 0) ?>><?= $h($vehiculo['placa']) ?> · <?= $h($vehiculo['tipo'] ?? '') ?></option>
        <?php endforeach; ?>
    </select>
    <p class="form-hint">Elige primero el tipo de vehículo para ver solo sus placas.</p>
</fieldset>

<label for="observaciones">Observaciones</label>
<textarea id="observaciones" name="observaciones" rows="4" maxlength="1000" placeholder="Indicaciones adicionales para el servicio"><?= $h($s['observaciones'] ?? '') ?></textarea>
