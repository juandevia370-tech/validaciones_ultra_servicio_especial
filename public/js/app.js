// Confirmación antes de eliminar registros (sin JavaScript en línea).
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (form instanceof HTMLFormElement && form.dataset.confirm) {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    }
});

// Formulario de servicio: precarga datos a partir de la ruta y del pasajero.
// Solo completa campos vacíos para no borrar lo que el usuario ya escribió.
(() => {
    const campo = (id) => document.getElementById(id);
    const completarSiVacio = (input, valor) => {
        if (input && valor && input.value.trim() === '') {
            input.value = valor;
        }
    };
    const opcionElegida = (select) => (select ? select.options[select.selectedIndex] : null);

    const ruta = campo('destino_id');
    if (ruta) {
        ruta.addEventListener('change', () => {
            const opcion = opcionElegida(ruta);
            if (!opcion || !opcion.value) {
                return;
            }
            completarSiVacio(campo('ciudad_origen'), opcion.dataset.origen);
            completarSiVacio(campo('ciudad_destino'), opcion.dataset.destino);
            completarSiVacio(campo('direccion_destino'), opcion.dataset.direccion);
            completarSiVacio(campo('flete'), opcion.dataset.tarifa);
        });
    }

    const pasajero = campo('pasajero_id');
    if (pasajero) {
        pasajero.addEventListener('change', () => {
            const opcion = opcionElegida(pasajero);
            if (opcion && opcion.value) {
                completarSiVacio(campo('telefono_contacto'), opcion.dataset.telefono);
            }
        });
    }
})();
