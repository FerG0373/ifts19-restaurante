<?php
namespace App\Core;


class ViewRenderer {
    private string $rutaBaseVistas;
    private array $rutas;
    private array $rutasNav;

    public function __construct(string $rutaBaseVistas, array $rutas) {
        $this->rutaBaseVistas = $rutaBaseVistas;  // Ruta base de las vistas.
        $this->rutas = $rutas;  // Array de rutas desde Router.
        $this->rutasNav = $this->obtenerRutasNav();  // Rutas para la navegación.
    }

    private function obtenerRutaSolicitada(): string {
        $rutaSolicitada = $_GET['url'] ?? 'home';
        return $rutaSolicitada === '' ? 'home' : $rutaSolicitada;
    }

    private function obtenerRutasNav(): array {
        $rutasNav = [];
        foreach ($this->rutas as $url => $definicionRuta) {
            if (isset($definicionRuta['nav']) && $definicionRuta['nav'] === true) {
                $rutasNav[$url] = $definicionRuta['destino'];
            }
        }
        return $rutasNav;
    }

    public function renderizarVistaDesdeUrl(): void {
        $rutaSolicitada = $this->obtenerRutaSolicitada();
        $rutaLayout = $this->rutaBaseVistas . '/0.00-layout.php';
        $rutaNotFound = $this->rutaBaseVistas . '/9.00-notfound.php';

        // Verifica si la vista actual existe en el array de rutas.
        if (!isset($this->rutas[$rutaSolicitada])) {
            require_once $rutaNotFound;
            exit();
        }

        $definicionRuta = $this->rutas[$rutaSolicitada];

        if (!isset($definicionRuta['destino'])) {
            require_once $rutaNotFound;  // Manejo de error si la ruta está mal definida.
            exit();
        }

        $contenidoPrincipal = $definicionRuta['destino'];
        $rutasNav = $this->rutasNav;
        
        require_once $rutaLayout;
    }

    public function renderizarVistaConDatos(string $vista, array $datos = []): void {
        extract($datos);  // Extrae las variables del array para usarlas en la vista.

        $rutaLayout = $this->rutaBaseVistas . '/0.00-layout.php';
        $rutaNotFound = $this->rutaBaseVistas . '/9.00-notfound.php';
        $rutaVista = $this->rutaBaseVistas . '/' . $vista . '.php';
        $rutasNav = $this->rutasNav;

        if (file_exists($rutaVista)) {
            $contenidoPrincipal = $rutaVista;
        } else {
            $contenidoPrincipal = $rutaNotFound;
        }
        
        require_once $rutaLayout;
    }
}