<?php
// config/config.php · Configuración de la aplicación y de la API
// Los valores se leen del archivo .env para NO escribir contraseñas en el código.

// Lee el archivo .env línea por línea y guarda cada NOMBRE=valor en $_ENV
function cargarEnv(string $ruta): void
{
    if (!is_file($ruta)) {
        return;   // si no hay .env se usan los valores por defecto
    }
    foreach (file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $linea = trim($linea);
        if ($linea === '' || str_starts_with($linea, '#') || !str_contains($linea, '=')) {
            continue;   // salta comentarios y líneas sin =
        }
        [$nombre, $valor] = explode('=', $linea, 2);
        $_ENV[trim($nombre)] = trim($valor);
    }
}

// Devuelve el valor de una variable del .env (o un valor por defecto)
function env(string $nombre, string $porDefecto = ''): string
{
    return $_ENV[$nombre] ?? $porDefecto;
}

cargarEnv(dirname(__DIR__) . '/.env');

// Constantes: MAYÚSCULAS_CON_GUION_BAJO
define('APP_ENV', env('APP_ENV', 'desarrollo'));
define('APP_DEBUG', env('APP_DEBUG', 'false') === 'true');

define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'papeleria'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', 'utf8mb4');

define('CORS_ORIGEN', env('CORS_ORIGEN', '*'));
define('NOMBRE_APP', 'Papelería El Estudiante');
