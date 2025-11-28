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
        // Llama al Repository para la persistencia.
        $mesaConId = $this->mesaRepository->insertarMesa($mesa);

        return $mesaConId;
    }


    public function bajaMesa(int $id): Mesa {
        
        // 1. Obtener la mesa actual (para validación y posterior retorno).
        $mesa = $this->mesaRepository->obtenerMesaPorId($id);

        if (!$mesa) {
            throw new RuntimeException("La mesa con ID {$id} no fue encontrada.");
        }
        
        // 2. Lógica de Negocio: No permitir dar de baja si está ocupada/reservada.
        if ($mesa->getEstadoMesa() !== EstadoMesa::LIBRE) {
            throw new RuntimeException("No se puede dar de baja la mesa N° {$mesa->getNroMesa()} porque su estado actual es " . strtoupper($mesa->getEstadoMesa()->value) . ".");
        }
        
        // 3. Llamar al Repository para actualizar el campo 'activo'.
        $mesaActualizada = $this->mesaRepository->desactivarMesa($mesa);

        return $mesaActualizada;
    }
}