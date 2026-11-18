<?php
// app/models/Cliente.php · Modelo de la tabla clientes
class Cliente
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // READ: clientes con su número de compras (LEFT JOIN de la AHD2)
    public function obtenerTodos(): array
    {
        $sql = 'SELECT cl.id_cliente, cl.nombre, cl.nit, cl.telefono, cl.correo,
                       COUNT(v.id_venta) AS compras
                FROM clientes cl
                LEFT JOIN ventas v ON v.id_cliente = cl.id_cliente
                GROUP BY cl.id_cliente, cl.nombre, cl.nit, cl.telefono, cl.correo
                ORDER BY cl.nombre';
        return $this->db->query($sql)->fetchAll();
    }

    public function obtenerPorId(int $idCliente): ?array
    {
        $consulta = $this->db->prepare('SELECT * FROM clientes WHERE id_cliente = :id');
        $consulta->execute([':id' => $idCliente]);
        $cliente = $consulta->fetch();
        return $cliente === false ? null : $cliente;
    }

    public function crear(array $datos): int
    {
        $sql = 'INSERT INTO clientes (nombre, nit, telefono, correo)
                VALUES (:nombre, :nit, :telefono, :correo)';
        $this->db->prepare($sql)->execute($this->prepararParametros($datos));
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $idCliente, array $datos): void
    {
        $sql = 'UPDATE clientes
                SET nombre = :nombre, nit = :nit, telefono = :telefono, correo = :correo
                WHERE id_cliente = :id';
        $parametros = $this->prepararParametros($datos);
        $parametros[':id'] = $idCliente;
        $this->db->prepare($sql)->execute($parametros);
    }

    public function eliminar(int $idCliente): void
    {
        $consulta = $this->db->prepare('DELETE FROM clientes WHERE id_cliente = :id');
        $consulta->execute([':id' => $idCliente]);
    }

    public function existeCorreo(string $correo, int $idExcluir = 0): bool
    {
        $consulta = $this->db->prepare(
            'SELECT COUNT(*) FROM clientes WHERE correo = :correo AND id_cliente <> :id'
        );
        $consulta->execute([':correo' => $correo, ':id' => $idExcluir]);
        return $consulta->fetchColumn() > 0;
    }

    // Los campos vacíos se guardan como NULL; el NIT vacío como 'CF'
    private function prepararParametros(array $datos): array
    {
        return [
            ':nombre'   => $datos['nombre'],
            ':nit'      => $datos['nit'] === '' ? 'CF' : $datos['nit'],
            ':telefono' => $datos['telefono'] === '' ? null : $datos['telefono'],
            ':correo'   => $datos['correo'] === '' ? null : $datos['correo'],
        ];
    }
}
