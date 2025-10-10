<?php
namespace App\Services;

use App\Models\Personal;
use App\Repositories\PersonalRepository;
use App\Shared\Enums\Puesto;
use App\Shared\Enums\Sexo;
use InvalidArgumentException;
use RuntimeException;
use DateTimeImmutable;


class PersonalService {
    private PersonalRepository $personalRepository;

    public function __construct(PersonalRepository $personalRepository) {
        $this->personalRepository = $personalRepository;
    }

    // Método por defecto para la ruta principal (/personal)
    public function listarPersonalActivo(): array {
        return $this->personalRepository->listarPersonal(true); 
    }


    public function listarTodoElPersonal(): array {
        return $this->personalRepository->listarPersonal(false);
    }


    public function mostrarDetalle(int $id): ?Personal {
        return $this->personalRepository->obtenerPersonalPorId($id);
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
            $fechaContratacion,
            null
        );
        

        // PERSISTENCIA (El Repositorio inserta y devuelve la versión final con ID)
        $personalGuardado = $this->personalRepository->insertarPersonal($personal);

        return $personalGuardado;
    }
}
?>