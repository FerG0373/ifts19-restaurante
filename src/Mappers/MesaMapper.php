<?php
namespace App\Mappers;

use App\DTOs\MesaAltaDTO;
use App\Models\Mesa;
use App\Shared\Enums\Ubicacion;
use App\Shared\Enums\EstadoMesa;
use InvalidArgumentException;
use Throwable;


class MesaMapper {

    /// Convierte el DTO de Alta (validado) en un Objeto de Dominio Mesa.
    public static function fromDtoAlta(MesaAltaDTO $dto): Mesa {        
        // Conversión de strings a Enums (Validación de Ubicación)
        try {
            $ubicacion = Ubicacion::from($dto->ubicacion);            
        } catch (Throwable $e) {
            // Se lanza si el string de ubicación no existe en el Enum.
            throw new InvalidArgumentException("La ubicación seleccionada ('{$dto->ubicacion}') no es válida.", 0, $e);
        }
        // Creación del Objeto Mesa
        $mesa = new Mesa(
            null,  // id null ya que se asigna en la DB.
            $dto->nroMesa,
            $dto->capacidad,
            $ubicacion,
            EstadoMesa::LIBRE,  // Estado inicial al dar de alta.
            true
        );

        return $mesa;
    }
    
}