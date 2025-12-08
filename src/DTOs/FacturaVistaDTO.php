<?php
namespace App\DTOs;

use App\Models\Factura;

class FacturaVistaDTO {
    public int $id;
    public int $pedidoId;
    public string $fechaEmision;
    public string $subtotal;
    public string $impuestos;
    public string $total;
    public ?string $metodoPago;
    public string $estado;

    public static function fromModel(Factura $factura): self {
        $dto = new self();
        
        $dto->id = $factura->getId();
        $dto->pedidoId = $factura->getPedidoId();
        $dto->fechaEmision = $factura->getFechaEmision()->format('d/m/Y H:i');
        $dto->subtotal = number_format($factura->getSubtotal(), 2, ',', '.');
        $dto->impuestos = number_format($factura->getImpuestos(), 2, ',', '.');
        $dto->total = number_format($factura->getTotal(), 2, ',', '.');
        $dto->metodoPago = $factura->getMetodoPago() ? $factura->getMetodoPago()->name : null;
        $dto->estado = $factura->getEstado()->name;
        
        return $dto;
    }
}