<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(NOMBRE_APP) ?></title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
  <header class="barra">
    <strong>📚 <?= e(NOMBRE_APP) ?></strong>
    <nav>
      <a href="index.php">Inicio</a>
      <a href="index.php?c=productos">Productos</a>
      <a href="index.php?c=clientes">Clientes</a>
      <a href="index.php?c=ventas">Ventas</a>
    </nav>
  </header>
  <main class="contenido">
    <?php if (isset($_SESSION['mensaje'])): ?>
      <div class="mensaje <?= e($_SESSION['mensaje']['tipo']) ?>">
        <?= e($_SESSION['mensaje']['texto']) ?>
      </div>
      <?php unset($_SESSION['mensaje']); // se muestra una sola vez ?>
    <?php endif; ?>
