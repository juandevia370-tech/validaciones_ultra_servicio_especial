(() => {
    const form = document.querySelector('#login-form');
    const message = document.querySelector('#login-message');
    const submit = document.querySelector('#login-submit');
    const basePath = document.body.dataset.basePath || '';

    const showMessage = (text, type = 'error') => {
        message.textContent = text;
        message.dataset.type = type;
        message.hidden = !text;
    };

    // Si el navegador ya tiene una sesión válida de Supabase (por ejemplo, tras
    // un reinicio del servidor que borró la sesión PHP), se restaura sin pedir
    // de nuevo la contraseña.
    const restoreSession = async () => {
        try {
            const { data } = await window.supabaseClient.auth.getSession();
            const session = data && data.session;
            if (!session) {
                return;
            }

            const response = await fetch(`${basePath}/auth/session`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({
                    access_token: session.access_token,
                    refresh_token: session.refresh_token
                })
            });

            if (response.ok) {
                window.location.assign(`${basePath}/`);
            } else if (response.status === 401) {
                await window.supabaseClient.auth.signOut();
            }
        } catch (error) {
            // Sin sesión restaurable: se muestra el formulario normalmente.
        }
    };
    restoreSession();

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        showMessage('');
        submit.disabled = true;
        submit.textContent = 'Validando acceso…';

        try {
            const { data, error } = await window.supabaseClient.auth.signInWithPassword({
                email: form.elements.correo.value.trim(),
                password: form.elements.contrasena.value
            });

            if (error || !data.session) {
                showMessage(error?.message || 'No se pudo iniciar sesión. Revisa tus datos.');
                return;
            }

            const response = await fetch(`${basePath}/auth/session`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({
                    access_token: data.session.access_token,
                    refresh_token: data.session.refresh_token
                })
            });
            const result = await response.json();

            if (!response.ok) {
                await window.supabaseClient.auth.signOut();
                showMessage(result.message || 'No fue posible verificar tu sesión.');
                return;
            }

            window.location.assign(`${basePath}/`);
        } catch (error) {
            showMessage('No fue posible conectar con el servicio de autenticación. Intenta de nuevo.');
        } finally {
            submit.disabled = false;
            submit.textContent = 'Iniciar sesión';
        }
    });
})();
