<?php
//app/api/CategoriaApiController.php · GET /api/categorias;
class CategoriaApiController extends ApiController
{
    // GET /api/categorias → lista de categorías (para llenar listas desplegables)
    protected function listar(): void
    {
        $categorias = (new Categoria())->obtenerTodas();
        $this->responder(200, ['datos' => $categorias, 'total' => count($categorias)]);
    }
}
