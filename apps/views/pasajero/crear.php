<h2>Formulario de pasajero</h2>

<form method="post">
    <input type="hidden" name="entidad" value="pasajero">

    <label for="pasajero_nombre">Nombre</label>
    <input type="text" id="pasajero_nombre" name="nombre" placeholder="Nombre completo" required>

    <label for="pasajero_documento">Documento</label>
    <input type="text" id="pasajero_documento" name="documento" placeholder="Número de documento" required>

    <label for="pasajero_telefono">Teléfono</label>
    <input type="tel" id="pasajero_telefono" name="telefono" placeholder="Número de teléfono" required>

    <label for="pasajero_cliente_id">Cliente responsable</label>
    <select id="pasajero_cliente_id" name="cliente_id" required>
        <option value="">Selecciona un cliente</option>
        <?php foreach ($clientes as $cliente): ?>
            <option value="<?= (int) $cliente['id'] ?>">
                <?= htmlspecialchars($cliente['nombre'], ENT_QUOTES, 'UTF-8') ?> (ID: <?= (int) $cliente['id'] ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Guardar pasajero</button>
</form>