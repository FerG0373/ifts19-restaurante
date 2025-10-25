<?php
namespace App\Mappers;

use App\DTOs\PersonalEdicionDTO;
use App\Models\Personal;
use App\Models\Usuario;
use App\Shared\Enums\Puesto;
use App\Shared\Enums\Sexo;
use App\Shared\Enums\PerfilAcceso;
use DateTimeImmutable;
use InvalidArgumentException;


class PersonalMapper {
    // Mapea un Modelo de Personal (obtenido de la DB) a un DTO de Edición (para la vista/formulario).
    public static function toDtoEdicion (Personal $personal): PersonalEdicionDTO {
        $usuario = $personal->getUsuario();
        
        if (!$usuario) {
            throw new \RuntimeException("El objeto Personal está incompleto, falta la entidad Usuario asociada.");
        }
        
        $dto = new PersonalEdicionDTO();
        $dto->id = $personal->getId();
        $dto->dni = $personal->getDni();
        $dto->nombre = $personal->getNombre();
        $dto->apellido = $personal->getApellido();
        $dto->email = $personal->getEmail();
        $dto->telefono = $personal->getTelefono();        
        // Formato 'Y-m-d' es necesario para el input type="date"
        $dto->fechaNacimiento = $personal->getFechaNacimiento()->format('Y-m-d');  // Formato 'Y-m-d' es estándar para <input type="date">
        // Mapeo a strings en minúscula para precargar selects/radios
        $dto->sexo = strtolower($personal->getSexo()->value);
        $dto->puesto = strtolower($personal->getPuesto()->value);
        $dto->perfilAcceso = strtolower($usuario->getPerfilAcceso()->value);
        $dto->activo = $usuario->isActivo() ? '1' : '0';
        
        return $dto;
    }

    // Mapea un DTO de Edición (validado desde el POST) a un Modelo de Personal (para el Service/Repository).
    public static function fromDtoEdicion(PersonalEdicionDTO $dto): Personal {
        try {
            // Conversión de Tipos
            $fechaNacimiento = new DateTimeImmutable($dto->fechaNacimiento);
            $sexo = Sexo::from($dto->sexo);
            $puesto = Puesto::from($dto->puesto);
            $perfilAcceso = PerfilAcceso::from($dto->perfilAcceso);
            
        } catch (\Throwable $e) {
            throw new InvalidArgumentException("Error en el formato de datos: Sexo, Puesto, Perfil o Fecha de Nacimiento.", 0, $e);
        }

        // Crea un objeto Usuario (solo con la información de Edición).
        $usuario = new Usuario(
             null,  // El ID se recupera en el Service.
             $perfilAcceso, 
             null,  // La contraseña se ignora en edición.
             $dto->activo 
        );

        // Crea el objeto Personal con la información actualizada.
        $personal = new Personal(
            $dto->id, // CLAVE: Se pasa el ID
            $dto->dni,
            $dto->nombre,
            $dto->apellido,
            $fechaNacimiento,
            $dto->email,
            $dto->telefono,
            $sexo,
            $puesto,
            null,  // La fecha de contratación se mantiene, el Service la manejará.
            $usuario  // El objeto Usuario parcial.
        );

        return $personal;
    } 
}