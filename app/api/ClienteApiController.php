<?php
// app/api/ClienteApiController.php · CRUD de clientes en formato JSON
// Reutiliza el modelo Cliente de la AHD3.
class ClienteApiController extends ApiController
{
    private Cliente $cliente;

    public function __construct()
    {
        $this->cliente = new Cliente();
    }

    // GET /api/clientes → clientes con su número de compras
    protected function listar(): void
    {
        $clientes = $this->cliente->obtenerTodos();
        $this->responder(200, ['datos' => $clientes, 'total' => count($clientes)]);
    }

    // GET /api/clientes/{id}
    protected function ver(int $id): void
    {
        $cliente = $this->cliente->obtenerPorId($id);
        if ($cliente === null) {
            $this->noEncontrado("No existe el cliente con id $id");
        }
        $this->responder(200, ['datos' => $cliente]);
    }

    // POST /api/clientes  (nombre obligatorio; nit, telefono y correo opcionales)
    protected function crear(): void
    {
        $datos = $this->normalizar($this->leerJson());
        $this->validar($datos);
        $id = $this->cliente->crear($datos);
        $this->responder(201, ['mensaje' => 'Cliente registrado', 'datos' => $this->cliente->obtenerPorId($id)]);
    }

    // PUT /api/clientes/{id}
    protected function actualizar(int $id): void
    {
        if ($this->cliente->obtenerPorId($id) === null) {
            $this->noEncontrado("No existe el cliente con id $id");
        }
        $datos = $this->normalizar($this->leerJson());
        $this->validar($datos, $id);
        $this->cliente->actualizar($id, $datos);
        $this->responder(200, ['mensaje' => 'Cliente actualizado', 'datos' => $this->cliente->obtenerPorId($id)]);
    }

    // DELETE /api/clientes/{id}
    protected function eliminar(int $id): void
    {
        if ($this->cliente->obtenerPorId($id) === null) {
            $this->noEncontrado("No existe el cliente con id $id");
        }
        try {
            $this->cliente->eliminar($id);
        } catch (PDOException $error) {
            if (($error->errorInfo[1] ?? 0) === 1451) {
                $this->responder(409, ['error' => 'No se puede eliminar: el cliente tiene compras registradas']);
            }
            throw $error;
        }
        $this->responder(200, ['mensaje' => "Cliente $id eliminado"]);
    }

    // ---------- Métodos privados de apoyo ----------

    private function normalizar(array $json): array
    {
        return [
            'nombre'   => trim((string) ($json['nombre'] ?? '')),
            'nit'      => strtoupper(trim((string) ($json['nit'] ?? ''))),
            'telefono' => trim((string) ($json['telefono'] ?? '')),
            'correo'   => strtolower(trim((string) ($json['correo'] ?? ''))),
        ];
    }

    private function validar(array $datos, int $idExcluir = 0): void
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

        if (!empty($errores)) {
            $this->responder(422, ['error' => 'Datos no válidos', 'errores' => $errores]);
        }
    }
}
