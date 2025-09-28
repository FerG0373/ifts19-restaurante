<?php
namespace App\Core;


class ViewRenderer {
    private string $rutaBaseVistas;
    private array $rutas;

    public function __construct(string $rutaBaseVistas, array $rutas) {
        $this->rutaBaseVistas = $rutaBaseVistas;  // Ruta base de las vistas.
        $this->rutas = $rutas;  // Array de rutas desde Router.
    }

    private function obtenerRutaSolicitada(): string {
        $rutaSolicitada = $_GET['url'] ?? 'home';
        return $rutaSolicitada === '' ? 'home' : $rutaSolicitada;
    }

    private function obtenerRutasNav(): array {
        $rutasNav = [];
        foreach ($this->rutas as $url => $definicionRuta) {
            if (isset($definicionRuta['nav']) && $definicionRuta['nav'] === true) {
                $rutasNav[$url] = $definicionRuta['archivo'];
            }
        }
        return $rutasNav;
    }

    public function renderizar(): void {
        $rutaSolicitada = $this->obtenerRutaSolicitada();
        $rutaNotFound = $this->rutaBaseVistas . '/9.00-notfound.php';
        $rutaLayout = $this->rutaBaseVistas . '/0.00-layout.php';

        // Verifica si la vista actual existe en el array de rutas.
        if (!isset($this->rutas[$rutaSolicitada])) {
            require_once $rutaNotFound;
            exit();
        }

        $definicionRuta = $this->rutas[$rutaSolicitada];

        if (!isset($definicionRuta['archivo'])) {
            require_once $rutaNotFound;  // Manejo de error si la ruta está mal definida
            exit();
        }

        $contenidoPrincipal = $definicionRuta['archivo'];
        $arrayRutasNav = $this->obtenerRutasNav();        
        
        require_once $rutaLayout;
    }
}