<?php
namespace App\Controllers;

use App\Services\MesaService;
use App\Core\ViewRenderer;
use App\DTOs\MesaVistaDTO;
use RuntimeException;


class MesaController {
    private MesaService $mesaService;
    private ViewRenderer $viewRenderer;

    public function __construct(MesaService $mesaService, ViewRenderer $viewRenderer) {
        $this->mesaService = $mesaService;
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Muestra el tablero de mesas, filtrando por ubicación (Salón o Exterior).
     * GET /mesa?ubicacion=salon o /mesa?ubicacion=exterior
     */
    public function listarMesasSegunUbicacion(): void {
        $exito = $_SESSION['msj_exito'] ?? null;
        if (isset($_SESSION['msj_exito'])) {
            unset($_SESSION['msj_exito']);
        }

        try {
            // Determina la ubicación activa de la URL
            $ubicacionActiva = $_GET['ubicacion'] ?? 'salon'; // 'salon' es el valor por defecto.

            // Valida que la ubicación sea una de las permitidas
            $ubicacionesPermitidas = ['salon', 'exterior'];
            if (!in_array($ubicacionActiva, $ubicacionesPermitidas)) {
                // Si la ubicación no es válida, usamos el valor por defecto.
                $ubicacionActiva = 'salon';
            }

            // Obtiene las mesas del Service filtradas por la ubicación activa.
            $listaMesaModelos = $this->mesaService->listarMesasPorUbicacion($ubicacionActiva);
            
            $titulo = 'Tablero de Mesas Operativas';
            
            // Mapeo de Modelo a DTO de Vista.
            $listaDTOs = array_map(function($mesaModelo) {
                return MesaVistaDTO::fromModel($mesaModelo);
            }, $listaMesaModelos);

            // Renderiza la vista con los datos y la ubicación activa.
            $this->viewRenderer->renderizarVistaConDatos('3.00-mesa', [
                'mesas' => $listaDTOs,
                'titulo' => $titulo, 
                'ubicacionActiva' => $ubicacionActiva,
                'exito' => $exito
            ]);

        } catch (\Exception $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}