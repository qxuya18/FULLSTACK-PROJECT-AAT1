<h1><?= e($titulo) ?></h1>

<?php if (!empty($errores)): ?>
  <ul class="errores">
    <?php foreach ($errores as $error): ?>
      <li><?= e($error) ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<form class="formulario" method="POST" action="index.php?c=clientes&a=<?= e($accion) ?>">
  <input type="hidden" name="id_cliente" value="<?= e($cliente['id_cliente'] ?? '') ?>">

  <label for="nombre">Nombre</label>
  <input id="nombre" name="nombre" maxlength="150" required value="<?= e($cliente['nombre'] ?? '') ?>">

  <label for="nit">NIT (déjalo vacío para CF)</label>
  <input id="nit" name="nit" maxlength="15" value="<?= e($cliente['nit'] ?? '') ?>">

  <label for="telefono">Teléfono</label>
  <input id="telefono" name="telefono" maxlength="15" value="<?= e($cliente['telefono'] ?? '') ?>">

  <label for="correo">Correo</label>
  <input id="correo" name="correo" type="email" maxlength="100" value="<?= e($cliente['correo'] ?? '') ?>">

  <p>
    <button class="boton">Guardar</button>
    <a class="boton secundario" href="index.php?c=clientes">Cancelar</a>
  </p>
</form>
