<?php
namespace App\Models;

use App\Shared\Enums\MetodoPago;
use App\Shared\Enums\EstadoFactura;
use DateTimeImmutable;
use DateTimeInterface;

class Factura {
    private ?int $id;
    private int $pedidoId;
    private DateTimeImmutable $fechaEmision;
    private float $subtotal;
    private float $impuestos;
    private float $total;
    private ?MetodoPago $metodoPago;
    private EstadoFactura $estado;

    public function __construct(
        ?int $id,
        int $pedidoId,
        DateTimeInterface $fechaEmision,
        float $subtotal,
        float $impuestos,
        float $total,
        ?MetodoPago $metodoPago,
        EstadoFactura $estado
    ) {
        $this->id = $id;
        $this->pedidoId = $pedidoId;
        $this->fechaEmision = new DateTimeImmutable($fechaEmision->format('Y-m-d H:i:s'));
        $this->subtotal = $subtotal;
        $this->impuestos = $impuestos;
        $this->total = $total;
        $this->metodoPago = $metodoPago;
        $this->estado = $estado;
    }

    // GETTERS
    public function getId(): ?int {
        return $this->id;
    }

    public function getPedidoId(): int {
        return $this->pedidoId;
    }

    public function getFechaEmision(): DateTimeImmutable {
        return $this->fechaEmision;
    }

    public function getSubtotal(): float {
        return $this->subtotal;
    }

    public function getImpuestos(): float {
        return $this->impuestos;
    }

    public function getTotal(): float {
        return $this->total;
    }

    public function getMetodoPago(): ?MetodoPago {
        return $this->metodoPago;
    }

    public function getEstado(): EstadoFactura {
        return $this->estado;
    }

    // SETTERS
    public function setMetodoPago(MetodoPago $metodoPago): void {
        $this->metodoPago = $metodoPago;
    }

    public function setEstado(EstadoFactura $estado): void {
        $this->estado = $estado;
    }

    // MÉTODOS DE CÁLCULO
    public static function calcularImpuestos(float $subtotal, float $porcentaje = 21.0): float {
        return round($subtotal * ($porcentaje / 100), 2);
    }

    public static function calcularTotal(float $subtotal, float $impuestos): float {
        return round($subtotal + $impuestos, 2);
    }
}