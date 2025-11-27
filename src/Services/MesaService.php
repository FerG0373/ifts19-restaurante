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
        return $this->mesaRepository->listarMesasPorUbicacion($ubicacion);
    }
}