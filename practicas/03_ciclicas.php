<?php
// 03_ciclicas.php · AHD3 · Repetir acciones con estructuras cíclicas y arrays
// Array asociativo multidimensional: cada producto es un array (como una fila de la tabla)
$productos = [
    ['codigo' => 'CUA-001', 'nombre' => 'Cuaderno universitario 100 hojas', 'precio' => 20.00, 'stock' => 40],
    ['codigo' => 'ESC-001', 'nombre' => 'Lápiz HB',                          'precio' => 2.50,  'stock' => 200],
    ['codigo' => 'OFI-003', 'nombre' => 'Engrapadora mediana',               'precio' => 35.00, 'stock' => 10],
    ['codigo' => 'TEC-002', 'nombre' => 'Calculadora científica',            'precio' => 120.00, 'stock' => 6],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Estructuras cíclicas</title>
  <style>
    table { border-collapse: collapse; }
    th, td { border: 1px solid #999; padding: 4px 10px; }
  </style>
</head>
<body>
  <h2>1. foreach: recorrer cada producto</h2>
  <table>
    <tr><th>Código</th><th>Producto</th><th>Precio</th><th>Stock</th><th>Valor</th></tr>
    <?php
    $valorTotal = 0;                                   // acumulador
    foreach ($productos as $producto):
        $valor = $producto['precio'] * $producto['stock'];
        $valorTotal += $valor;
    ?>
      <tr>
        <td><?= $producto['codigo'] ?></td>
        <td><?= $producto['nombre'] ?></td>
        <td>Q<?= number_format($producto['precio'], 2) ?></td>
        <td><?= $producto['stock'] ?></td>
        <td>Q<?= number_format($valor, 2) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <p>Valor total del inventario: <strong>Q<?= number_format($valorTotal, 2) ?></strong></p>

  <h2>2. for: cuando sabes cuántas veces repetir</h2>
  <p>
    <?php for ($numero = 1; $numero <= 5; $numero++): ?>
      Ticket #<?= str_pad($numero, 4, '0', STR_PAD_LEFT) ?><br>
    <?php endfor; ?>
  </p>

  <h2>3. while: repetir mientras se cumpla una condición</h2>
  <?php
  $stockCalculadoras = 6;
  $ventas = 0;
  while ($stockCalculadoras >= 2) {      // cada cliente compra 2
      $stockCalculadoras -= 2;
      $ventas++;
  }
  echo "<p>Se pueden atender $ventas ventas de 2 calculadoras. Sobran $stockCalculadoras.</p>";
  ?>

  <h2>4. Funciones útiles de arrays</h2>
  <?php
  $nombres = array_column($productos, 'nombre');                     // solo una columna
  $bajos = array_filter($productos, fn($p) => $p['stock'] < 15);     // filtrar
  echo '<p>Productos: ' . implode(', ', $nombres) . '</p>';
  echo '<p>Con stock bajo: ' . count($bajos) . '</p>';
  ?>
</body>
</html>
