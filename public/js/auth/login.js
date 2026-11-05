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
