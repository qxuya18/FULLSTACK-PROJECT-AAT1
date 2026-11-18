<?php
// index.php · Punto de entrada (Front Controller): TODAS las peticiones pasan por aquí
// Ejemplo de URL: index.php?c=productos&a=editar&id=3
declare(strict_types=1);
session_start();

define('RUTA_APP', __DIR__ . '/app');
require __DIR__ . '/config/config.php';
require RUTA_APP . '/helpers.php';

// Autocarga: cuando el código usa una clase, PHP busca su archivo aquí
spl_autoload_register(function (string $clase): void {
    foreach (['core', 'models', 'controllers'] as $carpeta) {
        $archivo = RUTA_APP . "/$carpeta/$clase.php";
        if (file_exists($archivo)) {
            require $archivo;
            return;
        }
    }
});

// Tabla de rutas: nombre en la URL → clase del controlador
$rutas = [
    'inicio'    => 'InicioController',
    'productos' => 'ProductoController',
    'clientes'  => 'ClienteController',
    'ventas'    => 'VentaController',
];

$nombreControlador = $_GET['c'] ?? 'inicio';
$accion = $_GET['a'] ?? 'index';

if (!isset($rutas[$nombreControlador])) {
    http_response_code(404);
    exit('Página no encontrada');
}

// class_exists usa la autocarga: si el archivo todavía no existe, avisa
if (!class_exists($rutas[$nombreControlador])) {
    exit('Página en construcción: falta crear ' . $rutas[$nombreControlador] . '.php');
}

$controlador = new $rutas[$nombreControlador]();

// Solo se pueden llamar métodos públicos que existan
if (!is_callable([$controlador, $accion])) {
    http_response_code(404);
    exit('Acción no encontrada');
}

try {
    $controlador->$accion();
} catch (PDOException $error) {
    http_response_code(500);
    echo '<h1>Error de base de datos</h1><p>' . e($error->getMessage()) . '</p>';
}
