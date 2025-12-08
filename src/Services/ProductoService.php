<?php
namespace App\Services;

use App\Models\Producto;
use App\Repositories\ProductoRepository;
use RuntimeException;

class ProductoService {
    private ProductoRepository $productoRepository;

    public function __construct(ProductoRepository $productoRepository) {
        $this->productoRepository = $productoRepository;
    }

    // Método por defecto para la ruta principal (/producto)
    public function listarProductosActivos(): array {
        return $this->productoRepository->listarProductos(true); 
    }

    public function listarTodosLosProductos(): array {
        return $this->productoRepository->listarProductos(false);
    }

    public function mostrarDetalle(int $id): ?Producto {
        return $this->productoRepository->obtenerProductoPorId($id);
    }

    public function agregarProducto(Producto $producto): Producto {
        // Validar Unicidad de Nombre
        if ($this->productoRepository->existeNombre($producto->getNombre())) {
            throw new RuntimeException("El producto '{$producto->getNombre()}' ya se encuentra registrado.");
        }

        // Validar precio positivo
        if ($producto->getPrecio() <= 0) {
            throw new RuntimeException("El precio debe ser un valor positivo.");
        }

        // Validar stock no negativo
        if ($producto->getCantidadStock() < 0) {
            throw new RuntimeException("La cantidad de stock no puede ser negativa.");
        }

        return $this->productoRepository->insertarProducto($producto);
    }

    public function actualizarProducto(Producto $productoActualizado): void {    
        // Obtiene los datos actuales (antiguos) de la DB
        $productoAntiguo = $this->productoRepository->obtenerProductoPorId($productoActualizado->getId());

        if (!$productoAntiguo) {
            throw new RuntimeException("No se pudo encontrar el producto con ID {$productoActualizado->getId()} para actualizar.");
        }

        // Verifica que el Nombre no esté duplicado (si se cambió)
        if ($productoActualizado->getNombre() !== $productoAntiguo->getNombre()) {
            if ($this->productoRepository->existeNombre($productoActualizado->getNombre())) {
                throw new RuntimeException("El nombre '{$productoActualizado->getNombre()}' ya está registrado en el sistema.");
            }
        }

        // Validar precio positivo
        if ($productoActualizado->getPrecio() <= 0) {
            throw new RuntimeException("El precio debe ser un valor positivo.");
        }

        // Validar stock no negativo
        if ($productoActualizado->getCantidadStock() < 0) {
            throw new RuntimeException("La cantidad de stock no puede ser negativa.");
        }

        // Persistencia: Enviar el objeto actualizado al Repository
        $this->productoRepository->updateProducto($productoActualizado);
    }
}