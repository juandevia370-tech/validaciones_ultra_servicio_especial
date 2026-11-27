<?php
$e = static function ($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
};
?>
<link rel="stylesheet" href="<?= $e(app_url('/css/app.css')) ?>">
<main class="crud-page">
<h2>Editar usuario #<?= (int) $usuario['id'] ?></h2>

<form class="crud-form" method="post" action="<?= $e(app_url('/usuario/actualizar')) ?>" autocomplete="off">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">

    <label for="usuario_nombre">Nombre completo</label>
    <input type="text" id="usuario_nombre" name="nombre" maxlength="100" value="<?= $e($usuario['nombre']) ?>" required>

    <label for="usuario_correo">Correo electrónico</label>
    <input type="email" id="usuario_correo" name="correo" maxlength="254" value="<?= $e($usuario['correo']) ?>" required>

    <label class="check-row" for="usuario_activo">
        <input type="checkbox" id="usuario_activo" name="activo" value="1" <?= $usuario['activo'] ? 'checked' : '' ?> <?= $esUsuarioActual ? 'disabled' : '' ?>>
        <span>Usuario activo (puede iniciar sesión)</span>
    </label>
    <?php if ($esUsuarioActual): ?>
        <input type="hidden" name="activo" value="1">
        <p class="form-hint">Este es tu usuario: no puedes desactivarlo.</p>
    <?php endif; ?>

    <fieldset class="form-section">
        <legend>Cambiar contraseña (opcional)</legend>
        <div class="form-grid">
            <div class="form-pair">
                <label for="usuario_contrasena">Nueva contraseña</label>
                <input type="password" id="usuario_contrasena" name="contrasena" minlength="8" maxlength="72" autocomplete="new-password">
            </div>
            <div class="form-pair">
                <label for="usuario_confirmar">Confirmar contraseña</label>
                <input type="password" id="usuario_confirmar" name="confirmar_contrasena" minlength="8" maxlength="72" autocomplete="new-password">
            </div>
        </div>
    </fieldset>
    <p class="form-hint">Déjala en blanco para conservar la actual. Al cambiarla se cierran las demás sesiones de este usuario.</p>

    <button type="submit">Guardar cambios</button>
    <a href="<?= $e(app_url('/?tabla=usuarios')) ?>">Cancelar</a>
</form>
</main>
