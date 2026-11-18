<h1>Productos</h1>

<form class="buscador" method="GET" action="index.php">
  <input type="hidden" name="c" value="productos">
  <input name="buscar" placeholder="Buscar por nombre o código" value="<?= e($buscar) ?>">
  <button class="boton">Buscar</button>
  <a class="boton secundario" href="index.php?c=productos">Limpiar</a>
  <a class="boton" href="index.php?c=productos&a=crear">+ Nuevo producto</a>
</form>

<table>
  <tr>
    <th>Código</th><th>Producto</th><th>Categoría</th>
    <th class="derecha">Precio</th><th class="derecha">Stock</th><th>Estado</th><th>Acciones</th>
  </tr>
  <?php foreach ($productos as $producto): ?>
    <tr>
      <td><?= e($producto['codigo']) ?></td>
      <td><?= e($producto['nombre']) ?></td>
      <td><?= e($producto['categoria']) ?></td>
      <td class="derecha"><?= quetzales($producto['precio']) ?></td>
      <td class="derecha"><?= e($producto['stock']) ?></td>
      <td class="estado-<?= e($producto['estado']) ?>"><?= e($producto['estado']) ?></td>
      <td class="acciones">
        <a class="boton secundario" href="index.php?c=productos&a=editar&id=<?= e($producto['id_producto']) ?>">Editar</a>
        <form method="POST" action="index.php?c=productos&a=eliminar"
              onsubmit="return confirm('¿Eliminar <?= e($producto['nombre']) ?>?');">
          <input type="hidden" name="id_producto" value="<?= e($producto['id_producto']) ?>">
          <button class="boton peligro">Eliminar</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<p><?= count($productos) ?> producto(s) encontrados.</p>
