<?php
// 02_selectivas.php · AHD3 · Orientar el flujo con estructuras selectivas
$nombreProducto = 'Calculadora científica';
$stock = 6;
$idCategoria = 5;
$totalCompra = 240.00;

// 1. if / elseif / else: el mismo criterio que fn_estado_stock de la AHD2
if ($stock === 0) {
    $estado = 'Agotado';
} elseif ($stock < 15) {
    $estado = 'Bajo: pedir más';
} else {
    $estado = 'Suficiente';
}
echo "<p>$nombreProducto tiene $stock unidades: <strong>$estado</strong></p>";

// 2. match (PHP 8): devuelve un valor según el caso (compara con ===)
$categoria = match ($idCategoria) {
    1 => 'Cuadernos',
    2 => 'Escritura',
    3 => 'Arte',
    4 => 'Oficina',
    5 => 'Tecnología',
    default => 'Sin categoría',
};
echo "<p>Categoría: $categoria</p>";

// 3. switch: varias opciones que ejecutan acciones
$formaPago = 'tarjeta';
switch ($formaPago) {
    case 'efectivo':
        $recargo = 0;
        break;
    case 'tarjeta':
        $recargo = $totalCompra * 0.05;   // 5 % de comisión
        break;
    default:
        $recargo = 0;
}
echo '<p>Recargo por pago con ' . $formaPago . ': Q' . number_format($recargo, 2) . '</p>';

// 4. Operador ternario: un if corto en una sola línea
$tipoCliente = $totalCompra >= 200 ? 'Cliente mayorista' : 'Cliente minorista';
echo "<p>$tipoCliente</p>";

// 5. Operador ?? : valor por defecto si el dato no existe
$buscar = $_GET['buscar'] ?? 'sin búsqueda';
echo "<p>Búsqueda recibida: $buscar</p>";
