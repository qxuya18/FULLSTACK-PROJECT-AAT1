<?php
// app/core/Database.php · Una sola conexión PDO para toda la aplicación
class Database
{
    // static: pertenece a la clase, no a cada objeto. Se comparte.
    private static ?PDO $conexion = null;

    public static function conectar(): PDO
    {
        // Si todavía no existe la conexión, se crea (solo la primera vez)
        if (self::$conexion === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

            self::$conexion = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // errores = excepciones
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // filas como arrays asociativos
                PDO::ATTR_EMULATE_PREPARES   => false,                  // consultas preparadas reales
            ]);
        }
        return self::$conexion;
    }
}
