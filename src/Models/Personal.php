<?php
namespace App\Models;

use DateTimeImmutable;
use DateTimeInterface;
use App\Enums\Sexo;
use App\Enums\Puesto;


class Personal {
    // ATRIBUTOS (PK y UQ)
    private ?int $id;  // PK + AI
    private string $dni;  // UQ
    private string $nombre;
    private string $apellido;
    private DateTimeImmutable  $fechaNacimiento;
    private string $email;  // UQ
    private string $telefono;
    private Sexo $sexo;
    private Puesto $puesto;
    private DateTimeImmutable $fechaContratacion;
    private bool $activo;

    // CONSTRUCTOR
    public function __construct (
        ?int $id,
        string $dni,
        string $nombre,
        string $apellido,
        DateTimeInterface $fechaNacimiento,
        string $email,
        string $telefono,
        Sexo $sexo,
        Puesto $puesto,
        DateTimeInterface $fechaContratacion,
        bool $activo = true
    ) {
        $this->id = $id;
        $this->dni = $dni;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->fechaNacimiento = $fechaNacimiento instanceof DateTimeImmutable ? $fechaNacimiento : new DateTimeImmutable($fechaNacimiento->format('Y-m-d'));
        $this->email = $email;
        $this->telefono = $telefono;
        $this->sexo = $sexo;
        $this->puesto = $puesto;
        $this->fechaContratacion = $fechaContratacion instanceof DateTimeImmutable ? $fechaContratacion : new DateTimeImmutable($fechaContratacion->format('Y-m-d'));
        $this->activo = $activo;
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
    
    public function isActivo(): bool { return $this->activo; }
}
?>