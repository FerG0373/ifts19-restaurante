<?php
namespace App\Models;

use App\Shared\Enums\PerfilAcceso;
use InvalidArgumentException;


class Usuario {
    // ATRIBUTOS
    private ?int $id;
    private PerfilAcceso $perfilAcceso;
    private ?string $passHash;  // Permite null para evitar pasar la contraseña en operaciones de Edición.
    private bool $activo;

    public function __construct(
        ?int $id,
        PerfilAcceso $perfilAcceso, 
        ?string $passHash, 
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

    // SETTERS
    public function setPassHash(string $passHash): void {
        if (empty($passHash)) {
            throw new InvalidArgumentException("El hash de la contraseña no puede ser una cadena vacía.");
        }
        $this->passHash = $passHash;
    }

    public function setActivo(bool $activo): void {
        $this->activo = $activo;
    }
}
?>