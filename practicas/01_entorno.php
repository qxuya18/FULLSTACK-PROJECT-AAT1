<?php
// 01_entorno.php · AHD3 · Comprobar que el servidor local está listo
$version = phpversion();
$tienePdoMysql = extension_loaded('pdo_mysql');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Entorno PHP</title>
</head>
<body>
  <h1>Servidor local de la papelería</h1>
  <p>Versión de PHP: <strong><?= $version ?></strong></p>
  <p>Servidor web: <strong><?= $_SERVER['SERVER_SOFTWARE'] ?? 'desconocido' ?></strong></p>

  <?php if (version_compare($version, '8.0.0', '>=')): ?>
    <p>✅ PHP 8 o superior: listo para la AHD3.</p>
  <?php else: ?>
    <p>❌ Tu PHP es muy antiguo. Instala la versión más reciente de XAMPP.</p>
  <?php endif; ?>

  <?php if ($tienePdoMysql): ?>
    <p>✅ La extensión pdo_mysql está activa: PHP puede hablar con MySQL.</p>
  <?php else: ?>
    <p>❌ Falta pdo_mysql. Actívala en php.ini (extension=pdo_mysql) y reinicia Apache.</p>
  <?php endif; ?>
</body>
</html> 
