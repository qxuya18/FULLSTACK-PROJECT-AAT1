<?php
// app/core/ApiController.php · Clase base de todos los controladores de la API
// Decide qué método ejecutar según el verbo HTTP y responde siempre en JSON.
abstract class ApiController
{
    // Relaciona el verbo HTTP (y si viene un id) con la operación del CRUD
    public function atender(string $metodo, ?int $id): void
    {
        match (true) {
            $metodo === 'GET' && $id === null    => $this->listar(),
            $metodo === 'GET'                    => $this->ver($id),
            $metodo === 'POST' && $id === null   => $this->crear(),
            $metodo === 'PUT' && $id !== null    => $this->actualizar($id),
            $metodo === 'DELETE' && $id !== null => $this->eliminar($id),
            default                              => $this->noPermitido(),
        };
    }

    // Si un controlador hijo no define una operación, la API responde 405
    protected function listar(): void { $this->noPermitido(); }
    protected function ver(int $id): void { $this->noPermitido(); }
    protected function crear(): void { $this->noPermitido(); }
    protected function actualizar(int $id): void { $this->noPermitido(); }
    protected function eliminar(int $id): void { $this->noPermitido(); }

    // Envía la respuesta: código de estado HTTP + datos convertidos a JSON
    protected function responder(int $codigo, array $datos): void
    {
        http_response_code($codigo);
        echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function noPermitido(): void
    {
        $this->responder(405, ['error' => 'Método no permitido para esta ruta']);
    }

    protected function noEncontrado(string $mensaje): void
    {
        $this->responder(404, ['error' => $mensaje]);
    }

    // Lee el cuerpo de la petición (JSON) y lo convierte en array
    protected function leerJson(): array
    {
        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            $this->responder(400, ['error' => 'El cuerpo de la petición debe ser un JSON válido']);
        }
        return $datos;
    }

    // MySQL devuelve los DECIMAL como texto ("18.50"); aquí se convierten en número
    protected function aNumeros(array $fila, array $columnas): array
    {
        foreach ($columnas as $columna) {
            if (isset($fila[$columna])) {
                $fila[$columna] = (float) $fila[$columna];
            }
        }
        return $fila;
    }
}
