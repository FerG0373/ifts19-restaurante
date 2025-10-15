<?php
namespace App\DTOs;

use App\Models\Personal;

class PersonalVistaDTO
{
    // Atributos de Personal.
    public int $id;
    public string $dni;
    public string $nombre;
    public string $apellido;
    public string $email;
    public string $telefono;
    public string $fechaNacimiento;  // Formato de fecha para la vista.
    public string $sexo;
    public string $puesto;
    public string $fechaContratacion;    
    // Atributos de Usuario.
    public string $perfilAcceso;
    public bool $activo;


    // Factory Method: Crea el DTO a partir del Modelo de Dominio (Personal).
    public static function fromModel(Personal $personal): self {
        $usuario = $personal->getUsuario();
                
        if (!$usuario) {
            throw new \RuntimeException("El objeto Personal está incompleto, falta la entidad Usuario asociada.");
        }

        $dto = new self();        
        // --- Mapeo de Personal ---
        $dto->id = $personal->getId();
        $dto->dni = $personal->getDni();
        $dto->nombre = $personal->getNombre();
        $dto->apellido = $personal->getApellido();
        $dto->email = $personal->getEmail();
        $dto->telefono = $personal->getTelefono();        
        // Formateo para la vista
        $dto->fechaNacimiento = $personal->getFechaNacimiento()->format('d/m/Y');
        $dto->sexo = $personal->getSexo()->name; // Usamos el nombre del Enum para mostrar (ej: M).
        $dto->puesto = $personal->getPuesto()->name; 
        // Manejo de la fecha de contratación (puede ser null si el model lo permite).
        $fechaContratacion = $personal->getFechaContratacion();
        $dto->fechaContratacion = $fechaContratacion->format('d/m/Y');        
        // --- Mapeo de Usuario ---
        $dto->perfilAcceso = $usuario->getPerfilAcceso()->name;  // Usamos el nombre del Enum para mostrar (ej: ADMIN).
        $dto->activo = $usuario->isActivo();
        
        return $dto;
    }
}