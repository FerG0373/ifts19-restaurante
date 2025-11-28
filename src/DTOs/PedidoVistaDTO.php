<?php
namespace App\DTOs;

use App\Models\Pedido;
use App\DTOs\PedidoDetalleDTO; 

class PedidoVistaDTO {
    public int $id;
    public string $numeroMesa;
    public string $nombreMozo;
    public string $fechaHora;  // Formateado para vista
    public string $tipoPedido;
    public string $estadoPedido;
    public string $total;  // Formateado para vista
    public ?string $observaciones;
    
    /** @var PedidoDetalleDTO[] */
    public array $detalles;

    public static function fromModel(Pedido $pedido): self {
        $dto = new self();
        
        $dto->id = $pedido->getId();
        $dto->numeroMesa = $pedido->getNumeroMesa();  // ✅ Método correcto
        $dto->nombreMozo = $pedido->getNombreMozo();
        $dto->fechaHora = $pedido->getFechaHora()->format('d/m/Y H:i');
        $dto->tipoPedido = $pedido->getTipoPedido()->name;
        $dto->estadoPedido = strtoupper($pedido->getEstadoPedido()->value);
        $dto->total = number_format($pedido->getTotal(), 2, ',', '.');
        $dto->observaciones = $pedido->getObservaciones();
        
        // Mapear detalles
        $dto->detalles = array_map(
            fn($detalle) => PedidoDetalleDTO::fromModel($detalle),
            $pedido->getDetalles()
        );
        
        return $dto;
    }
}