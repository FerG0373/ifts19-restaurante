<?php
namespace App\Core;


class ViewRenderer {
    private string $directorioVistas;
    private array $rutas;
    private array $rutasNav;

    public function __construct(string $directorioVistas, array $rutas) {
        $this->directorioVistas = $directorioVistas;  // Ruta base de las vistas.
        $this->rutas = $rutas;  // Array de rutas desde Router.
        $this->rutasNav = $this->obtenerRutasNav();  // Rutas para la navegación.
    }

    private function obtenerUrlSolicitada(): string {
        $url = $_GET['url'] ?? 'home';
        return $url === '' ? 'home' : $url;
    }

    private function obtenerRutasNav(): array {
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

        // Verifica si la vista actual existe en el array de rutas.
        if (!isset($this->rutas[$url])) {
            require_once $notFound;
            exit();
        }

        $definicionRuta = $this->rutas[$url];

        if (!isset($definicionRuta['destino'])) {
            require_once $notFound;  // Manejo de error si la ruta está mal definida.
            exit();
        }

        $contenidoPrincipal = $definicionRuta['destino'];
        
        require_once $layout;
    }

    public function renderizarVistaConDatos(string $vista, array $datos = []): void {
        extract($datos);  // Extrae las variables del array para usarlas en la vista.

        $rutaLayout = $this->directorioVistas . '/0.00-layout.php';
        $rutaNotFound = $this->directorioVistas . '/9.00-notfound.php';
        $rutaVista = $this->directorioVistas . '/' . $vista . '.php';        

        if (file_exists($rutaVista)) {
            $contenidoPrincipal = $rutaVista;
        } else {
            $contenidoPrincipal = $rutaNotFound;
        }
        
        require_once $rutaLayout;
    }
}