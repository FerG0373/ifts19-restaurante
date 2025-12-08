<?php
namespace App\DTOs;

use App\Models\PedidoDetalle;

class PedidoDetalleDTO {
    public int $productoId;
    public string $nombreProducto;
    public int $cantidad;
    public float $precioUnitario;
    public string $subtotal;  // Formateado para vista
    public ?string $instruccionesPreparacion;

    public static function fromModel(PedidoDetalle $detalle): self {
        $dto = new self();
        $dto->productoId = $detalle->getProductoId();
        $dto->nombreProducto = $detalle->getNombreProducto();
        $dto->cantidad = $detalle->getCantidad();
        $dto->precioUnitario = $detalle->getPrecioUnitario();
        $dto->subtotal = number_format($detalle->getSubtotal(), 2, ',', '.');
        $dto->instruccionesPreparacion = $detalle->getInstruccionesPreparacion();
        
        return $dto;
    }

    // Para crear desde el formulario
    public static function fromArray(array $data): self {
        $dto = new self();
        $dto->productoId = (int)$data['producto_id'];
        $dto->nombreProducto = $data['nombre_producto'] ?? '';
        $dto->cantidad = (int)$data['cantidad'];
        $dto->precioUnitario = (float)$data['precio_unitario'];
        $dto->instruccionesPreparacion = $data['instrucciones'] ?? null;
        
        return $dto;
    }
}