<h2>Formulario de conductor</h2>

<form method="post">
    <input type="hidden" name="entidad" value="conductor">

    <label for="conductor_nombre">Nombre</label>
    <input type="text" id="conductor_nombre" name="nombre" placeholder="Nombre completo" required>

    <label for="conductor_documento">Documento</label>
    <input type="text" id="conductor_documento" name="documento" placeholder="Número de documento" required>

    <label for="conductor_licencia">Licencia</label>
    <input type="text" id="conductor_licencia" name="licencia" placeholder="Número de licencia" required>

    <label for="conductor_telefono">Teléfono</label>
    <input type="tel" id="conductor_telefono" name="telefono" placeholder="Número de teléfono" required>

    <button type="submit">Guardar conductor</button>
</form>