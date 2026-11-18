<h1>Panel de la papelería</h1>

<div class="tarjetas">
  <div class="tarjeta">Ventas registradas <strong><?= e($resumen['ventas']) ?></strong></div>
  <div class="tarjeta">Total vendido <strong><?= quetzales($resumen['total_vendido']) ?></strong></div>
  <div class="tarjeta">Ticket promedio <strong><?= quetzales($resumen['ticket_promedio']) ?></strong></div>
</div>

<h2>⚠️ Productos con stock bajo</h2>
<?php if (empty($inventarioBajo)): ?>
  <p>Todo el inventario está en orden.</p>
<?php else: ?>
  <table>
    <tr><th>Código</th><th>Producto</th><th class="derecha">Stock</th></tr>
    <?php foreach ($inventarioBajo as $fila): ?>
      <tr>
        <td><?= e($fila['codigo']) ?></td>
        <td><?= e($fila['nombre']) ?></td>
        <td class="derecha"><?= e($fila['stock']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>
