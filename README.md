# Validaciones Ultra Servicio Especial

Aplicación web en PHP para administrar clientes, conductores, pasajeros, servicios, destinos y vehículos. Usa MySQL/MariaDB para los datos y Supabase Auth para el inicio de sesión.

## Estructura del proyecto

```text
validaciones_ultra_servicio_especial/
├── apps/                 # Controladores, modelos, vistas y autenticación
├── config/               # Configuración de conexión y Supabase
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
- Extensiones PHP `pdo_mysql` (local) o `pdo_pgsql` (producción) y `curl`.
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

Inicia sesión con un usuario creado en Supabase Auth.

## Módulos del sistema

Después de iniciar sesión se puede seleccionar y administrar cualquiera de las tablas existentes en la base `sistema_transporte`: clientes, conductores, pasajeros, servicios, destinos y vehículos. Cada módulo permite consultar, crear, editar y eliminar registros.

## Autenticación (Supabase Auth desde el servidor)

- El inicio de sesión se procesa **en el backend PHP**: el formulario envía correo y contraseña a `POST /login`, PHP los valida con Supabase (`/auth/v1/token`) y guarda los tokens **solo en la sesión del servidor**. El navegador nunca recibe tokens ni carga librerías de Supabase.
- Cada petición privada verifica el token con Supabase y lo renueva con el *refresh token* cuando expira. `POST /auth/logout` revoca los tokens en Supabase y destruye la sesión.
- Protecciones: token CSRF en todos los formularios, límite de 5 intentos fallidos cada 15 minutos por sesión, regeneración del ID de sesión al iniciar, cookie `HttpOnly`/`SameSite=Lax` (`Secure` con HTTPS), mensajes de error genéricos y política CSP sin scripts externos.
- La URL del proyecto y la clave pública (`publishable`) se configuran en `config/supabase.php` o con las variables `SUPABASE_URL` y `SUPABASE_PUBLISHABLE_KEY`. Nunca uses la clave `service_role`.
- Los usuarios se crean en Supabase > Authentication > Users (marcando *Auto Confirm User*).

## Despliegue en producción (Vercel + Supabase)

Arquitectura: **Vercel** (plan Hobby gratuito) ejecuta el PHP como funciones serverless con el runtime comunitario [`vercel-php`](https://github.com/vercel-community/php) (PHP 8.3) y sirve CSS/JS como archivos estáticos. **Supabase** aporta la base de datos PostgreSQL (esquema privado `transporte`), la autenticación y el almacenamiento de las sesiones PHP (tabla `transporte.sesiones`), porque en serverless no hay disco persistente. En local se sigue usando XAMPP/MariaDB sin cambios (`DB_DRIVER=mysql`).

| Archivo | Función |
|---|---|
| `vercel.json` | Runtime PHP, reescrituras de rutas, archivos estáticos y cabeceras de seguridad |
| `api/index.php` | Punto de entrada en Vercel; delega en `public/index.php` |
| `api/practica.php` | Sirve la práctica `public/practicaJSON` |
| `api/php.ini` | PHP de producción (sin mostrar errores, sesiones seguras) |
| `package.json` | Fija Node.js 22.x, requerido por `vercel-php` |
| `apps/middleware/SesionBaseDatos.php` | Sesiones PHP en PostgreSQL (`SESSION_DRIVER=database`) |
| `database/schema.pgsql.sql` | Esquema PostgreSQL equivalente al de MySQL (PK, UNIQUE, FK, RLS) |
| `database/exportar_a_supabase.php` | Genera el SQL de migración con los datos de MySQL local |
| `Dockerfile`, `docker/`, `render.yaml` | Alternativa: despliegue en Render/Docker con Apache |

### 1. Migrar la base de datos a Supabase

```bash
php database/exportar_a_supabase.php
```

Genera `database/backups/migracion_supabase_<fecha>.sql` (excluido de Git porque contiene datos personales). Abre Supabase > **SQL Editor**, pega el contenido y ejecútalo. Todo corre en una transacción; al final muestra el conteo de registros por tabla.

### 2. Crear el proyecto en Vercel

1. En Vercel: **Add New > Project**, importa este repositorio de GitHub. *Framework Preset*: **Other** (el resto lo toma de `vercel.json`).
2. En **Environment Variables** agrega:

| Variable | Valor |
|---|---|
| `APP_ENV` | `production` |
| `DB_DRIVER` | `pgsql` |
| `DB_HOST` | Supabase > Connect > **Session pooler** (`aws-…pooler.supabase.com`) |
| `DB_PORT` | `5432` |
| `DB_NAME` | `postgres` |
| `DB_USER` | `postgres.<id_del_proyecto>` |
| `DB_PASSWORD` | contraseña de la base de datos (secreta) |
| `DB_SCHEMA` | `transporte` |
| `DB_SSLMODE` | `require` |
| `SESSION_DRIVER` | `database` |

3. **Deploy**. Luego `https://<proyecto>.vercel.app/health` debe responder `{"status":"ok","database":"ok"}`.
4. Recomendado: en Settings > Functions, elige la región de Vercel más cercana a la región de Supabase.

### 3. Ajustar Supabase Auth

Supabase > Authentication > **URL Configuration**: define la URL de Vercel como *Site URL* y agrégala a *Redirect URLs*.

### Actualizar el sistema

Cada `git push` a la rama de producción del proyecto despliega automáticamente; las demás ramas generan *previews*. Los cambios de esquema se aplican con el SQL Editor de Supabase.

### Variables de entorno

| Variable | Descripción |
|---|---|
| `APP_ENV` | `local` o `production` |
| `DB_DRIVER` | `mysql` (XAMPP) o `pgsql` (Supabase) |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` | Conexión a la base de datos |
| `DB_SCHEMA` | Esquema de PostgreSQL (`transporte`) |
| `DB_SSLMODE` | TLS hacia la base de datos (`require` en producción) |
| `SESSION_DRIVER` | `files` (XAMPP/Docker) o `database` (Vercel) |
| `SUPABASE_URL`, `SUPABASE_PUBLISHABLE_KEY` | Opcionales; sobrescriben `config/supabase.php` |

Nunca uses la clave `service_role` de Supabase en esta aplicación ni la subas al repositorio.

## Comprobacion por consola

Desde la raiz del proyecto se puede revisar la sintaxis con:

```bash
php -l config/database.php
php -l public/index.php
```
