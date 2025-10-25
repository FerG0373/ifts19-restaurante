<?php
namespace App\DTOs;

use App\Models\Personal;
use App\Shared\Enums\Puesto;
use App\Shared\Enums\Sexo;
use App\Shared\Enums\PerfilAcceso;
use DateTimeImmutable;
use InvalidArgumentException;
use RuntimeException;


class PersonalEdicionDTO {
    public int $id;  // Se necesita el ID para saber qué registro actualizar.
    public string $dni;
    public string $nombre;
    public string $apellido;
    public string $email;
    public string $telefono;
    public string $fechaNacimiento;
    public string $sexo;
    public string $puesto;
    public string $perfilAcceso;
    public bool $activo;
        
    // Factory Method para validar datos DESDE el Formulario POST
    public static function fromArray(array $datosInput): self {
        // Validación de campos obligatorios para edición (ID es la clave)
        $camposObligatorios = ['id', 'dni', 'nombre', 'apellido', 'email', 'fecha_nacimiento', 'sexo', 'puesto', 'perfil_acceso', 'activo'];
        foreach ($camposObligatorios as $campo) {
            if (empty($datosInput[$campo])) { 
                throw new InvalidArgumentException("El campo '{$campo}' es obligatorio para la edición.");
            }
        }
        
        $dto = new self();
        $dto->id = (int)$datosInput['id'];
        $dto->dni = $datosInput['dni'];
        $dto->nombre = $datosInput['nombre'];
        $dto->apellido = $datosInput['apellido'];
        $dto->email = $datosInput['email'];
        $dto->telefono = $datosInput['telefono'] ?? '';
        $dto->fechaNacimiento = $datosInput['fecha_nacimiento'];
        $dto->sexo = strtolower($datosInput['sexo']);
        $dto->puesto = strtolower($datosInput['puesto']);
        $dto->perfilAcceso = strtolower($datosInput['perfil_acceso']);
        $dto->activo = ($datosInput['activo'] === '1');
        
        return $dto;
    }

    // Convierte el DTO a un array asociativo para precargar el formulario con el ViewRenderer.
    public function toArray(): array {
        return [
            'id' => $this->id,
            'dni' => $this->dni,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'fecha_nacimiento' => $this->fechaNacimiento,
            'sexo' => $this->sexo,
            'puesto' => $this->puesto,
            'perfil_acceso' => $this->perfilAcceso,
            'activo' => $this->activo
        ];
    }
}