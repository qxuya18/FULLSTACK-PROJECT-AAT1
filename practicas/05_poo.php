<?php
// 05_poo.php · AHD3 · Programación orientada a objetos
declare(strict_types=1);

// Clase (molde): nombre en PascalCase y en singular
class ProductoPapeleria
{
    // Constructor con promoción de propiedades (PHP 8)
    public function __construct(
        private string $codigo,
        private string $nombre,
        private float $precio,
        private int $stock = 0
    ) {
        $this->setPrecio($precio);   // reutiliza la validación del setter
    }

    // Getters: devuelven el valor
    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    // Setter: cambia el valor solo si es válido (encapsulamiento)
    public function setPrecio(float $precio): void
    {
        if ($precio <= 0) {
            throw new InvalidArgumentException('El precio debe ser mayor que cero');
        }
        $this->precio = $precio;
    }

    // Métodos de negocio: nombres en camelCase que empiezan con un verbo
    public function vender(int $cantidad): void
    {
        if ($cantidad > $this->stock) {
            throw new RuntimeException("No hay suficiente stock de {$this->nombre}");
        }
        $this->stock -= $cantidad;
    }

    public function calcularValorInventario(): float
    {
        return $this->precio * $this->stock;
    }

    public function obtenerEstado(): string
    {
        return match (true) {
            $this->stock === 0 => 'Agotado',
            $this->stock < 15  => 'Bajo',
            default            => 'Suficiente',
        };
    }
}

// Herencia: Cliente y Empleado SON personas
abstract class Persona
{
    public function __construct(protected string $nombre) {}

    abstract public function presentarse(): string;   // cada hija lo define
}

class Cliente extends Persona
{
    public function __construct(string $nombre, private string $nit = 'CF')
    {
        parent::__construct($nombre);
    }

    public function presentarse(): string
    {
        return "Soy {$this->nombre}, cliente con NIT {$this->nit}";
    }
}

class Empleado extends Persona
{
    public function __construct(string $nombre, private string $puesto)
    {
        parent::__construct($nombre);
    }

    public function presentarse(): string
    {
        return "Soy {$this->nombre} y trabajo como {$this->puesto}";
    }
}

// Objetos (instancias): se crean con new
$calculadora = new ProductoPapeleria('TEC-002', 'Calculadora científica', 120.00, 8);
$calculadora->vender(2);
echo '<p>' . $calculadora->getNombre() . ': quedan ' . $calculadora->getStock()
   . ' (' . $calculadora->obtenerEstado() . '), valor Q'
   . number_format($calculadora->calcularValorInventario(), 2) . '</p>';

try {
    $calculadora->vender(50);
} catch (RuntimeException $error) {
    echo '<p>❌ ' . $error->getMessage() . '</p>';
}

// Polimorfismo: el mismo mensaje (presentarse) responde distinto según el objeto
$personas = [new Cliente('Librería La Esquina', '4567891-0'), new Empleado('Sofía Gómez', 'Encargada de bodega')];
foreach ($personas as $persona) {
    echo '<p>' . $persona->presentarse() . '</p>';
}
