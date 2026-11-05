<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#102a43">
    <title>Crear cuenta | Ultra Servicio Especial</title>
    <link rel="stylesheet" href="../../../public/css/auth/registro.css">
</head>
<body>
    <main class="auth-page">
        <section class="auth-card" aria-labelledby="registro-title">
            <a class="brand" href="../../../public/" aria-label="Ir al inicio">
                <span class="brand-mark" aria-hidden="true">U</span>
                <span>Ultra <strong>Servicio Especial</strong></span>
            </a>

            <div class="auth-heading">
                <p class="eyebrow">Empieza hoy</p>
                <h1 id="registro-title">Crea tu cuenta</h1>
                <p class="subtitle">Completa tus datos para registrarte.</p>
            </div>

            <form class="auth-form" method="post">
                <div class="form-field">
                    <label for="nombre">Nombre completo</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        placeholder="Escribe tu nombre"
                        autocomplete="name"
                        minlength="2"
                        required
                    >
                </div>

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
                    <label for="telefono">Teléfono <span class="optional">(opcional)</span></label>
                    <input
                        type="tel"
                        id="telefono"
                        name="telefono"
                        placeholder="Tu número de contacto"
                        autocomplete="tel"
                    >
                </div>

                <div class="form-field">
                    <label for="contrasena">Contraseña</label>
                    <input
                        type="password"
                        id="contrasena"
                        name="contrasena"
                        placeholder="Crea una contraseña"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                    <small class="field-hint">Usa al menos 8 caracteres.</small>
                </div>

                <div class="form-field">
                    <label for="confirmar_contrasena">Confirmar contraseña</label>
                    <input
                        type="password"
                        id="confirmar_contrasena"
                        name="confirmar_contrasena"
                        placeholder="Escribe de nuevo tu contraseña"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                </div>

                <label class="checkbox-label terms" for="acepta_condiciones">
                    <input type="checkbox" id="acepta_condiciones" name="acepta_condiciones" required>
                    <span>Acepto los términos de uso y la política de privacidad.</span>
                </label>

                <button class="submit-button" type="submit">Crear cuenta</button>
            </form>

            <p class="auth-footer">
                ¿Ya tienes una cuenta?
                <a class="text-link" href="login.php">Inicia sesión</a>
            </p>
        </section>
    </main>
</body>
</html>
