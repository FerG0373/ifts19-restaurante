<?php
namespace App\DTOs;

use InvalidArgumentException;

class ProductoAltaDTO {
    public string $nombre;
    public string $descripcion;
    public float $precio;
    public int $cantidadStock;
    public string $categoria;
    
    // Factory Method que construye el DTO a partir del array de datos del formulario ($_POST)
    public static function fromArray(array $datosInput): self {
        // Verificar que los campos críticos existan y no estén vacíos
        $camposObligatorios = ['nombre', 'precio', 'cantidad_stock', 'categoria'];
        foreach ($camposObligatorios as $campo) {
            if (empty($datosInput[$campo])) { 
                throw new InvalidArgumentException("El campo '{$campo}' es obligatorio.");
            }
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
        $dto->nombre = trim($datosInput['nombre']);
        $dto->descripcion = trim($datosInput['descripcion'] ?? '');
        $dto->precio = (float)$datosInput['precio'];
        $dto->cantidadStock = (int)$datosInput['cantidad_stock'];
        $dto->categoria = strtolower(trim($datosInput['categoria']));
        
        return $dto;
    }
}