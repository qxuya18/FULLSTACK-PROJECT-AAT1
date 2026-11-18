<?php
// app/models/Venta.php · Modelo de ventas: usa las vistas y el procedimiento de la AHD2

require_once __DIR__ . '/../core/Database.php';   

class Venta
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // Lista de ventas con su total (vista v_total_ventas)
    public function obtenerTodas(): array
    {
        return $this->db
            ->query('SELECT * FROM v_total_ventas ORDER BY fecha DESC')
            ->fetchAll();
    }

    // Líneas de una venta (vista v_detalle_ventas)
    public function obtenerDetalle(int $idVenta): array
    {
        $consulta = $this->db->prepare(
            'SELECT * FROM v_detalle_ventas WHERE id_venta = :id ORDER BY producto'
        );
        $consulta->execute([':id' => $idVenta]);
        return $consulta->fetchAll();
    }

    // Total de una venta (función fn_total_venta)
    public function obtenerTotal(int $idVenta): float
    {
        $consulta = $this->db->prepare('SELECT fn_total_venta(:id)');
        $consulta->execute([':id' => $idVenta]);
        return (float) $consulta->fetchColumn();
    }

    // Resumen para la página de inicio
    public function obtenerResumen(): array
    {
        return $this->db->query(
            'SELECT COUNT(*) AS ventas,
                    IFNULL(SUM(total), 0) AS total_vendido,
                    IFNULL(ROUND(AVG(total), 2), 0) AS ticket_promedio
             FROM v_total_ventas'
        )->fetch();
    }

    // Registra la venta con el procedimiento que usa TRANSACCIÓN (AHD2 paso 12).
    // Si falta stock, MySQL hace ROLLBACK y PHP recibe una PDOException.
    public function registrar(int $idCliente, int $idEmpleado, int $idProducto, int $cantidad): int
    {
        $consulta = $this->db->prepare('CALL sp_registrar_venta(:cliente, :empleado, :producto, :cantidad, @id_venta)');
        $consulta->execute([
            ':cliente'  => $idCliente,
            ':empleado' => $idEmpleado,
            ':producto' => $idProducto,
            ':cantidad' => $cantidad,
        ]);
        $consulta->closeCursor();

        // Leer el parámetro OUT
        return (int) $this->db->query('SELECT @id_venta')->fetchColumn();
    }
}
