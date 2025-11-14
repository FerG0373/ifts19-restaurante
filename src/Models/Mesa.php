<?php
namespace App\Models;

use App\Shared\Enums\Ubicacion;
use App\Shared\Enums\EstadoMesa;
use InvalidArgumentException;


class Mesa {
    private ?int $id;
    private string $nroMesa;
    private int $capacidad;
    private Ubicacion $ubicacion;
    private EstadoMesa $estadoMesa;
    private bool $activo;
    

    public function __construct(
        ?int $id,
        string $nroMesa,
        int $capacidad,
        Ubicacion $ubicacion,
        EstadoMesa $estadoMesa,
        bool $activo
    ) {
        if ($capacidad <= 0) {
            throw new InvalidArgumentException("La capacidad de la mesa debe ser un número positivo.");
        }
        
        $this->id = $id;
        $this->nroMesa = $nroMesa;
        $this->capacidad = $capacidad;
        $this->ubicacion = $ubicacion;
        $this->estadoMesa = $estadoMesa;
        $this->activo = $activo;
    }

    // --- Getters ---

    public function getId(): ?int { return $this->id; }

    public function getNroMesa(): string { return $this->nroMesa; }

    public function getCapacidad(): int { return $this->capacidad; }

    public function getUbicacion(): Ubicacion { return $this->ubicacion; }

    public function getEstadoMesa(): EstadoMesa { return $this->estadoMesa; }

    public function isActivo(): bool { return $this->activo; }

    // --- Setters (Opcional, pero útil para edición o inicialización en el Repository) ---
    
    public function setEstadoMesa(EstadoMesa $estadoMesa): void {
        $this->estadoMesa = $estadoMesa;
    }

    public function setActivo(bool $activo): void {
        $this->activo = $activo;
    }
}