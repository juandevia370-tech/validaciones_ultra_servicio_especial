// Confirmación antes de eliminar registros (sin JavaScript en línea).
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (form instanceof HTMLFormElement && form.dataset.confirm) {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    }
});

// Filtra las placas según el tipo de vehículo elegido en el formulario de servicio.
document.querySelectorAll('select[data-filtra]').forEach((filtro) => {
    const placas = document.getElementById(filtro.dataset.filtra);
    if (!placas) {
        return;
    }

    const aplicarFiltro = () => {
        const tipo = filtro.value;
        Array.from(placas.options).forEach((opcion) => {
            if (!opcion.value) {
                return;
            }
            const visible = tipo === '' || opcion.dataset.tipo === tipo;
            opcion.hidden = !visible;
            opcion.disabled = !visible;
        });

        const seleccionada = placas.options[placas.selectedIndex];
        if (seleccionada && seleccionada.disabled) {
            placas.value = '';
        }
    };

    filtro.addEventListener('change', aplicarFiltro);
    placas.addEventListener('change', () => {
        const seleccionada = placas.options[placas.selectedIndex];
        if (seleccionada && seleccionada.value && filtro.value === '') {
            filtro.value = seleccionada.dataset.tipo || '';
            aplicarFiltro();
        }
    });
    aplicarFiltro();
});
