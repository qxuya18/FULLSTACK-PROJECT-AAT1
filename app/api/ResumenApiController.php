<?php
// app/api/ResumenApiController.php · GET /api/resumen
class ResumenApiController extends ApiController
{
    // GET /api/resumen → totales de ventas y productos con stock bajo
    protected function listar(): void
    {
        $resumen = (new Venta())->obtenerResumen();
        $this->responder(200, [
            'datos' => [
                'ventas'          => (int) $resumen['ventas'],
                'total_vendido'   => (float) $resumen['total_vendido'],
                'ticket_promedio' => (float) $resumen['ticket_promedio'],
                'inventario_bajo' => (new Producto())->obtenerInventarioBajo(),
            ],
        ]);
    }
}
