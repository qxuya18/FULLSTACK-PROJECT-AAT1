<?php
// 06_conexion.php · AHD3 · Primera conexión de PHP con MySQL usando PDO
try {
    $conexion = new PDO(
        'mysql:host=localhost;dbname=papeleria;charset=utf8mb4',
        'root',   // usuario de XAMPP
        ''        // en XAMPP la contraseña de root está vacía
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // query(): para consultas SIN datos del usuario
    $resultado = $conexion->query('SELECT COUNT(*) AS total FROM productos');
    $fila = $resultado->fetch(PDO::FETCH_ASSOC);
    echo "<p>✅ Conectado a papeleria. Hay {$fila['total']} productos.</p>";

    // prepare() + execute(): SIEMPRE que la consulta lleve datos externos
    $consulta = $conexion->prepare('SELECT nombre, precio FROM productos WHERE codigo = :codigo');
    $consulta->execute([':codigo' => 'TEC-002']);
    $producto = $consulta->fetch(PDO::FETCH_ASSOC);
    echo "<p>{$producto['nombre']} cuesta Q{$producto['precio']}</p>";

    // También podemos usar las funciones que creamos en la AHD2
    $total = $conexion->query('SELECT fn_total_venta(2)')->fetchColumn();
    echo "<p>Total de la venta 2 (con fn_total_venta): Q$total</p>";
} catch (PDOException $error) {
    echo '<p>❌ Error de conexión: ' . $error->getMessage() . '</p>';
}
