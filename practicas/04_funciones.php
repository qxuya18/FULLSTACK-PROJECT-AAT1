<?php
// 04_funciones.php · AHD3 · Funciones y manejo de excepciones
declare(strict_types=1);   // PHP respeta los tipos declarados

// Función con valor por defecto (igual que fn_precio_descuento de la AHD2)
function aplicarDescuento(float $precio, int $porcentaje = 10): float
{
    return round($precio - ($precio * $porcentaje / 100), 2);
}

// Función que valida y LANZA una excepción si algo está mal
function calcularSubtotal(int $cantidad, float $precioUnitario, int $stock): float
{
    if ($cantidad <= 0) {
        throw new InvalidArgumentException('La cantidad debe ser mayor que cero');
    }
    if ($cantidad > $stock) {
        throw new RuntimeException("No hay suficiente stock (solo hay $stock)");
    }
    return $cantidad * $precioUnitario;
}

echo '<p>Cuaderno con 15 %: Q' . aplicarDescuento(18.50, 15) . '</p>';   // 15.73
echo '<p>Cuaderno con 10 %: Q' . aplicarDescuento(18.50) . '</p>';       // 16.65

// try / catch / finally: intentar, atrapar el error y continuar
$pedidos = [2, 0, 50];   // cantidades que piden tres clientes
foreach ($pedidos as $cantidad) {
    try {
        $subtotal = calcularSubtotal($cantidad, 120.00, 6);
        echo "<p>✅ $cantidad calculadoras: Q" . number_format($subtotal, 2) . "</p>";
    } catch (InvalidArgumentException | RuntimeException $error) {
        echo "<p>❌ Pedido de $cantidad: " . $error->getMessage() . '</p>';
    } finally {
        echo '<hr>';      // se ejecuta siempre, haya error o no
    }
}
