<?php
// api/index.php · Punto de entrada de la API REST (responde siempre en JSON)
// Ejemplos: GET /api/productos · GET /api/productos/3 · POST /api/ventas
declare(strict_types=1);

define('RUTA_APP', dirname(__DIR__) . '/app');
require dirname(__DIR__) . '/config/config.php';

// En producción no se muestran detalles de los errores
ini_set('display_errors', APP_DEBUG ? '1' : '0');

// Autocarga: core (Database, ApiController), models (AHD3) y api (controladores JSON)
spl_autoload_register(function (string $clase): void {
    foreach (['core', 'models', 'api'] as $carpeta) {
        $archivo = RUTA_APP . "/$carpeta/$clase.php";
        if (file_exists($archivo)) {
            require $archivo;
            return;
        }
    }
});

// Encabezados de la respuesta: JSON y permisos CORS para la página web
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . CORS_ORIGEN);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// El navegador pregunta primero con OPTIONS si puede usar la API (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Separar la ruta: "/productos/3" → recurso "productos" e id 3
$ruta = trim($_SERVER['PATH_INFO'] ?? '', '/');
$partes = $ruta === '' ? [] : explode('/', $ruta);
$recurso = $partes[0] ?? '';
$id = null;

if (isset($partes[1])) {
    if (!ctype_digit($partes[1]) || isset($partes[2])) {
        http_response_code(404);
        exit(json_encode(['error' => 'Ruta no encontrada'], JSON_UNESCAPED_UNICODE));
    }
    $id = (int) $partes[1];
}

// Tabla de rutas: recurso → controlador de la API
$rutas = [
    'categorias' => 'CategoriaApiController',
    'empleados'  => 'EmpleadoApiController',
    'productos'  => 'ProductoApiController',
    'clientes'   => 'ClienteApiController',
    'ventas'     => 'VentaApiController',
    'resumen'    => 'ResumenApiController',
];

// Sin recurso: la API se presenta y muestra sus rutas
if ($recurso === '') {
    echo json_encode([
        'api'     => 'API REST · ' . NOMBRE_APP,
        'version' => '1.0',
        'rutas'   => array_map(fn($r) => "/$r", array_keys($rutas)),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (!isset($rutas[$recurso])) {
    http_response_code(404);
    exit(json_encode(['error' => "El recurso '$recurso' no existe"], JSON_UNESCAPED_UNICODE));
}

try {
    $controlador = new $rutas[$recurso]();
    $controlador->atender($_SERVER['REQUEST_METHOD'], $id);
} catch (Throwable $error) {
    // Cualquier error inesperado: 500. El detalle solo se ve en desarrollo.
    http_response_code(500);
    echo json_encode([
        'error'   => 'Error interno del servidor',
        'detalle' => APP_DEBUG ? $error->getMessage() : null,
    ], JSON_UNESCAPED_UNICODE);
}
