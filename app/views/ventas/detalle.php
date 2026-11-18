<h1>Venta #<?= e($idVenta) ?></h1>
<p>
  <strong>Cliente:</strong> <?= e($lineas[0]['cliente']) ?> ·
  <strong>Atendió:</strong> <?= e($lineas[0]['empleado'] ?? 'Sin asignar') ?> ·
  <strong>Fecha:</strong> <?= e(date('d/m/Y H:i', strtotime($lineas[0]['fecha']))) ?>
</p>

<table>
  <tr><th>Producto</th><th class="derecha">Cantidad</th><th class="derecha">Precio</th><th class="derecha">Subtotal</th></tr>
  <?php foreach ($lineas as $linea): ?>
    <tr>
      <td><?= e($linea['producto']) ?></td>
      <td class="derecha"><?= e($linea['cantidad']) ?></td>
      <td class="derecha"><?= quetzales($linea['precio_unitario']) ?></td>
      <td class="derecha"><?= quetzales($linea['subtotal']) ?></td>
    </tr>
  <?php endforeach; ?>
  <tr>
    <th colspan="3" class="derecha">TOTAL</th>
    <th class="derecha"><?= quetzales($total) ?></th>
  </tr>
</table>
<p><a class="boton secundario" href="index.php?c=ventas">← Volver a ventas</a></p>
