<?php
namespace App\DTOs;

use InvalidArgumentException;

class PedidoAltaDTO {
    public int $mesa_id;
    public int $personal_id;
    public string $tipo_pedido;
    public ?string $observaciones;
    public array $detalles;

    public function __construct(
        int $mesa_id,
        int $personal_id,
        string $tipo_pedido,
        array $detalles,
        ?string $observaciones = null
    ) {
        $this->mesa_id = $mesa_id;
        $this->personal_id = $personal_id;
        $this->tipo_pedido = $tipo_pedido;
        $this->detalles = $detalles;
        $this->observaciones = $observaciones;
    }

    public static function fromArray(array $datos): self {
        // Validar campos requeridos
        if (empty($datos['mesa_id'])) {
            throw new InvalidArgumentException('El ID de la mesa es requerido');
        }
        if (empty($datos['personal_id'])) {
            throw new InvalidArgumentException('El ID del personal es requerido');
        }
        if (empty($datos['tipo_pedido'])) {
            throw new InvalidArgumentException('El tipo de pedido es requerido');
        }

        // Procesar detalles del pedido
        $detalles = [];
        
        if (!empty($datos['productos']) && is_array($datos['productos'])) {
            foreach ($datos['productos'] as $index => $productoData) {
                if (empty($productoData['producto_id'])) {
                    continue;
                }
                
                $cantidad = isset($productoData['cantidad']) ? (int)$productoData['cantidad'] : 0;
                
                if ($cantidad > 0) {
                    $detalles[] = [
                        'producto_id' => (int)$productoData['producto_id'],
                        'cantidad' => $cantidad,
                        'instrucciones' => $productoData['instrucciones'] ?? null
                    ];
                }
            }
        }

        if (empty($detalles)) {
            throw new InvalidArgumentException('Debe agregar al menos un producto al pedido');
        }

        return new self(
            mesa_id: (int)$datos['mesa_id'],
            personal_id: (int)$datos['personal_id'],
            tipo_pedido: $datos['tipo_pedido'],
            detalles: $detalles,
            observaciones: $datos['observaciones'] ?? null
        );
    }
}