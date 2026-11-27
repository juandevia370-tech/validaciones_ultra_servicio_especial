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
            completarSiVacio(campo('direccion_origen'), opcion.dataset.direccionOrigen);
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
        const elegida = placas.options[placas.selectedIndex];
        if (elegida && elegida.disabled) {
            placas.value = '';
        }
    };

    filtro.addEventListener('change', aplicarFiltro);
    placas.addEventListener('change', () => {
        const elegida = placas.options[placas.selectedIndex];
        if (elegida && elegida.value && filtro.value === '') {
            filtro.value = elegida.dataset.tipo || '';
            aplicarFiltro();
        }
    });
    aplicarFiltro();
});

// Formulario de vehículo: sugiere las capacidades habituales según el tipo.
(() => {
    const tipo = document.getElementById('vehiculo_tipo');
    const lista = document.getElementById('capacidades_sugeridas');
    if (!tipo || !lista || !tipo.dataset.capacidades) {
        return;
    }
    let capacidades = {};
    try {
        capacidades = JSON.parse(tipo.dataset.capacidades);
    } catch (error) {
        return;
    }
    const actualizar = () => {
        lista.innerHTML = '';
        (capacidades[tipo.value] || []).forEach((valor) => {
            const opcion = document.createElement('option');
            opcion.value = valor;
            opcion.label = `${valor} pasajeros`;
            lista.appendChild(opcion);
        });
    };
    tipo.addEventListener('change', actualizar);
    actualizar();
})();
