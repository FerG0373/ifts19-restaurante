<?php
namespace App\Services;

use App\Repositories\MesaRepository;
use App\Models\Mesa;
use RuntimeException;
use InvalidArgumentException;


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
}