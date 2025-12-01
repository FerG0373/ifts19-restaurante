<?php
namespace App\Core;

use App\Services\AuthService;


class ViewRenderer {
    private string $directorioVistas;
    private array $rutas;
    private array $rutasNav;
    private AuthService $authService;

    public function __construct(string $directorioVistas, array $rutas) {
        $this->directorioVistas = $directorioVistas;  // Ruta base de las vistas.
        $this->rutas = $rutas;  // Array de rutas desde Router.
        $this->rutasNav = $this->obtenerRutasNav();  // Rutas para la navegación.
        $this->cargarHelpers();
        $this->authService = Container::getService(AuthService::class);
    }


    private function obtenerUrlSolicitada(): string {
        $url = $_GET['url'] ?? 'home';
        return rtrim($url, '/'); // Asegurarse de limpiar la URL
    }


    private function obtenerRutasNav(): array {
        $rutasNav = [];
        foreach ($this->rutas as $claveUrl => $definicionRuta) {
            if (isset($definicionRuta['nav']) && $definicionRuta['nav'] === true) {
                $rutasNav[$claveUrl] = $definicionRuta['destino'];
            }
        }
        return $rutasNav;
    }
        
   
    public function renderizarVistaDesdeUrl(): void {
        $url = $this->obtenerUrlSolicitada();
        $layout = $this->directorioVistas . '/0.00-layout.php';
        $notFound = $this->directorioVistas . '/9.00-notfound.php';

        // Intenta encontrar la vista estática o el 404.
        $rutaEstatica = $this->directorioVistas . '/' . $url . '.php'; 

        if (file_exists($rutaEstatica) && is_string($this->rutas[$url]['destino'] ?? null)) {
            // Se encontró una ruta estática (string)
            $contenidoPrincipal = $rutaEstatica;
        } else {
            // Usar la vista 404 por defecto
            $contenidoPrincipal = $notFound;
        }
        
        require_once $layout;
    }    

    
    public function renderizarVistaConDatos(string $vista, array $datos = []): void {
        // INYECTAR VARIABLES GLOBALES DE SESIÓN.
        $datos['estaAutenticado'] = $this->authService->estaAutenticado();
        // Si está autenticado, inyectamos el perfil para la lógica de navegación
        if ($datos['estaAutenticado']) {
            $datos['perfilAcceso'] = $_SESSION['perfil_acceso'] ?? null;
            $datos['usuarioDni'] = $_SESSION['usuario_dni'] ?? null;
        } else {
            $datos['perfilAcceso'] = null;
            $datos['usuarioDni'] = null;
        }

        extract($datos);  // Extrae las variables del array para usarlas en la vista.

        // Las variables $rutasNav y $viewRenderer (this) están disponibles en el layout.
        $rutasNav = $this->rutasNav;
        $viewRenderer = $this;

        $rutaLayout = $this->directorioVistas . '/0.00-layout.php';
        $rutaNotFound = $this->directorioVistas . '/9.00-notfound.php';        
        $rutaVista = $this->directorioVistas . '/' . $vista . '.php';        

        if (file_exists($rutaVista)) {
            $contenidoPrincipal = $rutaVista;
        } else {
            // Si la vista específica no existe, mostramos el 404
            $contenidoPrincipal = $rutaNotFound;
        }
        
        // Incluimos el layout que a su vez incluye $contenidoPrincipal
        require_once $rutaLayout;
    }


    private function cargarHelpers(): void {
        $helperPath = __DIR__ . '/../../Helpers/form_helper.php';
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }
}