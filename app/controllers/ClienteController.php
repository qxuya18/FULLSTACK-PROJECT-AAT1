<?php
// app/controllers/ClienteController.php · CRUD de clientes


require_once __DIR__ . '/../core/Databases.php';   

class ClienteController extends Controller
{
    private Cliente $cliente;

    public function __construct()
    {
        $this->cliente = new Cliente();
    }

    public function index(): void
    {
        $this->render('clientes/index', ['clientes' => $this->cliente->obtenerTodos()]);
    }

    public function crear(): void
    {
        $this->render('clientes/formulario', [
            'titulo' => 'Nuevo cliente', 'accion' => 'guardar', 'cliente' => [], 'errores' => [],
        ]);
    }

    public function guardar(): void
    {
        $datos = $this->leerFormulario();
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $this->render('clientes/formulario', [
                'titulo' => 'Nuevo cliente', 'accion' => 'guardar', 'cliente' => $datos, 'errores' => $errores,
            ]);
            return;
        }

        $this->cliente->crear($datos);
        $this->guardarMensaje('exito', "Cliente {$datos['nombre']} registrado.");
        $this->redirigir('clientes');
    }

    public function editar(): void
    {
        $cliente = $this->cliente->obtenerPorId($this->leerEntero($_GET, 'id'));

        if ($cliente === null) {
            $this->guardarMensaje('error', 'El cliente no existe.');
            $this->redirigir('clientes');
        }
        $this->render('clientes/formulario', [
            'titulo' => 'Editar cliente', 'accion' => 'actualizar', 'cliente' => $cliente, 'errores' => [],
        ]);
    }

    public function actualizar(): void
    {
        $idCliente = $this->leerEntero($_POST, 'id_cliente');
        $datos = $this->leerFormulario();
        $datos['id_cliente'] = $idCliente;
        $errores = $this->validar($datos, $idCliente);

        if (!empty($errores)) {
            $this->render('clientes/formulario', [
                'titulo' => 'Editar cliente', 'accion' => 'actualizar', 'cliente' => $datos, 'errores' => $errores,
            ]);
            return;
        }

        $this->cliente->actualizar($idCliente, $datos);
        $this->guardarMensaje('exito', "Cliente {$datos['nombre']} actualizado.");
        $this->redirigir('clientes');
    }

    public function eliminar(): void
    {
        try {
            $this->cliente->eliminar($this->leerEntero($_POST, 'id_cliente'));
            $this->guardarMensaje('exito', 'Cliente eliminado.');
        } catch (PDOException $error) {
            if (($error->errorInfo[1] ?? 0) === 1451) {
                $this->guardarMensaje('error', 'No se puede eliminar: el cliente tiene compras registradas.');
            } else {
                throw $error;
            }
        }
        $this->redirigir('clientes');
    }

    private function leerFormulario(): array
    {
        return [
            'nombre'   => trim($_POST['nombre'] ?? ''),
            'nit'      => strtoupper(trim($_POST['nit'] ?? '')),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'correo'   => strtolower(trim($_POST['correo'] ?? '')),
        ];
    }

    private function validar(array $datos, int $idExcluir = 0): array
    {
        $errores = [];

        if ($datos['nombre'] === '' || mb_strlen($datos['nombre']) > 150) {
            $errores[] = 'El nombre es obligatorio (máximo 150 caracteres).';
        }
        if (mb_strlen($datos['nit']) > 15) {
            $errores[] = 'El NIT no puede tener más de 15 caracteres.';
        }
        if ($datos['correo'] !== '') {
            if (filter_var($datos['correo'], FILTER_VALIDATE_EMAIL) === false) {
                $errores[] = 'El correo no tiene un formato válido.';
            } elseif ($this->cliente->existeCorreo($datos['correo'], $idExcluir)) {
                $errores[] = 'Ese correo ya pertenece a otro cliente.';
            }
        }
        return $errores;
    }
}
