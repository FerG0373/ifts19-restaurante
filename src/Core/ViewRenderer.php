<?php
namespace App\Core;


class ViewRenderer {
    private string $rutaVistas;
    private array $rutas;

    public function __construct(string $rutaVistas, array $rutas) {
        $this->rutaVistas = $rutaVistas;
        $this->rutas = $rutas;
    }

    private function obtenerPaginaSolicitada(): string {
        $paginaSolicitada = $_GET['url'] ?? 'home';
        return $paginaSolicitada === '' ? 'home' : $paginaSolicitada;
    }

    public function renderizar(): void {
        $paginaSolicitada = $this->obtenerPaginaSolicitada();
        $rutaNotFound = $this->rutaVistas . '/9.00-notfound.php';
        $rutaLayout = $this->rutaVistas . '/0.00-layout.php';

        // Verifica si la vista actual existe en el array de rutas.
        if (!isset($this->rutas[$paginaSolicitada])) {
            require_once $rutaNotFound;
            exit();
        }

        $contenidoPrincipal = $this->rutas[$paginaSolicitada];
        $arrayRutas = $this->rutas; // Para usar en el _header
        
        require_once $rutaLayout;
    }
}