<?php
namespace App\Core;

use App\Services\PersonalService;
use App\Services\MesaService;
use App\Services\AuthService;
use App\Controllers\PersonalController;
use App\Controllers\MesaController;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use Exception;

// Se encarga de mapear las URLs a vistas estáticas o controladores.
class Router {
    private array $rutas = [];
    private DataAccess $dataAccess;

    public function __construct(DataAccess $dataAccess) {
        $this->dataAccess = $dataAccess;
    }


    // ========== MÉTODOS PÚBLICOS ==========

    // Agrega una ruta al array. La variable $destino puede ser string (nombre de archivo de vista) o array [Clase::class, 'metodo'] para controladores, ya que mixed lo permite.
    public function agregarRuta(string $nombreRuta, mixed $destino, bool $enNavHeader, string $metodo = 'GET', ?array $middlewares = null): void {
        $this->rutas[$nombreRuta] = [
            'destino' => $destino,
            'nav' => $enNavHeader,
            'metodo' => strtoupper($metodo),  // Guarda el método en mayúsculas.
            'middlewares' => $middlewares
        ];
    }

    /***
     * Despacha la ruta solicitada, buscando coincidencia en las rutas registradas.
     * Ejecuta la vista o controlador correspondiente, o muestra error 404 si no encuentra la ruta.
     */
    public function despacharRuta(ViewRenderer $renderer): void {
        $rutaSolicitada = $this->obtenerRutaSolicitada();
        $metodoHttp = $this->obtenerMetodoHttp();
        
        $rutaEncontrada = $this->buscarRutaCoincidente($rutaSolicitada, $metodoHttp);
        
        if (!$rutaEncontrada) {
            $this->manejarRutaNoEncontrada($renderer);
            return;
        }

        // Ejecución del Middleware ANTES del controlador/vista
        $middleware = $rutaEncontrada['middlewares'] ?? null;
        if (!$this->ejecutarMiddleware($middleware, $rutaSolicitada)) {
            // El Middleware se encargó de la redirección y el exit, si falla.
            return; 
        }        
        // Ejecución del Destino (solo si el middleware no detuvo la solicitud).
        $this->ejecutarDestino($rutaEncontrada['destino'], $renderer);
    }

    // Obtiene el service correspondiente para un controlador dado.
    private function obtenerServiceParaControladores(string $claseController) {
        $mapeoControllerService = [
            // Para este Controller, éste Service.
            PersonalController::class => PersonalService::class,
            MesaController::class => MesaService::class,
            AuthController::class => AuthService::class,
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


    // ========== MÉTODOS PRIVADOS ==========

    /**
     * Obtiene y limpia la ruta solicitada desde los parámetros GET.
     * Retorna 'home' como ruta por defecto si no se especifica ruta.
     */
    private function obtenerRutaSolicitada(): string {
        $ruta = $_GET['url'] ?? 'home';
        return trim($ruta, '/');
    }
    /**
     * Obtiene el método HTTP de la solicitud actual (GET, POST, etc.).
     * Retorna 'GET' como método por defecto si no se puede determinar.
     */
    private function obtenerMetodoHttp(): string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
    /**
     * Busca en las rutas registradas una que coincida con la URL y método HTTP solicitados.
     * Retorna la definición de la ruta si encuentra coincidencia, o null si no.
     */
    private function buscarRutaCoincidente(string $ruta, string $metodo): ?array {
        foreach ($this->rutas as $nombreRuta => $definicionRuta) {
            if ($nombreRuta === $ruta && $definicionRuta['metodo'] === $metodo) {
                return $definicionRuta;
            }
        }
        return null;
    }
    /**
     * Maneja el caso cuando no se encuentra la ruta solicitada.
     * Renderiza la vista de error 404.
     */
    private function manejarRutaNoEncontrada(ViewRenderer $renderer): void {
        $renderer->renderizarVistaConDatos('9.00-notfound');
    }
    /**
     * Ejecuta el destino de la ruta encontrada.
     * Puede ser una vista (string) o un controlador (array [Clase::class, 'metodo']).
     * Si el destino no es válido, muestra error 404.
     */
    private function ejecutarDestino($destino, ViewRenderer $renderer): void {
        if (is_string($destino)) {
            $this->ejecutarVista($destino, $renderer);
        } elseif (is_array($destino) && count($destino) === 2) {
            $this->ejecutarControlador($destino, $renderer);
        } else {
            $this->manejarRutaNoEncontrada($renderer);
        }
    }
    /**
     * Ejecuta una vista simple, renderizándola directamente.
     */
    private function ejecutarVista(string $vista, ViewRenderer $renderer): void {
        $renderer->renderizarVistaConDatos($vista);
    }
    /**
     * Ejecuta un controlador y su método específico.
     * Instancia el controlador con inyección de dependencias y llama al método.
     */
    private function ejecutarControlador(array $destino, ViewRenderer $renderer): void {
        [$claseController, $metodo] = $destino;
        
        $service = $this->obtenerServiceParaControladores($claseController);
        $controlador = new $claseController($service, $renderer);
        
        $controlador->$metodo();
    }
    /**
     * Ejecuta el middleware asociado a una ruta. Si el middleware redirige, termina la ejecución.
     * Retorna false si el middleware detuvo la ejecución (redirigió), o true si continúa.
     */
    private function ejecutarMiddleware(?array $middlewares, string $rutaSolicitada): bool {
        if (!$middlewares) {
            return true;
        }

        foreach ($middlewares as $middlewareConfig) {            
            // Creamos una copia local para manipular si es un array, o usamos el string directo.
            if (is_array($middlewareConfig)) {
                $tempConfig = $middlewareConfig; // Copia del array.
                $claseMiddleware = array_shift($tempConfig); // Extraemos la clase.
                $argumentos = $tempConfig; // El resto son los argumentos.
            } else {
                $claseMiddleware = $middlewareConfig;
                $argumentos = [];
            }
            
            $instanciaMiddleware = new $claseMiddleware(); 

            if ($claseMiddleware === AuthMiddleware::class) {
                $instanciaMiddleware->requerirAutenticacion($rutaSolicitada);
            } elseif ($claseMiddleware === RoleMiddleware::class) {
                $rolRequerido = $argumentos[0] ?? null;
                if (!$rolRequerido) {
                    throw new \Exception("RoleMiddleware requiere un rol como argumento.");
                }
                $instanciaMiddleware->requiereRol($rolRequerido, $rutaSolicitada);
            }
        }

        return true;  // Todos los middlewares pasaron.
    }
}