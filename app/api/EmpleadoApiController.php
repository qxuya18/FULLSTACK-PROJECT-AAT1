<?php
// app/api/EmpleadoApiController.php · GET /api/empleados
class EmpleadoApiController extends ApiController
{
    // GET /api/empleados → lista de empleados (para el formulario de ventas)
    protected function listar(): void
    {
        $empleados = (new Empleado())->obtenerTodos();
        $this->responder(200, ['datos' => $empleados, 'total' => count($empleados)]);
    }
}
