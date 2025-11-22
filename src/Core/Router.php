<?php
namespace App\Core;

use App\Services\PersonalService;
use App\Services\MesaService;
use App\Controllers\PersonalController;
use App\Controllers\MesaController;
use App\Models\Mesa;
use Exception;

// Se encarga de mapear las URLs a vistas estáticas o controladores.
class Router {
    private array $rutas = [];
    private DataAccess $dataAccess;

    public function __construct(DataAccess $dataAccess) {
        $this->dataAccess = $dataAccess;
    }

    // Agrega una ruta al array. La variable $destino puede ser string (nombre de archivo de vista) o array [Clase::class, 'metodo'] para controladores, ya que mixed lo permite.
    public function agregarRuta(string $nombreRuta, mixed $destino, bool $enNavHeader, string $metodo = 'GET'): void {
        $this->rutas[$nombreRuta] = [
            'destino' => $destino,
            'nav' => $enNavHeader,
            'metodo' => strtoupper($metodo)  // Guarda el método en mayúsculas.
        ];
    }

    // Despacha la ruta solicitada. Si es una vista estática, la renderiza. Si es un controlador, lo instancia y llama al método.
    public function despacharRuta(ViewRenderer $renderer): void {
        $rutaSolicitada = $_GET['url'] ?? 'home';
        $rutaSolicitada = rtrim($rutaSolicitada, '/');
        
        $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';  // Obtener el método HTTP de la solicitud.

        // Buscar la ruta que coincide tanto con la URL solicitada como con el Método HTTP usado.
        $rutaEncontrada = null;
        foreach ($this->rutas as $nombreRuta => $definicionRuta) {
            if ($nombreRuta === $rutaSolicitada && $definicionRuta['metodo'] === $metodo) {
                $rutaEncontrada = $definicionRuta;
                break;
            }
        }
        
        // Verificar si se encontró la ruta.
        if (!$rutaEncontrada) {
            $renderer->renderizarVistaDesdeUrl();  // Si no se encuentra la ruta exacta, intentar renderizar la vista estática 404.
            return;
        }

        $destino = $rutaEncontrada['destino'];  // Puede ser string (vista) o array [Clase::class, 'metodo'] (controlador).
        
        if (is_string($destino)) {            
            $renderer->renderizarVistaDesdeUrl();
        // Verifica que es un array y que el array tenga exactamente 2 elementos: [Clase, 'metodo']. Ejemplo: [PersonalController::class, 'mostrarListado']).
        } elseif (is_array($destino) && count($destino) === 2) {            
            [$claseController, $metodo] = $destino;  // Destructuring assignment (asignación por desestructuración). Extrae los valores del array $destino into variables separadas.
            
            $service = $this->obtenerServiceParaControladores($claseController);
            $controlador = new $claseController($service, $renderer);  // Instancia el controlador dinámicamente con los servicios necesarios (inyección de dependencias). dynamic class instantiation
            
            $controlador->$metodo();

        } else {            
            $renderer->renderizarVistaDesdeUrl();  // Si el destino no es ni string ni array válido, renderiza la vista estática 404.
        }
    }


    private function obtenerServiceParaControladores(string $claseController) {
        $mapeoControllerService = [
            // Para este Controller, éste Service.
            PersonalController::class => PersonalService::class,
            MesaController::class => MesaService::class,
            // Agregar acá otros controladores:
        ];
        
        $claseService = $mapeoControllerService[$claseController] ?? null;
        if ($claseService) {
            return Container::getService($claseService);
        }
        throw new Exception("No se encontró service para el controlador: $claseController");
    }
    
    
    public function getRutas(): array {
        return $this->rutas;
    }
}
