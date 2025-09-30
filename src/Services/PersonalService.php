<?php
namespace App\Services;

namespace App\Services;

use App\Models\Personal;
use App\Repositories\PersonalRepository;
use App\Enums\Puesto;
use App\Enums\Sexo;
use InvalidArgumentException;
use RuntimeException;
use DateTimeImmutable;

class PersonalService {
    private PersonalRepository $personalRepository;

    public function __construct(PersonalRepository $personalRepository) {
        $this->personalRepository = $personalRepository;
    }

    public function altaPersonal(array $datos): Personal {
        // VALIDACIONES DE NEGOCIO
        if ($this->personalRepository->existeDni($datos['dni'] ?? '')) {
            throw new RuntimeException("El DNI ya está registrado en el sistema.");
        }
        if ($this->personalRepository->existeEmail($datos['email'] ?? '')) {
            throw new RuntimeException("El Email ya está registrado en el sistema.");
        }
        
        // CONVERSIÓN DE TIPOS (Mapeo de strings a objetos de dominio)
        try {
            $fechaNacimiento = new DateTimeImmutable($datos['fechaNacimiento']);
            $fechaContratacion = new DateTimeImmutable($datos['fechaContratacion']);
            $sexo = Sexo::from($datos['sexo']);
            $puesto = Puesto::from($datos['puesto']);
            
        } catch (\Throwable $e) {
            // Captura errores de valores inválidos (ej: 'sexo' no es 'm' ni 'f')
            throw new InvalidArgumentException("Datos de fecha, sexo o puesto inválidos.");
        }

        // CREACIÓN DEL OBJETO DE DOMINIO
        $personal = new Personal(
            null,  // ID inicial, será generado en la DB
            $datos['dni'],
            $datos['nombre'],
            $datos['apellido'],
            $fechaNacimiento,
            $datos['email'],
            $datos['telefono'],
            $sexo,
            $puesto,
            $fechaContratacion
        );

        // PERSISTENCIA (El Repositorio inserta y devuelve la versión final con ID)
        $personalGuardado = $this->personalRepository->insertarPersonal($personal);

        return $personalGuardado;
    }

    public function listarTodoElPersonal(): array {
        return $this->personalRepository->listarPersonal();
    }
}
?>