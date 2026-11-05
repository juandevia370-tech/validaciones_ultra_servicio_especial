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

session_set_cookie_params([
    'lifetime' => 0,
    'path' => $appBasePath !== '' ? $appBasePath : '/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/../apps/middleware/SupabaseAuth.php';
$supabaseAuth = new SupabaseAuth(require __DIR__ . '/../config/supabase.php');

$sendJson = static function ($status, array $payload) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload);
    exit;
};

if ($uri === '/login' && $method === 'GET') {
    if (!empty($_SESSION['supabase_access_token'])) {
        $result = $supabaseAuth->verify($_SESSION['supabase_access_token']);
        if ($result['status'] === 200) {
            header('Location: ' . app_url('/'));
            exit;
        }
    }

    require __DIR__ . '/../apps/views/auth/login.php';
    exit;
}

if ($uri === '/auth/session' && $method === 'POST') {
    $payload = json_decode(file_get_contents('php://input'), true);
    $accessToken = is_array($payload) ? ($payload['access_token'] ?? '') : '';
    $refreshToken = is_array($payload) ? ($payload['refresh_token'] ?? '') : '';

    if (!is_string($accessToken) || $accessToken === '' || strlen($accessToken) > 8192) {
        $sendJson(400, ['message' => 'La sesión recibida no es válida.']);
    }

    $result = $supabaseAuth->verify($accessToken);
    if ($result['status'] !== 200 || empty($result['data']['id'])) {
        $status = $result['status'] === 503 ? 503 : 401;
        $message = $status === 503
            ? 'El servicio de autenticación no está disponible. Intenta de nuevo.'
            : 'No se pudo validar la sesión con Supabase.';
        $sendJson($status, ['message' => $message]);
    }

    session_regenerate_id(true);
    $_SESSION['supabase_access_token'] = $accessToken;
    $_SESSION['supabase_refresh_token'] = is_string($refreshToken) ? $refreshToken : '';
    $_SESSION['supabase_user'] = $result['data'];

    $sendJson(200, ['message' => 'Sesión verificada.']);
}

if ($uri === '/auth/logout' && $method === 'POST') {
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
    $sendJson(200, ['message' => 'Sesión cerrada.']);
}

$isLoginRoute = $uri === '/login' && $method === 'GET';
$isSessionRoute = $uri === '/auth/session' || $uri === '/auth/logout';
if (!$isLoginRoute && !$isSessionRoute) {
    $accessToken = $_SESSION['supabase_access_token'] ?? '';
    $authResult = $accessToken !== ''
        ? $supabaseAuth->verify($accessToken)
        : ['status' => 401, 'data' => null];

    if ($authResult['status'] === 503) {
        http_response_code(503);
        exit('No se pudo conectar con Supabase para verificar la sesión. Intenta de nuevo.');
    }

    if ($authResult['status'] === 401 && !empty($_SESSION['supabase_refresh_token'])) {
        $refreshResult = $supabaseAuth->refresh($_SESSION['supabase_refresh_token']);
        if ($refreshResult['status'] === 200 && !empty($refreshResult['data']['access_token'])) {
            $authResult = $supabaseAuth->verify($refreshResult['data']['access_token']);
            if ($authResult['status'] === 200) {
                $_SESSION['supabase_access_token'] = $refreshResult['data']['access_token'];
                $_SESSION['supabase_refresh_token'] = $refreshResult['data']['refresh_token'] ?? $_SESSION['supabase_refresh_token'];
            }
        } elseif ($refreshResult['status'] === 503) {
            http_response_code(503);
            exit('No se pudo conectar con Supabase para verificar la sesión. Intenta de nuevo.');
        }
    }

    if ($authResult['status'] === 503) {
        http_response_code(503);
        exit('No se pudo conectar con Supabase para verificar la sesión. Intenta de nuevo.');
    }

    if ($authResult['status'] !== 200 || empty($authResult['data']['id'])) {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . app_url('/login'));
        exit;
    }

    $_SESSION['supabase_user'] = $authResult['data'];
    $authUser = $authResult['data'];
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../apps/controllers/ClienteController.php';
require_once __DIR__ . '/../apps/controllers/ConductorController.php';
require_once __DIR__ . '/../apps/controllers/PasajeroController.php';
require_once __DIR__ . '/../apps/controllers/ServicioControoller.php';
require_once __DIR__ . '/../apps/controllers/DestinoController.php';
require_once __DIR__ . '/../apps/controllers/VehiculoController.php';

if ($method === 'GET' && $uri === '/') {
    $clientes = (new Cliente($conexion))->obtenerTodos();
    $conductores = (new Conductor($conexion))->obtenerTodos();
    $pasajeros = (new Pasajero($conexion))->obtenerTodos();
    $servicios = (new Servicio($conexion))->obtenerTodos();
    $destinos = (new Destino($conexion))->obtenerTodos();
    $vehiculos = (new Vehiculo($conexion))->obtenerTodos();

    $mensajes = [
        'creado' => 'Registro creado correctamente.',
        'editado' => 'Registro actualizado correctamente.',
        'eliminado' => 'Registro eliminado correctamente.',
        'error' => 'No se pudo completar la operación. Verifica los datos y las relaciones.',
    ];
    $mensaje = $mensajes[$_GET['msg'] ?? ''] ?? '';

    $rutasCrear = [
        'clientes' => 'cliente',
        'conductores' => 'conductor',
        'pasajeros' => 'pasajero',
        'servicios' => 'servicio',
        'destinos' => 'destino',
        'vehiculos' => 'vehiculo',
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
            'columnas' => ['ID' => 'id', 'Cliente' => 'cliente', 'Pasajero' => 'pasajero', 'Conductor' => 'conductor', 'Vehículo' => 'vehiculo', 'Destino' => 'destino', 'Fecha' => 'fecha_servicio', 'Hora' => 'hora_servicio', 'Estado' => 'estado'],
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
            'columnas' => ['ID' => 'id', 'Placa' => 'placa', 'Marca' => 'marca', 'Modelo' => 'modelo', 'Capacidad' => 'capacidad', 'Conductor' => 'conductor'],
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

$rutasEdicion = [
    '/cliente/editar' => [ClienteController::class, 'editar', 'GET'],
    '/cliente/actualizar' => [ClienteController::class, 'actualizar', 'POST'],
    '/cliente/eliminar' => [ClienteController::class, 'eliminar', 'POST'],
    '/conductor/editar' => [ConductorController::class, 'editar', 'GET'],
    '/conductor/actualizar' => [ConductorController::class, 'actualizar', 'POST'],
    '/conductor/eliminar' => [ConductorController::class, 'eliminar', 'POST'],
    '/pasajero/editar' => [PasajeroController::class, 'editar', 'GET'],
    '/pasajero/actualizar' => [PasajeroController::class, 'actualizar', 'POST'],
    '/pasajero/eliminar' => [PasajeroController::class, 'eliminar', 'POST'],
    '/servicio/editar' => [ServicioController::class, 'editar', 'GET'],
    '/servicio/actualizar' => [ServicioController::class, 'actualizar', 'POST'],
    '/servicio/eliminar' => [ServicioController::class, 'eliminar', 'POST'],
    '/destino/editar' => [DestinoController::class, 'editar', 'GET'],
    '/destino/actualizar' => [DestinoController::class, 'actualizar', 'POST'],
    '/destino/eliminar' => [DestinoController::class, 'eliminar', 'POST'],
    '/vehiculo/editar' => [VehiculoController::class, 'editar', 'GET'],
    '/vehiculo/actualizar' => [VehiculoController::class, 'actualizar', 'POST'],
    '/vehiculo/eliminar' => [VehiculoController::class, 'eliminar', 'POST'],
];

if (isset($rutasEdicion[$uri])) {
    [$controladorClase, $accion, $metodoEsperado] = $rutasEdicion[$uri];
    if ($method !== $metodoEsperado) {
        http_response_code(405);
        exit('Método no permitido.');
    }

    (new $controladorClase($conexion))->$accion();
    exit;
}

$rutasCrear = [
    '/cliente/crear' => ClienteController::class,
    '/conductor/crear' => ConductorController::class,
    '/pasajero/crear' => PasajeroController::class,
    '/servicio/crear' => ServicioController::class,
    '/destino/crear' => DestinoController::class,
    '/vehiculo/crear' => VehiculoController::class,
];

if (!isset($rutasCrear[$uri])) {
    http_response_code(404);
    exit('Página no encontrada.');
}

if ($method !== 'GET' && $method !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$controlador = new $rutasCrear[$uri]($conexion);
if ($method === 'GET') {
    $controlador->crear();
    exit;
}

$ok = $controlador->guardar();
header('Location: ' . app_url('/?msg=' . ($ok ? 'creado' : 'error')));
exit;
