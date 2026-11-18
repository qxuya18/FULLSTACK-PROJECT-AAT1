<?php
// app/controllers/VentaController.php · Listar, ver y registrar ventas
class VentaController extends Controller
{
    private Venta $venta;

    public function __construct()
    {
        $this->venta = new Venta();
    }

    public function index(): void
    {
        $this->render('ventas/index', ['ventas' => $this->venta->obtenerTodas()]);
    }

    // GET index.php?c=ventas&a=ver&id=2
    public function ver(): void
    {
        $idVenta = $this->leerEntero($_GET, 'id');
        $lineas = $this->venta->obtenerDetalle($idVenta);

        if (empty($lineas)) {
            $this->guardarMensaje('error', 'La venta no existe.');
            $this->redirigir('ventas');
        }
        $this->render('ventas/detalle', [
            'idVenta' => $idVenta,
            'lineas'  => $lineas,
            'total'   => $this->venta->obtenerTotal($idVenta),
        ]);
    }

    // GET: formulario de nueva venta
    public function crear(): void
    {
        $this->mostrarFormulario([]);
    }

    // POST: registra la venta llamando al procedimiento con transacción
    public function guardar(): void
    {
        $datos = [
            'id_cliente'  => $this->leerEntero($_POST, 'id_cliente'),
            'id_empleado' => $this->leerEntero($_POST, 'id_empleado'),
            'id_producto' => $this->leerEntero($_POST, 'id_producto'),
            'cantidad'    => $this->leerEntero($_POST, 'cantidad'),
        ];

        // Validación con estructuras selectivas
        $errores = [];
        foreach (['id_cliente' => 'cliente', 'id_empleado' => 'empleado', 'id_producto' => 'producto'] as $campo => $nombre) {
            if ($datos[$campo] <= 0) {
                $errores[] = "Selecciona un $nombre.";
            }
        }
        if ($datos['cantidad'] <= 0) {
            $errores[] = 'La cantidad debe ser mayor que cero.';
        }

        if (empty($errores)) {
            try {
                $idVenta = $this->venta->registrar(
                    $datos['id_cliente'], $datos['id_empleado'], $datos['id_producto'], $datos['cantidad']
                );
                $this->guardarMensaje('exito', "Venta #$idVenta registrada correctamente.");
                header("Location: index.php?c=ventas&a=ver&id=$idVenta");
                exit;
            } catch (PDOException $error) {
                // errorInfo[2] trae el mensaje del SIGNAL: "No hay suficiente stock"
                $errores[] = 'No se registró la venta: ' . ($error->errorInfo[2] ?? $error->getMessage());
            }
        }
        $this->mostrarFormulario($datos, $errores);
    }

    private function mostrarFormulario(array $datos, array $errores = []): void
    {
        $this->render('ventas/formulario', [
            'datos'     => $datos,
            'errores'   => $errores,
            'clientes'  => (new Cliente())->obtenerTodos(),
            'empleados' => (new Empleado())->obtenerTodos(),
            'productos' => (new Producto())->obtenerDisponibles(),
        ]);
    }
}
