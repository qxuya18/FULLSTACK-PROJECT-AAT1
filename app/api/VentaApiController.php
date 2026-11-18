<?php
// app/api/VentaApiController.php · Ventas en formato JSON
// Reutiliza el modelo Venta de la AHD3, que usa las vistas, la función
// fn_total_venta y el procedimiento sp_registrar_venta de la AHD2.
class VentaApiController extends ApiController
{
    private Venta $venta;

    public function __construct()
    {
        $this->venta = new Venta();
    }

    // GET /api/ventas → ventas con su total (vista v_total_ventas)
    protected function listar(): void
    {
        $ventas = array_map(
            fn($fila) => $this->aNumeros($fila, ['total']),
            $this->venta->obtenerTodas()
        );
        $this->responder(200, ['datos' => $ventas, 'total' => count($ventas)]);
    }

    // GET /api/ventas/{id} → encabezado, líneas y total (el "ticket")
    protected function ver(int $id): void
    {
        $lineas = $this->venta->obtenerDetalle($id);
        if (empty($lineas)) {
            $this->noEncontrado("No existe la venta con id $id");
        }
        $this->responder(200, ['datos' => $this->armarTicket($id, $lineas)]);
    }

    // POST /api/ventas  (id_cliente, id_empleado, id_producto, cantidad)
    protected function crear(): void
    {
        $json = $this->leerJson();
        $datos = [];
        $errores = [];

        // Estructura cíclica para validar los 4 campos con la misma regla
        foreach (['id_cliente', 'id_empleado', 'id_producto', 'cantidad'] as $campo) {
            $valor = filter_var($json[$campo] ?? null, FILTER_VALIDATE_INT);
            if ($valor === false || $valor <= 0) {
                $errores[] = "El campo $campo es obligatorio y debe ser un entero mayor que cero.";
            }
            $datos[$campo] = (int) $valor;
        }
        if (!empty($errores)) {
            $this->responder(422, ['error' => 'Datos no válidos', 'errores' => $errores]);
        }

        try {
            $idVenta = $this->venta->registrar(
                $datos['id_cliente'], $datos['id_empleado'], $datos['id_producto'], $datos['cantidad']
            );
        } catch (PDOException $error) {
            $codigoMysql = $error->errorInfo[1] ?? 0;
            // 1644 = SIGNAL del procedimiento ("No hay suficiente stock", "El producto no existe")
            if ($codigoMysql === 1644) {
                $this->responder(409, ['error' => $error->errorInfo[2]]);
            }
            // 1452 = el cliente o el empleado no existen (llave foránea)
            if ($codigoMysql === 1452) {
                $this->responder(422, ['error' => 'Datos no válidos', 'errores' => ['El cliente o el empleado no existe.']]);
            }
            throw $error;
        }

        $this->responder(201, [
            'mensaje' => "Venta #$idVenta registrada",
            'datos'   => $this->armarTicket($idVenta, $this->venta->obtenerDetalle($idVenta)),
        ]);
    }

    // Convierte las filas de v_detalle_ventas en un solo objeto JSON ordenado
    private function armarTicket(int $idVenta, array $lineas): array
    {
        return [
            'id_venta' => $idVenta,
            'fecha'    => $lineas[0]['fecha'],
            'cliente'  => $lineas[0]['cliente'],
            'empleado' => $lineas[0]['empleado'],
            'lineas'   => array_map(fn($linea) => [
                'producto'        => $linea['producto'],
                'cantidad'        => (int) $linea['cantidad'],
                'precio_unitario' => (float) $linea['precio_unitario'],
                'subtotal'        => (float) $linea['subtotal'],
            ], $lineas),
            'total'    => $this->venta->obtenerTotal($idVenta),
        ];
    }
}
