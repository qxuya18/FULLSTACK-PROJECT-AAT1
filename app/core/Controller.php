<?php
// app/core/Controller.php · Clase base: todos los controladores heredan de ella
abstract class Controller
{
    // Muestra una vista dentro de la plantilla (encabezado + vista + pie)
    protected function render(string $vista, array $datos = []): void
    {
        extract($datos);   // ['productos' => [...]] se convierte en $productos
        require RUTA_APP . '/views/layout/header.php';
        require RUTA_APP . '/views/' . $vista . '.php';
        require RUTA_APP . '/views/layout/footer.php';
    }

    // Envía al navegador a otra página (patrón POST → Redirect → GET)
    protected function redirigir(string $controlador, string $accion = 'index'): void
    {
        header("Location: index.php?c=$controlador&a=$accion");
        exit;
    }

    // Guarda un mensaje para mostrarlo en la siguiente página
    protected function guardarMensaje(string $tipo, string $texto): void
    {
        $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
    }

    // Lee un número entero que llegó por GET o POST (0 si no es válido)
    protected function leerEntero(array $origen, string $clave): int
    {
        return (int) filter_var($origen[$clave] ?? 0, FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
    }
}
