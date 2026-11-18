<?php
// app/models/Empleado.php · Modelo de la tabla empleados
class Empleado
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    public function obtenerTodos(): array
    {
        return $this->db
            ->query('SELECT id_empleado, nombre, puesto FROM empleados ORDER BY nombre')
            ->fetchAll();
    }
}
