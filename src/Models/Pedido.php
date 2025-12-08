<?php
namespace App\Models;

use App\Shared\Enums\TipoPedido;
use App\Shared\Enums\EstadoPedido;
use DateTimeImmutable;
use DateTimeInterface;

class Pedido {
    private ?int $id;
    private int $mesaId;
    private string $numeroMesa;  // Para mostrar sin joins
    private int $personalId;
    private string $nombreMozo;  // Para mostrar sin joins
    private DateTimeImmutable $fechaHora;
    private TipoPedido $tipoPedido;
    private EstadoPedido $estadoPedido;
    private float $total;
    private ?string $observaciones;
    
    /** @var DetallePedido[] */
    private array $detalles;

    public function __construct(
        ?int $id,
        int $mesaId,
        string $numeroMesa,
        int $personalId,
        string $nombreMozo,
        DateTimeInterface $fechaHora,
        TipoPedido $tipoPedido,
        EstadoPedido $estadoPedido,
        float $total,
        ?string $observaciones = null,
        array $detalles = []
    ) {
        $this->id = $id;
        $this->mesaId = $mesaId;
        $this->numeroMesa = $numeroMesa;
        $this->personalId = $personalId;
        $this->nombreMozo = $nombreMozo;
        $this->fechaHora = new DateTimeImmutable($fechaHora->format('Y-m-d H:i:s'));
        $this->tipoPedido = $tipoPedido;
        $this->estadoPedido = $estadoPedido;
        $this->total = $total;
        $this->observaciones = $observaciones;
        $this->detalles = $detalles;
    }

    // GETTERS
    public function getId(): ?int {
        return $this->id;
    }

    public function getMesaId(): int {
        return $this->mesaId;
    }

    public function getNumeroMesa(): string {
        return $this->numeroMesa;
    }

    public function getPersonalId(): int {
        return $this->personalId;
    }

    public function getNombreMozo(): string {
        return $this->nombreMozo;
    }

    public function getFechaHora(): DateTimeImmutable {
        return $this->fechaHora;
    }

    public function getTipoPedido(): TipoPedido {
        return $this->tipoPedido;
    }

    public function getEstadoPedido(): EstadoPedido {
        return $this->estadoPedido;
    }

    public function getTotal(): float {
        return $this->total;
    }

    public function getObservaciones(): ?string {
        return $this->observaciones;
    }

    /**
     * @return DetallePedido[]
     */
    public function getDetalles(): array {
        return $this->detalles;
    }

    // SETTERS
    public function setEstadoPedido(EstadoPedido $estado): void {
        $this->estadoPedido = $estado;
    }

    public function setDetalles(array $detalles): void {
        $this->detalles = $detalles;
    }

    public function setTotal(float $total): void {
        $this->total = $total;
    }

    // MÉTODO CALCULADO
    public function calcularTotal(): float {
        $total = 0;
        foreach ($this->detalles as $detalle) {
            $total += $detalle->getSubtotal();
        }
        return $total;
    }
}