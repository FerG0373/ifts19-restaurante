<?php
namespace App\Core;

use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\GuestMiddleware;
use ReflectionClass;
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
    
    
    public function getRutas(): array {
        return $this->rutas;
    }
    
    // ========== MÉTODOS PRIVADOS ==========

    /**
     * Obtiene y limpia la ruta solicitada desde los parámetros GET.
     * Retorna 'login' como ruta por defecto si no se especifica ruta.
     */
    private function obtenerRutaSolicitada(): string {
        $ruta = $_GET['url'] ?? 'login';
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
     * Implementa Inyección de Dependencias (DI) dinámica usando Reflection para leer los argumentos del constructor del controlador.
     */
    private function ejecutarControlador(array $destino, ViewRenderer $renderer): void {
        [$claseController, $metodo] = $destino;
        $argumentosConstructor = [];
        
        try {
            // 1. Usar Reflection para obtener el constructor del controlador.
            $reflectionClass = new ReflectionClass($claseController);
            $constructor = $reflectionClass->getConstructor();

            if ($constructor) {
                // 2. Iterar sobre los parámetros del constructor.
                foreach ($constructor->getParameters() as $parametro) {
                    $tipo = $parametro->getType();

                    if ($tipo && !$tipo->isBuiltin()) {
                        $nombreClase = $tipo->getName();
                        
                        // 3. Resuelve la dependencia según el tipo de clase.
                        if ($nombreClase === ViewRenderer::class) {
                            // Inyecta el ViewRenderer que ya está disponible.
                            $argumentosConstructor[] = $renderer;
                        } else {
                            // Esto maneja 1 o N Services de forma automática.
                            $argumentosConstructor[] = Container::getService($nombreClase);
                        }
                    } else {
                        // Si el parámetro no tiene un tipo de clase (ej. string, int), no podemos resolverlo automáticamente.
                        throw new Exception("El parámetro '{$parametro->getName()}' en el constructor de $claseController no es una dependencia de clase resoluble (debe ser ViewRenderer o un Service).");
                    }
                }
            }

            // 4. Instancia el controlador con los argumentos resueltos.
            $controlador = $reflectionClass->newInstanceArgs($argumentosConstructor);
            
            // 5. Ejecuta el método.
            $controlador->$metodo();

        } catch (\ReflectionException $e) {
            $this->manejarRutaNoEncontrada($renderer);
        } catch (Exception $e) {
            // Esto captura errores si Container::getService falla o la lógica de DI falla
            $renderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Inyección de Dependencias',
                'mensaje' => "No se pudo instanciar el controlador {$claseController}. Detalles: " . $e->getMessage()
            ]);
        }
    }
    /**
     * Ejecuta los middlewares asociados a la ruta.
     * Retorna true si todos los middlewares pasan, false si alguno falla.
     */
    private function ejecutarMiddleware(?array $middlewares, string $rutaSolicitada): bool {
        if (!$middlewares) return true;  // Si $middlewares es null o array vacío, no hay middlewares que ejecutar, pasa directo.

        foreach ($middlewares as $middlewareConfig) {            
            if (is_array($middlewareConfig)) {
                $tempConfig = $middlewareConfig;  // Crea copia para no modificar el original.
                $claseMiddleware = array_shift($tempConfig);  //Extrae y remueve el primer elemento (la clase).
                $argumentos = $tempConfig;  // Lo que queda son los argumentos para el middleware
            } else {
                $claseMiddleware = $middlewareConfig;  // Si es string, lo toma directamente como clase.
                $argumentos = [];  // Argumentos vacíos porque no se pasaron parámetros.
            }
            
            $instanciaMiddleware = new $claseMiddleware();
            
            // Ejecuta el método procesar del middleware. Si retorna false, el middleware falló la verificación.
            if (!$instanciaMiddleware->procesar($rutaSolicitada, $argumentos)) {
                // Ejecuta la lógica de fallo (redirecciones, mensajes de error)
                $this->manejarFalloMiddleware($claseMiddleware, $rutaSolicitada);
                // Retorna false inmediatamente, corta la ejecución de más middlewares.
                return false;
            }
        }
        // Si llegó acá: Todos los middlewares pasaron sus verificaciones.
        return true;
    }
    /**
     * Maneja la redirección o acción cuando un middleware falla.
     */
    private function manejarFalloMiddleware(string $claseMiddleware, string $rutaSolicitada): void {
        if ($claseMiddleware === AuthMiddleware::class) {
            // Usuario NO logueado intentando acceder a ruta protegida.
            $_SESSION['auth_error'] = "Debe iniciar sesión para acceder a {$rutaSolicitada}.";
            $_SESSION['redirect_to'] = $rutaSolicitada;
            header("Location: " . APP_BASE_URL . "login");
        }
        // El resto de fallos (RoleMiddleware, GuestMiddleware) asumen que el usuario está logueado y lo redirigen a home.
        elseif ($claseMiddleware === RoleMiddleware::class) {
            // RoleMiddleware debe haber seteado ya un mensaje de error en la sesión (o lo dejamos genérico si no lo hizo).
            $_SESSION['auth_error'] = $_SESSION['auth_error'] ?? "Acceso denegado.";
            header("Location: " . APP_BASE_URL . "home");
        } 
        
        elseif ($claseMiddleware === GuestMiddleware::class) {
            // Usuario logueado intentando acceder a login. Redirigir a home sin mensaje de error.
            header("Location: " . APP_BASE_URL . "home");
        }
        // Ejecuta el exit para cortar la ejecución del script después de la redirección.
        exit;
    }
}