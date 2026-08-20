# Actividad de apropiación

## 1. Requisito funcional seleccionado

Registrar y administrar la información de un servicio especial.

## 2. Clase que representa el requisito

La clase propuesta es `ServicioEspecial`.

## 3. Información que debe almacenar

- Nombre del servicio.
- Descripción del servicio.
- Precio del servicio.
- Duración del servicio en minutos.

## 4. Datos que no deberían aceptarse

- Un nombre vacío.
- Una descripción vacía.
- Un precio menor o igual a cero.
- Una duración menor o igual a cero.

## 5. Importancia de las validaciones

Las validaciones evitan registrar información incompleta o incorrecta. Esto ayuda a
mantener la calidad de los datos y permite que el sistema trabaje con servicios
coherentes.

## 6. Implementación

La clase se encuentra en `apps/models/ServicioEspecial.php` y las pruebas de
constructores, setters, validaciones y getters se encuentran en `public/index.php`.
