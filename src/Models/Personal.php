<?php
namespace App\Models;

use DateTimeImmutable;
use DateTimeInterface;
use App\Shared\Enums\Sexo;
use App\Shared\Enums\Puesto;
use App\Models\Usuario;


class Personal {
    // ATRIBUTOS (PK y UQ)
    private ?int $id;  // PK + AI
    private string $dni;  // UQ
    private string $nombre;
    private string $apellido;
    private ?DateTimeImmutable  $fechaNacimiento;
    private string $email;  // UQ
    private string $telefono;
    private Sexo $sexo;
    private Puesto $puesto;
    private ?DateTimeImmutable $fechaContratacion;
    private ?Usuario $usuario; // Relación 1 a 1 con Usuario. Puede ser null si el empleado no tiene usuario.

    // CONSTRUCTOR
    public function __construct (
        ?int $id,
        string $dni,
        string $nombre,
        string $apellido,
        ?DateTimeInterface $fechaNacimiento,
        string $email,
        string $telefono,
        Sexo $sexo,
        Puesto $puesto,
        ?DateTimeInterface $fechaContratacion,
        ?Usuario $usuario
    ) {
        $this->id = $id;
        $this->dni = $dni;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->fechaNacimiento = new DateTimeImmutable($fechaNacimiento->format('Y-m-d'));
        $this->email = $email;
        $this->telefono = $telefono;
        $this->sexo = $sexo;
        $this->puesto = $puesto;
        $this->fechaContratacion = $fechaContratacion ? new DateTimeImmutable($fechaContratacion->format('Y-m-d')) : null;
        $this->usuario = $usuario;
    }

    // GETTERS
    public function getId(): ?int { return $this->id; }

    public function getDni(): string { return $this->dni; }

    public function getEmail(): string { return $this->email; }

    public function getNombre(): string { return $this->nombre; }

    public function getApellido(): string { return $this->apellido; }

    public function getTelefono(): string { return $this->telefono; }

    public function getFechaNacimiento(): DateTimeInterface { return $this->fechaNacimiento; }

    public function getSexo(): Sexo { return $this->sexo; }

    public function getPuesto(): Puesto { return $this->puesto; }

    public function getFechaContratacion(): DateTimeInterface { return $this->fechaContratacion; }
    
    public function getUsuario(): ?Usuario { return $this->usuario; }
    
    public function isActivo(): bool { return $this->usuario != null && $this->usuario->isActivo(); }
}
?>