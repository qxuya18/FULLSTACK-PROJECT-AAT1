<?php
// app/controllers/InicioController.php · Página principal con un resumen
class InicioController extends Controller
{
    public function index(): void
    {
        $venta = new Venta();
        $producto = new Producto();

        $this->render('inicio/index', [
            'resumen'         => $venta->obtenerResumen(),
            'inventarioBajo'  => $producto->obtenerInventarioBajo(),
        ]);
    }
}
