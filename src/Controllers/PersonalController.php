<?php
namespace App\Controllers;

use App\Services\PersonalService;
use App\Core\ViewRenderer;


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
            // Determinar el filtro a partir del parámetro GET.
            $filtro = $_GET['filtro'] ?? 'activo';  // Por defecto, solo se ve el personal activo.
            $esActivo = ($filtro === 'activo');
            
            $listaPersonal = [];
            // Ejecutar lógica de negocio basada en el filtro.
            if ($esActivo) {
                $listaPersonal = $this->personalService->listarPersonalActivo();  // Llama al método que lista al personal activo (comportamiento por defecto).
                $titulo = 'Listado de Personal';                
            } else {               
                $listaPersonal = $this->personalService->listarTodoElPersonal();  // Llama al método que lista a TODO el personal (activos e inactivos).
                $titulo = 'Listado de Personal (Activos e Inactivos)';
            }
            // Renderizar la vista con los datos.
            $this->viewRenderer->renderizarVistaConDatos('2.00-personal', [
                'personal' => $listaPersonal,
                'titulo' => $titulo, // Usamos el título dinámico para reflejar el filtro.
                'esActivo' => $esActivo,
                'urlVerActivos' => 'personal',  // URL por defecto, sin parámetros
                'urlVerTodos' => 'personal?filtro=todos'  // URL con parámetro para ver a todos (incluye inactivos).
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