<?php
namespace App\Mappers;

use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\DTOs\PedidoAltaDTO;
use App\Shared\Enums\TipoPedido;
use App\Shared\Enums\EstadoPedido;
use DateTimeImmutable;

class PedidoMapper {
    
    public static function fromDtoAlta(PedidoAltaDTO $dto, array $productosInfo): Pedido {
        // Calcular el total y preparar los detalles
        $total = 0;
        $detalles = [];
        
        foreach ($dto->detalles as $detalle) {
            $producto = $productosInfo[$detalle['producto_id']] ?? null;
            
            if (!$producto) {
                throw new \RuntimeException("Producto con ID {$detalle['producto_id']} no encontrado");
            }
            
            // Crear detalle con constructor con parámetros opcionales
            $detallePedido = new PedidoDetalle(
                id: null,
                pedidoId: null,  // Se asignará después de guardar el pedido
                productoId: $detalle['producto_id'],
                nombreProducto: $producto['nombre'] ?? '',
                cantidad: $detalle['cantidad'],
                precioUnitario: $producto['precio'],
                instruccionesPreparacion: $detalle['instrucciones'] ?? null
            );
            
            $total += $producto['precio'] * $detalle['cantidad'];
            $detalles[] = $detallePedido;
        }
        
        // Crear el pedido con todos los parámetros requeridos
        $pedido = new Pedido(
            id: null,
            mesaId: $dto->mesa_id,
            numeroMesa: '',  // Se obtendrá del repository
            personalId: $dto->personal_id,
            nombreMozo: '',  // Se obtendrá del repository
            fechaHora: new DateTimeImmutable(),
            tipoPedido: TipoPedido::from($dto->tipo_pedido),
            estadoPedido: EstadoPedido::from('pendiente'),
            total: $total,
            observaciones: $dto->observaciones,
            detalles: $detalles
        );
        
        return $pedido;
    }
}