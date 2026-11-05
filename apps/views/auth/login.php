<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f2f6f8">
    <title>Iniciar sesión | Ultra Servicio Especial</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(app_url('/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body data-base-path="<?= htmlspecialchars(APP_BASE_PATH, ENT_QUOTES, 'UTF-8') ?>">
    <main class="auth-page">
        <section class="auth-card" aria-labelledby="login-title">
            <a class="auth-brand" href="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>">
                <span class="auth-brand-mark" aria-hidden="true">U</span>
                <span>Ultra <strong>Servicio Especial</strong></span>
            </a>

            <header class="auth-heading">
                <p class="auth-eyebrow">Sistema de transporte</p>
                <h1 id="login-title">Bienvenido de nuevo</h1>
                <p class="auth-subtitle">Ingresa con la cuenta autorizada para administrar tus servicios.</p>
            </header>

            <p class="auth-message" id="login-message" role="alert" aria-live="polite" hidden></p>

            <form class="auth-form" id="login-form">
                <div class="form-field">
                    <label for="correo">Correo electrónico</label>
                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        placeholder="nombre@correo.com"
                        autocomplete="username"
                        inputmode="email"
                        required
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
                        required
                    >
                </div>

                <button class="auth-submit" id="login-submit" type="submit">Iniciar sesión</button>
            </form>

            <p class="auth-note">
                Acceso protegido con Supabase Auth. Si aún no tienes usuario, solicítalo al administrador del proyecto.
            </p>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="<?= htmlspecialchars(app_url('/js/auth/config.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= htmlspecialchars(app_url('/js/auth/supabase.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= htmlspecialchars(app_url('/js/auth/login.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
