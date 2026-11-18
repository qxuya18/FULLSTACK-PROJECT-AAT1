<?php
// app/controllers/ProductoController.php · Recibe las peticiones de productos
class ProductoController extends Controller
{
    private Producto $producto;
    private Categoria $categoria;

    public function __construct()
    {
        $this->producto = new Producto();
        $this->categoria = new Categoria();
    }

    // GET index.php?c=productos  (y ?buscar=texto)
    public function index(): void
    {
        $buscar = trim($_GET['buscar'] ?? '');
        $this->render('productos/index', [
            'productos' => $this->producto->obtenerTodos($buscar),
            'buscar'    => $buscar,
        ]);
    }

    // GET: formulario vacío para un producto nuevo
    public function crear(): void
    {
        $this->mostrarFormulario('Nuevo producto', 'guardar', []);
    }

    // POST: recibe el formulario y crea el producto
    public function guardar(): void
    {
        $datos = $this->leerFormulario();
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $this->mostrarFormulario('Nuevo producto', 'guardar', $datos, $errores);
            return;
        }

        $this->producto->crear($datos);
        $this->guardarMensaje('exito', "Producto {$datos['nombre']} creado.");
        $this->redirigir('productos');
    }

    // GET index.php?c=productos&a=editar&id=3
    public function editar(): void
    {
        $idProducto = $this->leerEntero($_GET, 'id');
        $producto = $this->producto->obtenerPorId($idProducto);

        if ($producto === null) {
            $this->guardarMensaje('error', 'El producto no existe.');
            $this->redirigir('productos');
        }
        $this->mostrarFormulario('Editar producto', 'actualizar', $producto);
    }

    // POST: guarda los cambios
    public function actualizar(): void
    {
        $datos = $this->leerFormulario();
        $idProducto = $this->leerEntero($_POST, 'id_producto');
        $datos['id_producto'] = $idProducto;
        $errores = $this->validar($datos, $idProducto);

        if (!empty($errores)) {
            $this->mostrarFormulario('Editar producto', 'actualizar', $datos, $errores);
            return;
        }

        $this->producto->actualizar($idProducto, $datos);
        $this->guardarMensaje('exito', "Producto {$datos['nombre']} actualizado.");
        $this->redirigir('productos');
    }

    // POST: elimina (los formularios de borrar usan POST, nunca un enlace GET)
    public function eliminar(): void
    {
        $idProducto = $this->leerEntero($_POST, 'id_producto');

        try {
            $this->producto->eliminar($idProducto);
            $this->guardarMensaje('exito', 'Producto eliminado.');
        } catch (PDOException $error) {
            // 1451: el producto ya aparece en ventas (llave foránea)
            if (($error->errorInfo[1] ?? 0) === 1451) {
                $this->guardarMensaje('error', 'No se puede eliminar: este producto ya tiene ventas registradas.');
            } else {
                throw $error;
            }
        }
        $this->redirigir('productos');
    }

    // ---------- Métodos privados de apoyo ----------

    private function mostrarFormulario(string $titulo, string $accion, array $producto, array $errores = []): void
    {
        $this->render('productos/formulario', [
            'titulo'     => $titulo,
            'accion'     => $accion,
            'producto'   => $producto,
            'categorias' => $this->categoria->obtenerTodas(),
            'errores'    => $errores,
        ]);
    }

    private function leerFormulario(): array
    {
        return [
            'codigo'       => strtoupper(trim($_POST['codigo'] ?? '')),
            'nombre'       => trim($_POST['nombre'] ?? ''),
            'precio'       => trim($_POST['precio'] ?? ''),
            'stock'        => trim($_POST['stock'] ?? ''),
            'id_categoria' => $this->leerEntero($_POST, 'id_categoria'),
        ];
    }

    // Valida SIEMPRE en el servidor, aunque el formulario tenga "required"
    private function validar(array $datos, int $idExcluir = 0): array
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
            $errores[] = 'Selecciona una categoría.';
        }
        return $errores;
    }
}
