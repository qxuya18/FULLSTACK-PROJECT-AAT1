<h1>Registrar venta</h1>

<?php if (!empty($errores)): ?>
  <ul class="errores">
    <?php foreach ($errores as $error): ?>
      <li><?= e($error) ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<form class="formulario" method="POST" action="index.php?c=ventas&a=guardar">
  <label for="id_cliente">Cliente</label>
  <select id="id_cliente" name="id_cliente" required>
    <option value="">-- Selecciona --</option>
    <?php foreach ($clientes as $cliente): ?>
      <option value="<?= e($cliente['id_cliente']) ?>"
        <?= ($datos['id_cliente'] ?? 0) == $cliente['id_cliente'] ? 'selected' : '' ?>>
        <?= e($cliente['nombre']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <label for="id_empleado">Atendido por</label>
  <select id="id_empleado" name="id_empleado" required>
    <option value="">-- Selecciona --</option>
    <?php foreach ($empleados as $empleado): ?>
      <option value="<?= e($empleado['id_empleado']) ?>"
        <?= ($datos['id_empleado'] ?? 0) == $empleado['id_empleado'] ? 'selected' : '' ?>>
        <?= e($empleado['nombre']) ?> (<?= e($empleado['puesto']) ?>)
      </option>
    <?php endforeach; ?>
  </select>

  <label for="id_producto">Producto</label>
  <select id="id_producto" name="id_producto" required>
    <option value="">-- Selecciona --</option>
    <?php foreach ($productos as $producto): ?>
      <option value="<?= e($producto['id_producto']) ?>"
        <?= ($datos['id_producto'] ?? 0) == $producto['id_producto'] ? 'selected' : '' ?>>
        <?= e($producto['nombre']) ?> · <?= quetzales($producto['precio']) ?> · quedan <?= e($producto['stock']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <label for="cantidad">Cantidad</label>
  <input id="cantidad" name="cantidad" type="number" min="1" required value="<?= e($datos['cantidad'] ?? 1) ?>">

  <p>
    <button class="boton">Registrar venta</button>
    <a class="boton secundario" href="index.php?c=ventas">Cancelar</a>
  </p>
</form>
