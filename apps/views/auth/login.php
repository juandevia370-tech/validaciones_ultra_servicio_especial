<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#102a43">
    <title>Iniciar sesión | Ultra Servicio Especial</title>
    <link rel="stylesheet" href="../../../public/css/auth/login.css">
</head>
<body>
    <main class="auth-page">
        <section class="auth-card" aria-labelledby="login-title">
            <a class="brand" href="../../../public/" aria-label="Ir al inicio">
                <span class="brand-mark" aria-hidden="true">U</span>
                <span>Ultra <strong>Servicio Especial</strong></span>
            </a>

            <div class="auth-heading">
                <p class="eyebrow">Qué bueno tenerte de vuelta</p>
                <h1 id="login-title">Inicia sesión</h1>
                <p class="subtitle">Ingresa tus datos para continuar.</p>
            </div>

            <form class="auth-form" method="post">
                <div class="form-field">
                    <label for="correo">Correo electrónico</label>
                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        placeholder="nombre@correo.com"
                        autocomplete="email"
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
                        required
                    >
                </div>

                <div class="form-options">
                    <label class="checkbox-label" for="recordarme">
                        <input type="checkbox" id="recordarme" name="recordarme">
                        <span>Recordarme</span>
                    </label>
                    <a class="text-link" href="#">¿Olvidaste tu contraseña?</a>
                </div>

                <button class="submit-button" type="submit">Entrar a mi cuenta</button>
            </form>

            <p class="auth-footer">
                ¿Aún no tienes una cuenta?
                <a class="text-link" href="registro.php">Regístrate</a>
            </p>

            <p class="security-note">
                Tus datos se mantienen protegidos y son de uso personal.
            </p>
        </section>
    </main>
</body>
</html>
