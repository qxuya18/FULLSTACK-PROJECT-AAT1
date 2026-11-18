<h1>Clientes</h1>
<p><a class="boton" href="index.php?c=clientes&a=crear">+ Nuevo cliente</a></p>

<table>
  <tr><th>Nombre</th><th>NIT</th><th>Teléfono</th><th>Correo</th><th class="derecha">Compras</th><th>Acciones</th></tr>
  <?php foreach ($clientes as $cliente): ?>
    <tr>
      <td><?= e($cliente['nombre']) ?></td>
      <td><?= e($cliente['nit']) ?></td>
      <td><?= e($cliente['telefono'] ?? '—') ?></td>
      <td><?= e($cliente['correo'] ?? '—') ?></td>
      <td class="derecha"><?= e($cliente['compras']) ?></td>
      <td class="acciones">
        <a class="boton secundario" href="index.php?c=clientes&a=editar&id=<?= e($cliente['id_cliente']) ?>">Editar</a>
        <form method="POST" action="index.php?c=clientes&a=eliminar"
              onsubmit="return confirm('¿Eliminar a <?= e($cliente['nombre']) ?>?');">
          <input type="hidden" name="id_cliente" value="<?= e($cliente['id_cliente']) ?>">
          <button class="boton peligro">Eliminar</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
