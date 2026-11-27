<?php

$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
$appBasePath = rtrim(str_replace('\\', '/', dirname($scriptPath)), '/');
if ($appBasePath === '.' || $appBasePath === '/') {
    $appBasePath = '';
}

$originalRequestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$documentRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
$scriptFilename = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
if ($appBasePath === '' && $documentRoot !== '') {
    $documentRootPrefix = $documentRoot . '/';
    if (stripos($scriptFilename, $documentRootPrefix) === 0) {
        $relativeScriptPath = substr($scriptFilename, strlen($documentRoot));
        $appBasePath = rtrim(str_replace('\\', '/', dirname($relativeScriptPath)), '/');
        if ($appBasePath === '.' || $appBasePath === '/') {
            $appBasePath = '';
        }
    }
}

if ($appBasePath === '') {
    $publicPosition = strrpos((string) $originalRequestPath, '/public');
    if ($publicPosition !== false) {
        $afterPublic = substr((string) $originalRequestPath, $publicPosition + strlen('/public'));
        if ($afterPublic === '' || $afterPublic[0] === '/') {
            $appBasePath = substr((string) $originalRequestPath, 0, $publicPosition + strlen('/public'));
        }
    }
}

if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', $appBasePath);
}

if (!function_exists('app_url')) {
    function app_url($path = '')
    {
        return APP_BASE_PATH . '/' . ltrim((string) $path, '/');
    }
}

$requestUri = $originalRequestPath;
if ($appBasePath !== '' && strpos($requestUri, $appBasePath) === 0) {
    $requestUri = substr($requestUri, strlen($appBasePath));
}
$uri = '/' . ltrim((string) $requestUri, '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Comprobación de estado para la plataforma de hosting: no requiere sesión
// y no devuelve datos, solo si la aplicación y la base de datos responden.
if ($uri === '/health' && ($method === 'GET' || $method === 'HEAD')) {
    require_once __DIR__ . '/../config/database.php';
    $conexion->query('SELECT 1');
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(['status' => 'ok', 'database' => 'ok']);
    exit;
}

// En la nube el HTTPS termina en el proxy de la plataforma, que informa el
// protocolo original con X-Forwarded-Proto.
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => $appBasePath !== '' ? $appBasePath : '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);

// En serverless (Vercel) las sesiones se guardan en PostgreSQL; en XAMPP o
// Docker siguen usando archivos, como siempre.
require_once __DIR__ . '/../config/env.php';
if (app_env('SESSION_DRIVER', 'files') === 'database') {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../apps/middleware/SesionBaseDatos.php';
    if ($conexion->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
        error_log('SESSION_DRIVER=database requiere DB_DRIVER=pgsql.');
        http_response_code(500);
        exit('La configuración del servidor no es válida.');
    }
    session_set_save_handler(new SesionBaseDatos($conexion, (int) ini_get('session.gc_maxlifetime')), true);
}
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../apps/middleware/Csrf.php';
require_once __DIR__ . '/../apps/middleware/Autenticacion.php';
$autenticacion = new Autenticacion(new Usuario($conexion));

$destroySession = static function () {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $cookie = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $cookie['path'],
            'domain' => $cookie['domain'],
            'secure' => $cookie['secure'],
            'httponly' => $cookie['httponly'],
            'samesite' => 'Lax',
        ]);
    }
    session_destroy();
};

$redirectToLogin = static function ($message = '', $email = '') {
    if ($message !== '') {
        $_SESSION['login_error'] = $message;
        $_SESSION['login_email'] = $email;
    }
    header('Location: ' . app_url('/login'), true, 303);
    exit;
};

// Todo formulario POST debe incluir el token CSRF de la sesión.
if ($method === 'POST' && !csrf_valid()) {
    if ($uri === '/login') {
        $redirectToLogin('Tu sesión de inicio expiró. Vuelve a intentarlo.');
    }
    http_response_code(419);
    exit('El formulario expiró o no es válido. Vuelve atrás, recarga la página e inténtalo de nuevo.');
}

switch ($uri) {
    case '/login':
        if ($method === 'GET') {
            if ($autenticacion->usuarioActual() !== null) {
                header('Location: ' . app_url('/'));
                exit;
            }

            $loginError = is_string($_SESSION['login_error'] ?? null) ? $_SESSION['login_error'] : '';
            $loginEmail = is_string($_SESSION['login_email'] ?? null) ? $_SESSION['login_email'] : '';
            unset($_SESSION['login_error'], $_SESSION['login_email']);

            header('Cache-Control: no-store');
            require __DIR__ . '/../apps/views/auth/login.php';
            exit;
        }

        if ($method === 'POST') {
            $email = strtolower(trim((string) ($_POST['correo'] ?? '')));
            $password = (string) ($_POST['contrasena'] ?? '');

            // Límite por sesión: 5 intentos fallidos cada 15 minutos. Además,
            // cada cuenta se bloquea 15 minutos tras 5 fallos (ver Autenticacion).
            $now = time();
            $attempts = array_values(array_filter(
                (array) ($_SESSION['login_attempts'] ?? []),
                static function ($time) use ($now) {
                    return is_int($time) && $time > $now - 900;
                }
            ));
            if (count($attempts) >= 5) {
                $redirectToLogin('Demasiados intentos fallidos. Espera 15 minutos e inténtalo de nuevo.', $email);
            }

            if (
                strlen($email) > 254
                || filter_var($email, FILTER_VALIDATE_EMAIL) === false
                || $password === ''
                || strlen($password) > 72
            ) {
                $_SESSION['login_attempts'] = array_merge($attempts, [$now]);
                $redirectToLogin('Correo o contraseña incorrectos.', $email);
            }

            try {
                $result = $autenticacion->iniciarSesion($email, $password);
            } catch (PDOException $e) {
                error_log('Error en el inicio de sesión: ' . $e->getMessage());
                $redirectToLogin('No fue posible iniciar sesión en este momento. Intenta de nuevo.', $email);
            }
            unset($password);

            if ($result['ok']) {
                unset($_SESSION['login_attempts'], $_SESSION['login_error'], $_SESSION['login_email']);
                header('Location: ' . app_url('/'), true, 303);
                exit;
            }

            $_SESSION['login_attempts'] = array_merge($attempts, [$now]);
            if ($result['motivo'] === 'bloqueado') {
                $redirectToLogin('Por seguridad, la cuenta está bloqueada temporalmente. Espera 15 minutos e inténtalo de nuevo.', $email);
            }
            if ($result['motivo'] === 'inactivo') {
                $redirectToLogin('Tu usuario está desactivado. Contacta al administrador.', $email);
            }
            $redirectToLogin('Correo o contraseña incorrectos.', $email);
        }

        http_response_code(405);
        exit('Método no permitido.');

    case '/auth/logout':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }

        $destroySession();
        header('Location: ' . app_url('/login'), true, 303);
        exit;
}

// A partir de aquí todas las rutas son privadas.
$authUser = $autenticacion->usuarioActual();
if ($authUser === null) {
    $destroySession();
    header('Location: ' . app_url('/login'));
    exit;
}

// Las páginas privadas no se guardan en caché (evita verlas con "Atrás" tras cerrar sesión).
header('Cache-Control: no-store, private');

require_once __DIR__ . '/../apps/controllers/UsuarioController.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../apps/controllers/ClienteController.php';
require_once __DIR__ . '/../apps/controllers/ConductorController.php';
require_once __DIR__ . '/../apps/controllers/PasajeroController.php';
require_once __DIR__ . '/../apps/controllers/ServicioControoller.php';
require_once __DIR__ . '/../apps/controllers/DestinoController.php';
require_once __DIR__ . '/../apps/controllers/VehiculoController.php';

switch ($uri) {
    case '/':
        if ($method === 'GET') {
            $clientes = (new Cliente($conexion))->obtenerTodos();
            $conductores = (new Conductor($conexion))->obtenerTodos();
            $pasajeros = (new Pasajero($conexion))->obtenerTodos();
            $servicios = (new Servicio($conexion))->obtenerTodos();
            $destinos = (new Destino($conexion))->obtenerTodos();
            $vehiculos = (new Vehiculo($conexion))->obtenerTodos();
            $usuarios = (new Usuario($conexion))->obtenerTodos();

            $mensajes = [
                'creado' => 'Registro creado correctamente.',
                'editado' => 'Registro actualizado correctamente.',
                'eliminado' => 'Registro eliminado correctamente.',
                'error' => 'No se pudo completar la operación. Verifica los datos y las relaciones.',
                'bloqueado' => 'Este servicio está finalizado y ya no se puede editar.',
            ];
            $codigoMensaje = is_string($_GET['msg'] ?? null) ? $_GET['msg'] : '';
            $mensaje = $mensajes[$codigoMensaje] ?? '';
            if ($codigoMensaje === 'error' && !empty($_SESSION['flash_error']) && is_string($_SESSION['flash_error'])) {
                $mensaje = $_SESSION['flash_error'];
            }
            unset($_SESSION['flash_error']);
            $mensajeEsError = in_array($codigoMensaje, ['error', 'bloqueado'], true);

            $rutasCrear = [
                'clientes' => 'cliente',
                'conductores' => 'conductor',
                'pasajeros' => 'pasajero',
                'servicios' => 'servicio',
                'destinos' => 'destino',
                'vehiculos' => 'vehiculo',
                'usuarios' => 'usuario',
            ];

            $tablas = [
                [
                    'titulo' => 'Clientes',
                    'ruta' => 'cliente',
                    'clave' => 'clientes',
                    'registros' => $clientes,
                    'columnas' => ['ID' => 'id', 'Nombre' => 'nombre', 'Documento' => 'documento', 'Correo' => 'correo', 'Teléfono' => 'telefono'],
                ],
                [
                    'titulo' => 'Conductores',
                    'ruta' => 'conductor',
                    'clave' => 'conductores',
                    'registros' => $conductores,
                    'columnas' => ['ID' => 'id', 'Nombre' => 'nombre', 'Documento' => 'documento', 'Licencia' => 'licencia', 'Teléfono' => 'telefono'],
                ],
                [
                    'titulo' => 'Pasajeros',
                    'ruta' => 'pasajero',
                    'clave' => 'pasajeros',
                    'registros' => $pasajeros,
                    'columnas' => ['ID' => 'id', 'Nombre' => 'nombre', 'Documento' => 'documento', 'Teléfono' => 'telefono', 'Cliente' => 'cliente'],
                ],
                [
                    'titulo' => 'Servicios',
                    'ruta' => 'servicio',
                    'clave' => 'servicios',
                    'registros' => $servicios,
                    'columnas' => ['ID' => 'id', 'Cliente' => 'cliente', 'Pasajero' => 'pasajero', 'Conductor' => 'conductor', 'Tipo de vehículo' => 'tipo_vehiculo', 'Placa' => 'placa', 'Destino' => 'destino', 'Salida' => 'salida', 'Llegada' => 'llegada', 'Estado' => 'estado'],
                ],
                [
                    'titulo' => 'Destinos',
                    'ruta' => 'destino',
                    'clave' => 'destinos',
                    'registros' => $destinos,
                    'columnas' => ['ID' => 'id', 'Origen' => 'ciudad_origen', 'Destino' => 'ciudad_destino', 'Dirección' => 'direccion_destino', 'Tarifa' => 'tarifa'],
                ],
                [
                    'titulo' => 'Vehículos',
                    'ruta' => 'vehiculo',
                    'clave' => 'vehiculos',
                    'registros' => $vehiculos,
                    'columnas' => ['ID' => 'id', 'Placa' => 'placa', 'Tipo' => 'tipo', 'Marca' => 'marca', 'Modelo' => 'modelo', 'Capacidad' => 'capacidad', 'Conductor' => 'conductor'],
                ],
                [
                    'titulo' => 'Usuarios',
                    'ruta' => 'usuario',
                    'clave' => 'usuarios',
                    'registros' => $usuarios,
                    'columnas' => ['ID' => 'id', 'Nombre' => 'nombre', 'Correo' => 'correo', 'Estado' => 'estado', 'Último acceso' => 'ultimo_acceso_legible'],
                ],
            ];

            $selectedTable = $_GET['tabla'] ?? 'todos';
            $validTables = array_merge(['todos'], array_keys($rutasCrear));
            if (!in_array($selectedTable, $validTables, true)) {
                $selectedTable = 'todos';
            }
            $visibleTables = $selectedTable === 'todos'
                ? $tablas
                : array_values(array_filter($tablas, static function ($table) use ($selectedTable) {
                    return $table['clave'] === $selectedTable;
                }));

            require __DIR__ . '/../apps/views/index.php';
            exit;
        }
        http_response_code(404);
        exit('Página no encontrada.');

    case '/cliente/editar':
        if ($method !== 'GET') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new ClienteController($conexion))->editar();
        exit;

    case '/cliente/actualizar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new ClienteController($conexion))->actualizar();
        exit;

    case '/cliente/eliminar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new ClienteController($conexion))->eliminar();
        exit;

    case '/conductor/editar':
        if ($method !== 'GET') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new ConductorController($conexion))->editar();
        exit;

    case '/conductor/actualizar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new ConductorController($conexion))->actualizar();
        exit;

    case '/conductor/eliminar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new ConductorController($conexion))->eliminar();
        exit;

    case '/pasajero/editar':
        if ($method !== 'GET') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new PasajeroController($conexion))->editar();
        exit;

    case '/pasajero/actualizar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new PasajeroController($conexion))->actualizar();
        exit;

    case '/pasajero/eliminar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new PasajeroController($conexion))->eliminar();
        exit;

    case '/servicio/editar':
        if ($method !== 'GET') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new ServicioController($conexion))->editar();
        exit;

    case '/servicio/actualizar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new ServicioController($conexion))->actualizar();
        exit;

    case '/servicio/eliminar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new ServicioController($conexion))->eliminar();
        exit;

    case '/destino/editar':
        if ($method !== 'GET') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new DestinoController($conexion))->editar();
        exit;

    case '/destino/actualizar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new DestinoController($conexion))->actualizar();
        exit;

    case '/destino/eliminar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new DestinoController($conexion))->eliminar();
        exit;

    case '/vehiculo/editar':
        if ($method !== 'GET') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new VehiculoController($conexion))->editar();
        exit;

    case '/vehiculo/actualizar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new VehiculoController($conexion))->actualizar();
        exit;

    case '/vehiculo/eliminar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new VehiculoController($conexion))->eliminar();
        exit;

    case '/usuario/editar':
        if ($method !== 'GET') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new UsuarioController($conexion, $authUser['id']))->editar();
        exit;

    case '/usuario/actualizar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new UsuarioController($conexion, $authUser['id']))->actualizar();
        exit;

    case '/usuario/eliminar':
        if ($method !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }
        (new UsuarioController($conexion, $authUser['id']))->eliminar();
        exit;

    case '/usuario/crear':
        $controladorClase = UsuarioController::class;
        break;

    case '/cliente/crear':
        $controladorClase = ClienteController::class;
        break;

    case '/conductor/crear':
        $controladorClase = ConductorController::class;
        break;

    case '/pasajero/crear':
        $controladorClase = PasajeroController::class;
        break;

    case '/servicio/crear':
        $controladorClase = ServicioController::class;
        break;

    case '/destino/crear':
        $controladorClase = DestinoController::class;
        break;

    case '/vehiculo/crear':
        $controladorClase = VehiculoController::class;
        break;

    default:
        http_response_code(404);
        exit('Página no encontrada.');
}

if ($method !== 'GET' && $method !== 'POST') {
        http_response_code(405);
        exit('Método no permitido.');
}

$controlador = new $controladorClase($conexion);
if ($method === 'GET') {
        $controlador->crear();
        exit;
}

$ok = $controlador->guardar();
header('Location: ' . app_url('/?msg=' . ($ok ? 'creado' : 'error')));
exit;
