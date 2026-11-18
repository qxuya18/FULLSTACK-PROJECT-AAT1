<?php
// app/api/ProductoApiController.php · CRUD de productos en formato JSON
// Reutiliza el modelo Producto de la AHD3: el SQL ya está escrito y probado.
class ProductoApiController extends ApiController
{
    private Producto $producto;

    public function __construct()
    {
        $this->producto = new Producto();
    }

    // GET /api/productos  (opcional: ?buscar=texto)
    protected function listar(): void
    {
        $buscar = trim($_GET['buscar'] ?? '');
        $productos = array_map(
            fn($fila) => $this->aNumeros($fila, ['precio']),
            $this->producto->obtenerTodos($buscar)
        );
        $this->responder(200, ['datos' => $productos, 'total' => count($productos)]);
    }

    // GET /api/productos/{id}
    protected function ver(int $id): void
    {
        $producto = $this->producto->obtenerPorId($id);
        if ($producto === null) {
            $this->noEncontrado("No existe el producto con id $id");
        }
        $this->responder(200, ['datos' => $this->aNumeros($producto, ['precio'])]);
    }

    // POST /api/productos  (cuerpo JSON con codigo, nombre, precio, stock, id_categoria)
    protected function crear(): void
    {
        $datos = $this->normalizar($this->leerJson());
        $this->validar($datos);

        try {
            $id = $this->producto->crear($datos);
        } catch (PDOException $error) {
            $this->manejarErrorDeLlave($error);
        }
        $this->responder(201, [
            'mensaje' => 'Producto creado',
            'datos'   => $this->aNumeros($this->producto->obtenerPorId($id), ['precio']),
        ]);
    }

    // PUT /api/productos/{id}  (cuerpo JSON con TODOS los campos)
    protected function actualizar(int $id): void
    {
        if ($this->producto->obtenerPorId($id) === null) {
            $this->noEncontrado("No existe el producto con id $id");
        }
        $datos = $this->normalizar($this->leerJson());
        $this->validar($datos, $id);

        try {
            $this->producto->actualizar($id, $datos);
        } catch (PDOException $error) {
            $this->manejarErrorDeLlave($error);
        }
        $this->responder(200, [
            'mensaje' => 'Producto actualizado',
            'datos'   => $this->aNumeros($this->producto->obtenerPorId($id), ['precio']),
        ]);
    }

    // DELETE /api/productos/{id}
    protected function eliminar(int $id): void
    {
        if ($this->producto->obtenerPorId($id) === null) {
            $this->noEncontrado("No existe el producto con id $id");
        }
        try {
            $this->producto->eliminar($id);
        } catch (PDOException $error) {
            $this->manejarErrorDeLlave($error);
        }
        $this->responder(200, ['mensaje' => "Producto $id eliminado"]);
    }

    // ---------- Métodos privados de apoyo ----------

    // Limpia los datos recibidos (igual que leerFormulario() de la AHD3)
    private function normalizar(array $json): array
    {
        return [
            'codigo'       => strtoupper(trim((string) ($json['codigo'] ?? ''))),
            'nombre'       => trim((string) ($json['nombre'] ?? '')),
            'precio'       => $json['precio'] ?? '',
            'stock'        => $json['stock'] ?? '',
            'id_categoria' => (int) ($json['id_categoria'] ?? 0),
        ];
    }

    // Las mismas reglas de la AHD3; si hay errores responde 422 con la lista
    private function validar(array $datos, int $idExcluir = 0): void
    {
        $errores = [];

        if ($datos['codigo'] === '') {
            $errores[] = 'El código es obligatorio.';
        } elseif ($this->producto->existeCodigo($datos['codigo'], $idExcluir)) {
            $errores[] = "El código {$datos['codigo']} ya está registrado.";
        }
        if ($datos['nombre'] === '' || mb_strlen($datos['nombre']) > 100) {
            $errores[] = 'El nombre es obligatorio (máximo 100 caracteres).';
        }
        if (!is_numeric($datos['precio']) || $datos['precio'] <= 0) {
            $errores[] = 'El precio debe ser un número mayor que cero.';
        }
        if (filter_var($datos['stock'], FILTER_VALIDATE_INT) === false || $datos['stock'] < 0) {
            $errores[] = 'El stock debe ser un número entero de 0 o más.';
        }
        if ($datos['id_categoria'] <= 0) {
            $errores[] = 'El id_categoria es obligatorio.';
        }

        if (!empty($errores)) {
            $this->responder(422, ['error' => 'Datos no válidos', 'errores' => $errores]);
        }
    }

    // Traduce los errores de llave foránea de MySQL a respuestas claras
    private function manejarErrorDeLlave(PDOException $error): void
    {
        $codigoMysql = $error->errorInfo[1] ?? 0;
        if ($codigoMysql === 1451) {
            $this->responder(409, ['error' => 'No se puede eliminar: el producto ya tiene ventas registradas']);
        }
        if ($codigoMysql === 1452) {
            $this->responder(422, ['error' => 'Datos no válidos', 'errores' => ['La categoría indicada no existe.']]);
        }
        throw $error;   // otro error: lo atiende api/index.php (500)
    }
}
