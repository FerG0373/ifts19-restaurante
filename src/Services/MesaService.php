<?php
namespace App\Services;

use App\Models\Mesa;
use App\Repositories\MesaRepository;
use RuntimeException;
use InvalidArgumentException;


class MesaService {
    private MesaRepository $mesaRepository;

    public function __construct(MesaRepository $mesaRepository) {
        $this->mesaRepository = $mesaRepository;
    }


    public function listarMesasPorUbicacion(string $ubicacion): array {
        // Validación a nivel de Service (aunque el Controller ya lo hizo, es una buena práctica)
        if (!in_array($ubicacion, ['salon', 'exterior'])) {
            throw new InvalidArgumentException("Ubicación de mesa no válida.");
        }
        
        return $this->mesaRepository->listarMesasPorUbicacion($ubicacion);
    }
}