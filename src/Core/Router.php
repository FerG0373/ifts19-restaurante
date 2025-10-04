<?php
namespace App\Core;

use App\Core\DataAccess;
use PDO;

/**
 * El Router se encarga de mapear las URLs entrantes a Vistas estáticas
 * o a Controladores específicos (MVC). También maneja la inyección de la DB.
 */
class Router {
    private string $rutaBaseVistas;
    private array $rutas = [];
    private DataAccess $dataAccess; // 👈 Guardamos el objeto DataAccess completo

    public function __construct(string $rutaBaseVistas, DataAccess $dataAccess) {
        $this->rutaBaseVistas = $rutaBaseVistas;
        $this->dataAccess = $dataAccess; // 👈 Guardamos el DataAccess inyectado
    }

    /**
     * Agrega una nueva ruta al mapa. El destino puede ser un string (nombre de archivo de vista)
     * o un array [Clase::class, 'metodo'] para Controladores.
     */
    public function agregarRuta(string $nombreRuta, mixed $destino, bool $enNavHeader): void {
        // CORRECCIÓN CLAVE: NO PREFIJAMOS la ruta base aquí.
        // Si es un string, ViewRenderer se encargará de buscar el archivo.
        $this->rutas[$nombreRuta] = [
            'destino' => $destino, 
            'nav' => $enNavHeader
        ];
    }

    /**
     * Procesa la URL solicitada, resuelve si es una vista estática o un controlador,
     * e inicia la ejecución de la lógica o el renderizado.
     */
    public function despacharRuta(ViewRenderer $renderer): void {
        $rutaSolicitada = $_GET['url'] ?? 'home';
        $rutaSolicitada = rtrim($rutaSolicitada, '/');

        if (!isset($this->rutas[$rutaSolicitada])) {
            // Ruta no encontrada, renderiza 404
            $renderer->renderizarVistaDesdeUrl(); 
            return;
        }

        $destino = $this->rutas[$rutaSolicitada]['destino'];

        // Caso 1: VISTA ESTÁTICA (El destino es un string, como '1.01-home.php')
        if (is_string($destino)) {
            // El ViewRenderer tiene la lógica para adjuntar la ruta base a este nombre de archivo
            $renderer->renderizarVistaDesdeUrl(); 

        // Caso 2: CONTROLADOR (El destino es un array, como [PersonalController::class, 'mostrarListado'])
        } elseif (is_array($destino) && count($destino) === 2) {
            
            [$controladorClase, $metodo] = $destino;

            // --- INYECCIÓN DE DEPENDENCIAS MANUAL ---
            // 1. Instanciar Repositorio: Le inyectamos el DataAccess que guardamos en el constructor del Router
            // Esto es crucial para que el Repositorio pueda obtener la conexión PDO.
            $personalRepository = new \App\Repositories\PersonalRepository($this->dataAccess);

            // 2. Instanciar Servicio: Le inyectamos el Repositorio
            $personalService = new \App\Services\PersonalService($personalRepository);
            
            // 3. Instanciar Controlador: Le inyectamos el Servicio y el Renderizador
            $controlador = new $controladorClase($personalService, $renderer);
            
            // 4. Ejecutar el método del Controlador (MVC)
            $controlador->$metodo();

        } else {
            // Manejar error de ruta mal configurada
            $renderer->renderizarVistaDesdeUrl(); 
        }
    }
    
    public function getRutas(): array {
        return $this->rutas;
    }
}
