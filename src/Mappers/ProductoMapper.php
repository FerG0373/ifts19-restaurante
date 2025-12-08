<?php
namespace App\Mappers;

use App\DTOs\ProductoEdicionDTO;
use App\DTOs\ProductoAltaDTO;
use App\Models\Producto;
use App\Shared\Enums\Categoria;
use InvalidArgumentException;

class ProductoMapper {
    // Mapea un Modelo de Producto (obtenido de la DB) a un DTO de Edición (para la vista/formulario)
    public static function toDtoEdicion(Producto $producto): ProductoEdicionDTO {
        $dto = new ProductoEdicionDTO();
        $dto->id = $producto->getId();
        $dto->nombre = $producto->getNombre();
        $dto->descripcion = $producto->getDescripcion();
        $dto->precio = $producto->getPrecio();
        $dto->cantidadStock = $producto->getCantidadStock();
        $dto->categoria = strtolower($producto->getCategoria()->value);
        $dto->activo = $producto->isActivo();
        
        return $dto;
    }

    // Mapea un DTO de Edición (validado desde el POST) a un Modelo de Producto (para el Service/Repository)
    public static function fromDtoEdicion(ProductoEdicionDTO $dto): Producto {
        try {
            // Conversión de Tipos
            $categoria = Categoria::from($dto->categoria);
            
        } catch (\Throwable $e) {
            throw new InvalidArgumentException("Error en el formato de datos: Categoría inválida.", 0, $e);
        }

        // Crea el objeto Producto con la información actualizada
        $producto = new Producto(
            $dto->id,  // CLAVE: Se pasa el ID
            $dto->nombre,
            $dto->descripcion,
            $dto->precio,
            $dto->cantidadStock,
            $categoria,
            $dto->activo
        );

        return $producto;
    }

    // Convierte el DTO en el Objeto de Dominio (Producto). Realiza el mapeo de tipos (strings a Enums)
    public static function fromDtoAlta(ProductoAltaDTO $dto): Producto {
        // Conversión de Tipos (mapeo a Enum)
        try {
            $categoria = Categoria::from($dto->categoria);
            
        } catch (\Throwable $e) {
            // Captura errores si, por ejemplo, el Enum no existe
            throw new InvalidArgumentException("Error en el formato de datos de Categoría.", 0, $e);
        }

        // Creación del Objeto Producto
        $producto = new Producto(
            null,  // ID null al inicio (será asignado por la DB)
            $dto->nombre,
            $dto->descripcion,
            $dto->precio,
            $dto->cantidadStock,
            $categoria,
            true  // Activo por defecto en alta
        );

        return $producto;
    }
}