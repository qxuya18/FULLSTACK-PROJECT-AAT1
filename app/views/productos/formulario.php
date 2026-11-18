<h1><?= e($titulo) ?></h1>

<?php if (!empty($errores)): ?>
  <ul class="errores">
    <?php foreach ($errores as $error): ?>
      <li><?= e($error) ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<form class="formulario" method="POST" action="index.php?c=productos&a=<?= e($accion) ?>">
  <input type="hidden" name="id_producto" value="<?= e($producto['id_producto'] ?? '') ?>">

  <label for="codigo">Código</label>
  <input id="codigo" name="codigo" maxlength="20" required value="<?= e($producto['codigo'] ?? '') ?>">

  <label for="nombre">Nombre</label>
  <input id="nombre" name="nombre" maxlength="100" required value="<?= e($producto['nombre'] ?? '') ?>">

  <label for="precio">Precio (Q)</label>
  <input id="precio" name="precio" type="number" step="0.01" min="0.01" required value="<?= e($producto['precio'] ?? '') ?>">

  <label for="stock">Stock</label>
  <input id="stock" name="stock" type="number" min="0" required value="<?= e($producto['stock'] ?? 0) ?>">

  <label for="id_categoria">Categoría</label>
  <select id="id_categoria" name="id_categoria" required>
    <option value="">-- Selecciona --</option>
    <?php foreach ($categorias as $categoria): ?>
      <option value="<?= e($categoria['id_categoria']) ?>"
        <?= ($producto['id_categoria'] ?? 0) == $categoria['id_categoria'] ? 'selected' : '' ?>>
        <?= e($categoria['nombre']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <p>
    <button class="boton">Guardar</button>
    <a class="boton secundario" href="index.php?c=productos">Cancelar</a>
  </p>
</form>
