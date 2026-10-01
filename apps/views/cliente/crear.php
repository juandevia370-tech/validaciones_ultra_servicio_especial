<h2>Formulario de cliente</h2>

<form method="post">
    <input type="hidden" name="entidad" value="cliente">

    <label for="cliente_nombre">Nombre</label>
    <input type="text" id="cliente_nombre" name="nombre" placeholder="Nombre completo" required>

    <label for="cliente_documento">Documento</label>
    <input type="text" id="cliente_documento" name="documento" placeholder="Número de documento" required>

    <label for="cliente_correo">Correo</label>
    <input type="email" id="cliente_correo" name="correo" placeholder="correo@ejemplo.com" required>

    <label for="cliente_telefono">Teléfono</label>
    <input type="tel" id="cliente_telefono" name="telefono" placeholder="Número de teléfono" required>

    <button type="submit">Guardar cliente</button>
</form>