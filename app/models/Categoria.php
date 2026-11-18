<?php
// app/models/Categoria.php · Modelo de la tabla categorias
class Categoria
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    public function obtenerTodas(): array
    {
        return $this->db
            ->query('SELECT id_categoria, nombre FROM categorias ORDER BY nombre')
            ->fetchAll();
    }
}
