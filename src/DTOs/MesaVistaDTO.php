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

    private function __construct() {}

    
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
}