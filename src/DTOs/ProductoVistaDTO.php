<?php
namespace App\DTOs;

use App\Models\Producto;

class ProductoVistaDTO {
    public int $id;
    public string $nombre;
    public string $descripcion;
    public string $precio;  // Formateado para la vista
    public int $cantidadStock;
    public string $categoria;
    public bool $activo;

    // Factory Method: Crea el DTO a partir del Modelo de Dominio (Producto)
    public static function fromModel(Producto $producto): self {
        $dto = new self();
        
        $dto->id = $producto->getId();
        $dto->nombre = $producto->getNombre();
        $dto->descripcion = $producto->getDescripcion();
        $dto->precio = number_format($producto->getPrecio(), 2, ',', '.');  // Formato: 1.234,56
        $dto->cantidadStock = $producto->getCantidadStock();
        $dto->categoria = $producto->getCategoria()->name;  // Nombre del Enum
        $dto->activo = $producto->isActivo();
        
        return $dto;
    }
}