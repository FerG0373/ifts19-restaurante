<?php
namespace App\DTOs;

use App\Models\Personal;
use App\Models\Usuario;
use App\Shared\Enums\Puesto;
use App\Shared\Enums\Sexo;
use App\Shared\Enums\PerfilAcceso;
use DateTimeImmutable;
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


    // Convierte el DTO en el Objeto de Domino (Personal y Usuario). Realiza el mapeo de tipos (strings a Enums y DateTimeImmutable).
    public function toPersonalModel(): Personal {
        // Conversión de Tipos (mapeo a Enums y DateTimeImmutable)
        try {
            $fechaNacimiento = new DateTimeImmutable($this->fechaNacimiento);
            $sexo = Sexo::from($this->sexo);
            $puesto = Puesto::from($this->puesto);
            $perfilAcceso = PerfilAcceso::from($this->perfilAcceso);
            
        } catch (\Throwable $e) {
            // Captura errores si, por ejemplo, el Enum no existe o la fecha es inválida.
            throw new InvalidArgumentException("Error en el formato de datos de Sexo, Puesto, Perfil o Fecha de Nacimiento.", 0, $e);
        }

        // Creación del Objeto Usuario
        $usuario = new Usuario(
            null,  // null al inicio
            $perfilAcceso, 
            $this->passTextoPlano,
            true
        );

        // Creación del Objeto Personal
        $personal = new Personal(
            null,
            $this->dni,
            $this->nombre,
            $this->apellido,
            $fechaNacimiento,
            $this->email,
            $this->telefono,
            $sexo,
            $puesto,
            null,  // Fecha Contratación: null, se asigna en la DB y se recupera en el Repository.
            $usuario
        );

        return $personal;
    }    
}