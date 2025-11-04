<?php
namespace App\DTOs;

use InvalidArgumentException;


class PersonalAltaDTO
{
    public string $dni;
    public string $nombre;
    public string $apellido;
    public string $email;
    public string $telefono;
    public string $fechaNacimiento;
    public string $sexo;
    public string $puesto;
    public string $perfilAcceso;
    public string $passTextoPlano;
    private string $passConfirmacion;
    
    
    // Factory Method que construye el DTO a partir del array de datos del formulario ($_POST).
    public static function fromArray(array $datosInput): self {
        // Verificar que los campos críticos existan y no estén vacíos.
        $camposObligatorios = ['dni', 'nombre', 'apellido', 'email', 'fecha_nacimiento', 'sexo', 'puesto', 'perfil_acceso', 'pass', 'pass_confirmacion'];
        foreach ($camposObligatorios as $campo) {
            if (empty($datosInput[$campo])) { 
                throw new InvalidArgumentException("El campo '{$campo}' es obligatorio.");
            }
        }

        if ($datosInput['pass'] !== $datosInput['pass_confirmacion']) {
            throw new InvalidArgumentException("La contraseña y la confirmación no coinciden.");
        }

        $dto = new self();
        // Mapeo de array a propiedades del DTO (usando array access para los campos del formulario)
        $dto->dni = $datosInput['dni'];
        $dto->nombre = $datosInput['nombre'];
        $dto->apellido = $datosInput['apellido'];
        $dto->email = $datosInput['email'] ?? ''; // Asumimos opcional o vacío si no existe.
        $dto->telefono = $datosInput['telefono'] ?? '';
        $dto->fechaNacimiento = $datosInput['fecha_nacimiento'];
        $dto->sexo = strtolower($datosInput['sexo']);
        $dto->puesto = strtolower($datosInput['puesto']);
        $dto->perfilAcceso = strtolower($datosInput['perfil_acceso']);
        $dto->passTextoPlano = $datosInput['pass'];
        $dto->passConfirmacion = $datosInput['pass_confirmacion'];
        
        return $dto;
    }
}