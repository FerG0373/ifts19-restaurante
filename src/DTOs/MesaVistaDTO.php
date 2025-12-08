<?php
namespace App\DTOs;

use App\Models\Mesa;

class MesaVistaDTO {
    public ?int $id;
    public string $nroMesa;
    public int $capacidad;
    public string $ubicacion;
    public string $estadoMesa;
    public bool $activo;

    public ?int $mozoId; 
    public ?string $mozoNombreCompleto;

    private function __construct() {
        $this->mozoId = null;
        $this->mozoNombreCompleto = null;
    }

    
    public static function fromModel(Mesa $mesa): self {
        $dto = new self();
        $dto->id = $mesa->getId();
        $dto->nroMesa = $mesa->getNroMesa();
        $dto->capacidad = $mesa->getCapacidad();
        $dto->ubicacion = $mesa->getUbicacion()->value;
        $dto->estadoMesa = $mesa->getEstadoMesa()->value;
        $dto->activo = $mesa->isActivo();

        return $dto;
    }


    public function setMozoAsignado(?array $asignacion): void {
        if ($asignacion) {
            $this->mozoId = (int)$asignacion['personal_id'];
            // Combina nombre y apellido para una fácil visualización en la vista.
            $this->mozoNombreCompleto = $asignacion['nombre'] . ' ' . $asignacion['apellido'];
        }
    }
}