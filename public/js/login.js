// Evita envíos dobles del formulario de inicio de sesión y valida lo básico
// antes de enviarlo. La autenticación real ocurre en el servidor.
(() => {
    const form = document.querySelector('#login-form');
    const submit = document.querySelector('#login-submit');
    if (!form || !submit) {
        return;
    }

    form.addEventListener('submit', (event) => {
        if (!form.checkValidity()) {
            event.preventDefault();
            form.reportValidity();
            return;
        }

        submit.disabled = true;
        submit.textContent = 'Validando acceso…';
    });

    // Si el usuario vuelve con "Atrás", el botón queda utilizable otra vez.
    window.addEventListener('pageshow', () => {
        submit.disabled = false;
        submit.textContent = 'Iniciar sesión';
    });
})();
