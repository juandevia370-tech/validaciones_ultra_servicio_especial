<link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
<main class="crud-page">
<h2>Nuevo usuario</h2>

<form class="crud-form" method="post" autocomplete="off">
    <?= csrf_field() ?>
    <label for="usuario_nombre">Nombre completo</label>
    <input type="text" id="usuario_nombre" name="nombre" maxlength="100" required>

    <label for="usuario_correo">Correo electrónico</label>
    <input type="email" id="usuario_correo" name="correo" maxlength="254" autocomplete="off" required>

    <fieldset class="form-section">
        <legend>Contraseña</legend>
        <div class="form-grid">
            <div class="form-pair">
                <label for="usuario_contrasena">Contraseña</label>
                <input type="password" id="usuario_contrasena" name="contrasena" minlength="8" maxlength="72" autocomplete="new-password" required>
            </div>
            <div class="form-pair">
                <label for="usuario_confirmar">Confirmar contraseña</label>
                <input type="password" id="usuario_confirmar" name="confirmar_contrasena" minlength="8" maxlength="72" autocomplete="new-password" required>
            </div>
        </div>
    </fieldset>
    <p class="form-hint">Mínimo 8 caracteres, con al menos una letra y un número. Se guarda cifrada; nadie puede verla después.</p>

    <button type="submit">Crear usuario</button>
    <a href="<?= htmlspecialchars(app_url('/?tabla=usuarios'), ENT_QUOTES, 'UTF-8') ?>">Volver</a>
</form>
</main>
