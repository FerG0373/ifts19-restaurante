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


    public function listarMesasActivas(): array {
        return $this->mesaRepository->listarMesas(true); 
    }

    
    public function listarTodasLasMesas(): array {
        return $this->mesaRepository->listarMesas(false);
    }
}