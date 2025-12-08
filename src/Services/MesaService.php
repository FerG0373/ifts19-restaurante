<?php
namespace App\Services;

use App\Repositories\MesaRepository;
use App\Models\Mesa;
use App\Shared\Enums\EstadoMesa;
use RuntimeException;


class MesaService {
    private MesaRepository $mesaRepository;

    public function __construct(MesaRepository $mesaRepository) {
        $this->mesaRepository = $mesaRepository;
    }


    public function listarMesasPorUbicacion(string $ubicacion): array {        
        return $this->mesaRepository->listarMesasPorUbicacion($ubicacion);
    }

    
    public function agregarMesa(Mesa $mesa): Mesa {        
        $mesaConId = $this->mesaRepository->insertarMesa($mesa);  // Llama al Repository para la persistencia.

        return $mesaConId;
    }


    public function eliminarMesa(int $id): Mesa {        
        // Obtiene la mesa actual (para validación y posterior retorno).
        $mesa = $this->mesaRepository->obtenerMesaPorId($id);

        if (!$mesa) {
            throw new RuntimeException("La mesa con ID {$id} no fue encontrada.");
        }
        
        // Lógica de Negocio: No permitir dar de baja si está ocupada/reservada.
        if ($mesa->getEstadoMesa() !== EstadoMesa::LIBRE) {
            throw new RuntimeException("No se puede dar de baja la mesa N° {$mesa->getNroMesa()} porque su estado actual es " . strtoupper($mesa->getEstadoMesa()->value) . ".");
        }
        
        // Llama al Repository para actualizar el campo 'activo'.
        $mesaActualizada = $this->mesaRepository->desactivarMesa($mesa);

        return $mesaActualizada;
    }


    public function asignarMozo(int $idMesa, int $idPersonal): void {
        // Valida que la mesa exista.
        $mesa = $this->mesaRepository->obtenerMesaPorId($idMesa);

        if (!$mesa) {
            throw new RuntimeException("La mesa con ID {$idMesa} no fue encontrada.");
        }

        $this->mesaRepository->insertarAsignacionMozo($idMesa, $idPersonal);
    }


    public function obtenerAsignacionActiva(int $idMesa): ?array {
        return $this->mesaRepository->obtenerAsignacionActiva($idMesa);
    }


    public function finalizarAsignacionMozo(int $idMesa, int $idPersonal): void {
    // Aquí puedes agregar validaciones de negocio si las necesitas.
    // Por ejemplo: ¿Es realmente el mozo asignado? ¿La mesa sigue libre?
    
    $this->mesaRepository->finalizarAsignacionMozo($idMesa, $idPersonal);
}
}