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
        string $passHash, 
        PerfilAcceso $perfilAcceso, 
        bool $activo = true
    ) {
        $this->id = $id;
        $this->passHash = $passHash;
        $this->perfilAcceso = $perfilAcceso;
        $this->activo = $activo;
    }

    // GETTERS
    public function getId(): ?int { return $this->id; }

    public function getPassHash(): string { return $this->passHash; }

    public function getPerfil(): PerfilAcceso { return $this->perfilAcceso; }

    public function isActivo(): bool { return $this->activo; }
}
?>