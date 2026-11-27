# Validaciones Ultra Servicio Especial

Aplicación web en PHP para administrar clientes, conductores, pasajeros, servicios, destinos y vehículos. Usa MySQL/MariaDB (administrable con phpMyAdmin) y autenticación de usuarios propia en el backend PHP.

## Estructura del proyecto

```text
validaciones_ultra_servicio_especial/
├── apps/                 # Controladores, modelos, vistas y autenticación
├── config/               # Configuración de entorno y base de datos
├── database/schema.sql   # Esquema inicial de la base de datos
├── public/               # Raíz web; index.php y recursos públicos
├── .env.example          # Ejemplo de variables de conexión
└── README.md
```

## Archivos principales

### `public/index.php`

Es el punto de entrada y enrutador de la aplicación. La raíz web del servidor debe apuntar a `public`.

### `config/database.php`

Lee las credenciales de variables de entorno o del archivo `.env`, que no se debe publicar en Git.

## Requisitos

- PHP 7.4 o superior.
- Apache con `mod_rewrite` y soporte para `.htaccess`.
- Extensión PHP `pdo_mysql` (y `mbstring`).
- MySQL o MariaDB compatible.

## Ejecucion con XAMPP

1. Ubicar el proyecto dentro de la carpeta `htdocs` de XAMPP.
2. Crear la base `sistema_transporte` e importar `database/schema.sql` desde phpMyAdmin.
3. Copiar `.env.example` como `.env`; para XAMPP se pueden dejar los valores locales de ejemplo.
4. Iniciar Apache y MySQL desde el panel de control de XAMPP.
5. Configurar Apache para que la raíz del sitio sea la carpeta `public` y abrir:

```text
http://localhost/login
```

Ejecuta en phpMyAdmin las migraciones de `database/migraciones/` (001, 002 y 003), crea tu primer usuario con `php database/crear_usuario.php` e inicia sesión con él.

## Módulos del sistema

Después de iniciar sesión se puede seleccionar y administrar cualquiera de las tablas existentes en la base `sistema_transporte`: clientes, conductores, pasajeros, servicios, destinos y vehículos. Cada módulo permite consultar, crear, editar y eliminar registros.

## Autenticación de usuarios (propia, en el backend)

- Los usuarios viven en la tabla `usuarios` de la base del sistema. Las contraseñas se guardan **solo como hash bcrypt** (`password_hash` / `password_verify` de PHP); nadie puede leerlas.
- `POST /login` valida correo y contraseña en PHP, regenera el ID de sesión y guarda en la sesión del servidor solo el ID del usuario. No se usa ningún servicio externo de autenticación.
- Protecciones: bloqueo de la cuenta 15 minutos tras 5 contraseñas incorrectas, límite de intentos por sesión, tiempo de respuesta igual exista o no el correo, cierre automático tras 2 horas sin actividad (o 12 horas desde el inicio), cierre de las demás sesiones al cambiar la contraseña, token CSRF en todos los formularios, cookie `HttpOnly`/`SameSite=Lax` (`Secure` con HTTPS) y CSP estricta.
- **Gestión de usuarios** desde el panel (tabla *Usuarios*): crear, editar, activar/desactivar, cambiar contraseña y eliminar. No puedes desactivarte ni eliminarte a ti mismo, y siempre debe quedar al menos un usuario activo.
- **Crear usuarios por consola**:
  - Local (XAMPP): `php database/crear_usuario.php`
  - Producción: `php database/crear_usuario.php --sql` genera una instrucción SQL con la contraseña ya cifrada para ejecutarla en la pestaña SQL de phpMyAdmin.

## Despliegue en producción (Vercel + MySQL)

Arquitectura: **Vercel** (plan Hobby gratuito) ejecuta el PHP como funciones serverless con el runtime comunitario [`vercel-php`](https://github.com/vercel-community/php) (PHP 8.3) y sirve CSS, JS e imágenes como archivos estáticos. La base de datos es **MySQL/MariaDB** administrable con **phpMyAdmin** (por ejemplo, el plan gratuito de AlwaysData), accesible desde Internet. Como en serverless no hay disco persistente, las sesiones PHP se guardan en la tabla `sesiones` (`SESSION_DRIVER=database`). En local se usa XAMPP sin cambios.

| Archivo | Función |
|---|---|
| `vercel.json` | Runtime PHP, región, rutas, archivos estáticos y cabeceras de seguridad |
| `api/index.php`, `api/practica.php` | Puntos de entrada en Vercel (delegan en `public/`) |
| `api/php.ini` | PHP de producción en Vercel |
| `package.json` | Fija Node.js 22.x, requerido por `vercel-php` |
| `apps/middleware/SesionBaseDatos.php` | Sesiones PHP en MySQL |
| `database/migraciones/*.mysql.sql` | Cambios de esquema (001 servicios/vehículos, 002 usuarios, 003 sesiones) |
| `database/crear_usuario.php` | Crea usuarios por consola |
| `public/.htaccess`, `.htaccess` | Reglas de Apache (XAMPP): enrutador, cabeceras y bloqueo de archivos internos |

### 1. Base de datos MySQL

1. Crea una base MySQL/MariaDB con acceso remoto y su usuario (en AlwaysData: *Bases de datos > MySQL*).
2. En su **phpMyAdmin**, selecciona la base y usa **Importar** con el volcado de tu base local (`mysqldump` o *Exportar* desde el phpMyAdmin de XAMPP). Debe incluir todas las tablas, también `usuarios` y `sesiones`.
3. Crea el primer usuario: `php database/crear_usuario.php --sql` genera una instrucción con la contraseña ya cifrada; pégala en la pestaña **SQL** de phpMyAdmin.

### 2. Variables de entorno en Vercel

| Variable | Valor |
|---|---|
| `APP_ENV` | `production` |
| `DB_HOST` | servidor MySQL (AlwaysData: `mysql-<cuenta>.alwaysdata.net`) |
| `DB_PORT` | `3306` |
| `DB_NAME` | nombre de la base |
| `DB_USER` | usuario de la base |
| `DB_PASSWORD` | contraseña de la base (márcala como *Secret*) |
| `DB_SSL` | `true` (conexión cifrada) |
| `SESSION_DRIVER` | `database` |

Después de cambiar variables: **Deployments > ⋯ > Redeploy**. Comprueba `https://<proyecto>.vercel.app/health` → `{"status":"ok","database":"ok"}`.

### Actualizar el sistema

Cada `git push` a `main` despliega automáticamente en Vercel. Los cambios de esquema se ejecutan en phpMyAdmin (local y producción) con los archivos de `database/migraciones/`.

### Variables de entorno

| Variable | Descripción |
|---|---|
| `APP_ENV` | `local` o `production` |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` | Conexión MySQL |
| `DB_SSL`, `DB_SSL_CA` | Conexión cifrada hacia un MySQL remoto |
| `SESSION_DRIVER` | `files` (XAMPP) o `database` (Vercel) |

Nunca subas `.env` ni contraseñas al repositorio.

## Comprobacion por consola

Desde la raiz del proyecto se puede revisar la sintaxis con:

```bash
php -l config/database.php
php -l public/index.php
```
