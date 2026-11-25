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

## Configuración de Supabase Auth

- La URL del proyecto y la clave pública se configuran en `config/supabase.php` y `public/js/auth/config.js`.
- PHP verifica el token de acceso con Supabase antes de permitir el acceso a las rutas protegidas. La sesión de la aplicación usa una cookie `HttpOnly`.
- Crea usuarios de prueba desde el panel de Supabase Auth. No se necesita publicar ni copiar una clave `service_role`; nunca debe agregarse al navegador ni al repositorio.
- El login requiere conexión con Supabase y la extensión cURL habilitada en PHP.

## Despliegue en producción (Render + Supabase)

Arquitectura: **Render** (plan Free, contenedor Docker con PHP 8.2 + Apache) ejecuta la aplicación; **Supabase** aporta la base de datos PostgreSQL (esquema privado `transporte`) y la autenticación. En local se sigue usando XAMPP/MariaDB sin cambios (`DB_DRIVER=mysql`).

Archivos de infraestructura:

| Archivo | Función |
|---|---|
| `Dockerfile` | Imagen PHP 8.2 + Apache con `pdo_pgsql`, `pdo_mysql` y cURL |
| `docker/` | Configuración de Apache (raíz `public/`, cabeceras de seguridad), PHP de producción y arranque |
| `render.yaml` | Blueprint de Render (servicio, variables de entorno, `/health`) |
| `database/schema.pgsql.sql` | Esquema PostgreSQL equivalente al de MySQL (PK, UNIQUE, FK, RLS) |
| `database/exportar_a_supabase.php` | Genera el SQL de migración con los datos de MySQL local |

### 1. Migrar la base de datos a Supabase

```bash
php database/exportar_a_supabase.php
```

Genera `database/backups/migracion_supabase_<fecha>.sql` (excluido de Git porque contiene datos personales). Abre Supabase > **SQL Editor**, pega el contenido y ejecútalo. Todo corre en una transacción; al final muestra el conteo de registros por tabla.

### 2. Crear el servicio en Render

1. En Render: **New > Blueprint**, conecta este repositorio de GitHub y selecciona la rama.
2. Render lee `render.yaml` y pide los valores secretos. Tómalos de Supabase > **Connect > Session pooler**:
   - `DB_HOST`: `aws-0-<region>.pooler.supabase.com`
   - `DB_USER`: `postgres.<id_del_proyecto>`
   - `DB_PASSWORD`: la contraseña de la base de datos (Project Settings > Database)
3. Al terminar el despliegue, `https://<servicio>.onrender.com/health` debe responder `{"status":"ok","database":"ok"}`.

### 3. Ajustar Supabase Auth

Supabase > Authentication > **URL Configuration**: define la URL de Render como *Site URL* y agrégala a *Redirect URLs*.

### Actualizar el sistema

Cada `git push` a la rama configurada vuelve a desplegar automáticamente. Los cambios de esquema se aplican con el SQL Editor de Supabase.

### Variables de entorno

| Variable | Producción | Descripción |
|---|---|---|
| `APP_ENV` | `production` | Entorno (`local` o `production`) |
| `DB_DRIVER` | `pgsql` | `mysql` (XAMPP) o `pgsql` (Supabase) |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` | secreto | Conexión a la base de datos |
| `DB_SCHEMA` | `transporte` | Esquema de PostgreSQL |
| `DB_SSLMODE` | `require` | TLS hacia la base de datos |
| `SUPABASE_URL`, `SUPABASE_PUBLISHABLE_KEY` | opcional | Sobrescriben `config/supabase.php` |

Nunca uses la clave `service_role` de Supabase en esta aplicación ni la subas al repositorio.

## Comprobacion por consola

Desde la raiz del proyecto se puede revisar la sintaxis con:

```bash
php -l config/database.php
php -l public/index.php
```
