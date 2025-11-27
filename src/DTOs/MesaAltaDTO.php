<?php
namespace App\DTOs;

use InvalidArgumentException;


class MesaAltaDTO
{
    // Propiedades que representan los campos del formulario
    public string $nroMesa;
    public int $capacidad;
    public string $ubicacion;
    
    
    // Factory Method que construye y valida el DTO a partir del array de datos del formulario ($_POST).
    public static function fromArray(array $datosInput): self {
        // Verificar que los campos críticos existan y no estén vacíos.
        $camposObligatorios = ['nroMesa', 'capacidad', 'ubicacion'];
        foreach ($camposObligatorios as $campo) {
            if (empty($datosInput[$campo])) { 
                throw new InvalidArgumentException("El campo '{$campo}' es obligatorio.");
            }
        }
        
        // Validación de formato/tipo para capacidad.
        if (!is_numeric($datosInput['capacidad']) || $datosInput['capacidad'] <= 0) {
            throw new InvalidArgumentException("La capacidad debe ser un número entero positivo.");
        }
        
        $dto = new self();        
        // Mapeo de array a propiedades del DTO.
        $dto->nroMesa = trim($datosInput['nroMesa']);
        $dto->capacidad = (int)$datosInput['capacidad'];
        $dto->ubicacion = strtolower(trim($datosInput['ubicacion'])); 
        
        return $dto;
    }
}