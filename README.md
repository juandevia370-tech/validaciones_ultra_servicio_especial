# Validaciones Ultra Servicio Especial

Proyecto PHP desarrollado para aplicar encapsulamiento y validacion de datos en el registro de un servicio especial.

## Descripcion del proyecto

El proyecto representa un servicio ultra especial que puede ser registrado y administrado dentro de un sistema. Para proteger la informacion, los datos del servicio se manejan mediante una clase con atributos privados, constructores, metodos getter y setter, y reglas de validacion.

El requisito funcional seleccionado es:

> Registrar y administrar la informacion de un servicio especial.

## Clase principal

La clase `ServicioEspecial` almacena la informacion necesaria del servicio:

- `nombre`: nombre del servicio.
- `descripcion`: detalle de lo que ofrece el servicio.
- `precio`: valor del servicio.
- `duracionMinutos`: tiempo estimado del servicio en minutos.

## Encapsulamiento

Los cuatro atributos de `ServicioEspecial` se declararon como `private`. Esto impide modificar directamente la informacion desde fuera de la clase.

El acceso a los atributos se realiza mediante:

- Metodos `get` para consultar la informacion.
- Metodos `set` para actualizar la informacion de forma controlada.
- Un constructor para inicializar el objeto aplicando las mismas validaciones.

## Validaciones implementadas

El sistema no permite:

- Registrar un nombre vacio.
- Registrar una descripcion vacia.
- Registrar un precio menor o igual a cero.
- Registrar una duracion menor o igual a cero.

Cuando un dato no cumple una regla, el setter lanza una excepcion `InvalidArgumentException` con un mensaje que explica el error.

## Estructura del proyecto

```text
validaciones_ultra_servicio_especial/
├── apps/
│   └── models/
│       └── ServicioEspecial.php
├── docs/
│   └── actividad.md
├── public/
│   └── index.php
└── README.md
```

## Archivos principales

### `apps/models/ServicioEspecial.php`

Contiene la clase principal del proyecto, sus atributos privados, el constructor, los getters, los setters y las reglas de validacion.

### `public/index.php`

Crea una instancia de `ServicioEspecial`, prueba los setters con informacion valida, prueba las validaciones con informacion incorrecta y muestra los datos usando los getters.

### `docs/actividad.md`

Contiene las respuestas a las preguntas previas de la actividad de apropiacion.

## Requisitos

- PHP 7.4 o superior.
- XAMPP con Apache.

## Ejecucion con XAMPP

1. Ubicar el proyecto dentro de la carpeta `htdocs` de XAMPP.
2. Iniciar el servicio Apache desde el panel de control de XAMPP.
3. Abrir en el navegador:

```text
http://localhost/validaciones_ultra_servicio_especial/public/
```

La pagina mostrara la informacion valida del servicio y el resultado de cada validacion aplicada.

## Comprobacion por consola

Desde la raiz del proyecto se puede revisar la sintaxis con:

```bash
php -l apps/models/ServicioEspecial.php
php -l public/index.php
```

## Resultado esperado

El sistema debe mostrar el servicio con estos datos finales:

- Nombre: Servicio premium de atencion.
- Descripcion: Atencion prioritaria con seguimiento personalizado.
- Precio: 95000.
- Duracion: 90 minutos.

Tambien debe informar que fueron rechazados el nombre y la descripcion vacios, el precio igual a cero y la duracion negativa.
