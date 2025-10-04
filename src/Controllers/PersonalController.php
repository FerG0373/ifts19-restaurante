<?php
namespace App\Controllers;

use App\Services\PersonalService;
use App\Core\ViewRenderer;
use InvalidArgumentException;
use RuntimeException;


class PersonalController {
    private PersonalService $personalService;
    private ViewRenderer $viewRenderer;

    public function __construct(PersonalService $personalService, ViewRenderer $viewRenderer) {
        $this->personalService = $personalService;
        $this->viewRenderer = $viewRenderer;
    }

    // GET /personal
    public function listarPersonal(): void {
        try {
            $listaPersonal = $this->personalService->listarTodoElPersonal();

            $this->viewRenderer->renderizarVistaConDatos('2.00-personal', [
                'personal' => $listaPersonal,
                'titulo' => 'Listado de Personal del Restaurante'
            ]);

        } catch (\Exception $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public function listarPersonalActivo(): void {
        
    }



}
?>