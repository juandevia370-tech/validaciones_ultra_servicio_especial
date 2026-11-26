<?php
$escape = static function ($value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#3c0054">
    <title>Iniciar sesión | ULTRA Servicio Especial</title>
    <link rel="icon" type="image/png" href="<?= $escape(app_url('/img/favicon.png')) ?>">
    <link rel="stylesheet" href="<?= $escape(app_url('/css/app.css')) ?>">
</head>
<body>
    <main class="auth-page">
        <div class="auth-shell">
            <aside class="auth-aside">
                <img class="auth-aside-logo" src="<?= $escape(app_url('/img/logo-ultra-blanco.png')) ?>" alt="ULTRA Servicio Especial" width="200" height="188">
                <div>
                    <p class="auth-aside-tagline">Más allá de lo que esperas</p>
                    <ul class="auth-aside-list">
                        <li>Clientes y pasajeros</li>
                        <li>Conductores y vehículos</li>
                        <li>Destinos y servicios</li>
                    </ul>
                </div>
            </aside>

            <section class="auth-card" aria-labelledby="login-title">
                <img class="auth-logo" src="<?= $escape(app_url('/img/logo-ultra.png')) ?>" alt="ULTRA Servicio Especial" width="170" height="157">

                <header class="auth-heading">
                    <p class="auth-eyebrow">Sistema de transporte</p>
                    <h1 id="login-title">Bienvenido de nuevo</h1>
                    <p class="auth-subtitle">Ingresa con la cuenta autorizada para administrar tus servicios.</p>
                </header>

                <?php if ($loginError !== ''): ?>
                    <p class="auth-message" role="alert"><?= $escape($loginError) ?></p>
                <?php endif; ?>

                <form class="auth-form" id="login-form" method="post" action="<?= $escape(app_url('/login')) ?>" novalidate>
                    <?= csrf_field() ?>
                    <div class="form-field">
                        <label for="correo">Correo electrónico</label>
                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            value="<?= $escape($loginEmail) ?>"
                            placeholder="nombre@correo.com"
                            autocomplete="username"
                            inputmode="email"
                            maxlength="254"
                            required
                            <?= $loginEmail === '' ? 'autofocus' : '' ?>
                        >
                    </div>

                    <div class="form-field">
                        <label for="contrasena">Contraseña</label>
                        <input
                            type="password"
                            id="contrasena"
                            name="contrasena"
                            placeholder="Ingresa tu contraseña"
                            autocomplete="current-password"
                            minlength="6"
                            maxlength="72"
                            required
                            <?= $loginEmail !== '' ? 'autofocus' : '' ?>
                        >
                    </div>

                    <button class="auth-submit" id="login-submit" type="submit">Iniciar sesión</button>
                </form>

                <p class="auth-note">
                    Acceso protegido. Si aún no tienes usuario, solicítalo al administrador del sistema.
                </p>
            </section>
        </div>
    </main>
    <script src="<?= $escape(app_url('/js/login.js')) ?>" defer></script>
</body>
</html>
