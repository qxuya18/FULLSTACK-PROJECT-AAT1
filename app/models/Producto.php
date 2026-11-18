<?php
// app/models/Producto.php · Modelo: todo lo que se hace con la tabla productos

require_once __DIR__ . '/../core/Database.php';

class Producto
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // READ: lista de productos con su categoría (opcional: buscar por nombre o código)
    public function obtenerTodos(string $buscar = ''): array
    {
        $sql = 'SELECT p.id_producto, p.codigo, p.nombre, p.precio, p.stock,
                       c.nombre AS categoria,
                       fn_estado_stock(p.stock) AS estado
                FROM productos p
                JOIN categorias c ON c.id_categoria = p.id_categoria
                WHERE p.nombre LIKE :buscar OR p.codigo LIKE :buscar2
                ORDER BY p.nombre';
        $consulta = $this->db->prepare($sql);
        $consulta->execute([':buscar' => "%$buscar%", ':buscar2' => "%$buscar%"]);
        return $consulta->fetchAll();
    }

    // READ: un solo producto por su id (null si no existe)
    public function obtenerPorId(int $idProducto): ?array
    {
        $consulta = $this->db->prepare('SELECT * FROM productos WHERE id_producto = :id');
        $consulta->execute([':id' => $idProducto]);
        $producto = $consulta->fetch();
        return $producto === false ? null : $producto;
    }

    // Productos con existencia, para el formulario de ventas
    public function obtenerDisponibles(): array
    {
        return $this->db
            ->query('SELECT id_producto, nombre, precio, stock FROM productos WHERE stock > 0 ORDER BY nombre')
            ->fetchAll();
    }

    // Usa el procedimiento almacenado de la AHD2
    public function obtenerInventarioBajo(): array
    {
        $consulta = $this->db->query('CALL sp_inventario_bajo()');
        $filas = $consulta->fetchAll();
        $consulta->closeCursor();   // obligatorio después de un CALL
        return $filas;
    }

    // CREATE
    public function crear(array $datos): int
    {
        $sql = 'INSERT INTO productos (codigo, nombre, precio, stock, id_categoria)
                VALUES (:codigo, :nombre, :precio, :stock, :id_categoria)';
        $this->db->prepare($sql)->execute([
            ':codigo'       => $datos['codigo'],
            ':nombre'       => $datos['nombre'],
            ':precio'       => $datos['precio'],
            ':stock'        => $datos['stock'],
            ':id_categoria' => $datos['id_categoria'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    // UPDATE
    public function actualizar(int $idProducto, array $datos): void
    {
        $sql = 'UPDATE productos
                SET codigo = :codigo, nombre = :nombre, precio = :precio,
                    stock = :stock, id_categoria = :id_categoria
                WHERE id_producto = :id';
        $this->db->prepare($sql)->execute([
            ':codigo'       => $datos['codigo'],
            ':nombre'       => $datos['nombre'],
            ':precio'       => $datos['precio'],
            ':stock'        => $datos['stock'],
            ':id_categoria' => $datos['id_categoria'],
            ':id'           => $idProducto,
        ]);
    }

    // DELETE
    public function eliminar(int $idProducto): void
    {
        $consulta = $this->db->prepare('DELETE FROM productos WHERE id_producto = :id');
        $consulta->execute([':id' => $idProducto]);
    }

    // ¿Ya existe otro producto con ese código? (la columna es UNIQUE)
    public function existeCodigo(string $codigo, int $idExcluir = 0): bool
    {
        $consulta = $this->db->prepare(
            'SELECT COUNT(*) FROM productos WHERE codigo = :codigo AND id_producto <> :id'
        );
        $consulta->execute([':codigo' => $codigo, ':id' => $idExcluir]);
        return $consulta->fetchColumn() > 0;
    }
}
