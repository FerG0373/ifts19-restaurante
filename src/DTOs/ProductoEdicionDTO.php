<?php
namespace App\DTOs;

use InvalidArgumentException;

class ProductoEdicionDTO {
    public int $id;  // Se necesita el ID para saber qué registro actualizar
    public string $nombre;
    public string $descripcion;
    public float $precio;
    public int $cantidadStock;
    public string $categoria;
    public bool $activo;
        
    // Factory Method para validar datos DESDE el Formulario POST
    public static function fromArray(array $datosInput): self {
        // Validación de campos obligatorios para edición (ID es la clave)
        $camposObligatorios = ['id', 'nombre', 'precio', 'cantidad_stock', 'categoria'];
        foreach ($camposObligatorios as $campo) {
            if (empty($datosInput[$campo])) { 
                throw new InvalidArgumentException("El campo '{$campo}' es obligatorio para la edición.");
            }
        }

        // Debe existir y no ser null o cadena vacía, pero DEBE aceptar la cadena '0'
        if (!isset($datosInput['activo']) || $datosInput['activo'] === null || $datosInput['activo'] === '') {
             throw new InvalidArgumentException("El campo 'activo' es obligatorio para la edición.");
        }

        // Validar que el precio sea numérico y positivo
        if (!is_numeric($datosInput['precio']) || (float)$datosInput['precio'] <= 0) {
            throw new InvalidArgumentException("El precio debe ser un número positivo.");
        }

        // Validar que la cantidad de stock sea un número entero no negativo
        if (!is_numeric($datosInput['cantidad_stock']) || (int)$datosInput['cantidad_stock'] < 0) {
            throw new InvalidArgumentException("La cantidad de stock debe ser un número entero no negativo.");
        }
        
        $dto = new self();
        $dto->id = (int)$datosInput['id'];
        $dto->nombre = trim($datosInput['nombre']);
        $dto->descripcion = trim($datosInput['descripcion'] ?? '');
        $dto->precio = (float)$datosInput['precio'];
        $dto->cantidadStock = (int)$datosInput['cantidad_stock'];
        $dto->categoria = strtolower(trim($datosInput['categoria']));
        $dto->activo = ($datosInput['activo'] === '1');
        
        return $dto;
    }

    // Convierte el DTO a un array asociativo para precargar el formulario con el ViewRenderer
    public function toArray(): array {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'cantidad_stock' => $this->cantidadStock,
            'categoria' => $this->categoria,
            'activo' => $this->activo
        ];
    }
}