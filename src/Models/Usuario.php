<?php
namespace App\Models;

use App\Shared\Enums\PerfilAcceso;


class Usuario {
    // ATRIBUTOS
    private int $id;
    private PerfilAcceso $perfilAcceso;
    private string $passHash;
    private bool $activo;

    public function __construct(
        int $id,
        PerfilAcceso $perfilAcceso, 
        string $passHash, 
        bool $activo = true
    ) {
        $this->id = $id;
        $this->perfilAcceso = $perfilAcceso;
        $this->passHash = $passHash;
        $this->activo = $activo;
    }

    // GETTERS
    public function getId(): ?int { return $this->id; }

    public function getPassHash(): string { return $this->passHash; }

    public function getPerfilAcceso(): PerfilAcceso { return $this->perfilAcceso; }

    public function isActivo(): bool { return $this->activo; }
}
?>