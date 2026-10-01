<h2>Formulario de servicio</h2>

<form method="post">
    <input type="hidden" name="entidad" value="servicio">

    <label for="cliente_id">ID del cliente</label>
    <input type="number" id="cliente_id" name="cliente_id" min="1" placeholder="ID del cliente" required>

    <label for="pasajero_id">ID del pasajero</label>
    <input type="number" id="pasajero_id" name="pasajero_id" min="1" placeholder="ID del pasajero" required>

    <label for="conductor_id">ID del conductor</label>
    <input type="number" id="conductor_id" name="conductor_id" min="1" placeholder="ID del conductor" required>

    <label for="vehiculo_id">ID del vehículo</label>
    <input type="number" id="vehiculo_id" name="vehiculo_id" min="1" placeholder="ID del vehículo" required>

    <label for="destino_id">ID del destino</label>
    <input type="number" id="destino_id" name="destino_id" min="1" placeholder="ID del destino" required>

    <label for="fecha_servicio">Fecha del servicio</label>
    <input type="date" id="fecha_servicio" name="fecha_servicio" placeholder="Fecha del servicio" required>

    <label for="hora_servicio">Hora del servicio</label>
    <input type="time" id="hora_servicio" name="hora_servicio" placeholder="Hora del servicio" required>

    <label for="estado">Estado</label>
    <input type="text" id="estado" name="estado" placeholder="Estado del servicio" required>

    <button type="submit">Guardar servicio</button>
</form>