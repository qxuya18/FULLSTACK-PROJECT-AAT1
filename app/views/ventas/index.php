<h1>Ventas</h1>
<p><a class="boton" href="index.php?c=ventas&a=crear">+ Registrar venta</a></p>

<table>
  <tr><th>#</th><th>Fecha</th><th>Cliente</th><th>Atendió</th><th class="derecha">Total</th><th></th></tr>
  <?php foreach ($ventas as $venta): ?>
    <tr>
      <td><?= e($venta['id_venta']) ?></td>
      <td><?= e(date('d/m/Y H:i', strtotime($venta['fecha']))) ?></td>
      <td><?= e($venta['cliente']) ?></td>
      <td><?= e($venta['empleado'] ?? 'Sin asignar') ?></td>
      <td class="derecha"><?= quetzales($venta['total']) ?></td>
      <td><a class="boton secundario" href="index.php?c=ventas&a=ver&id=<?= e($venta['id_venta']) ?>">Ver</a></td>
    </tr>
  <?php endforeach; ?>
</table>
