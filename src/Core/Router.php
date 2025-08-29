<?php
namespace App\Core;

class Router {
    private string $rutaBase;
    private array $rutas = [];
    
    public function __construct(string $rutaBase) {
        $this->rutaBase = $rutaBase;
    }

    public function agregarRuta(string $nombreRuta, string $nombreArchivo): void {
        $this->rutas[$nombreRuta] = $this->rutaBase . '/' . $nombreArchivo;
    }

    public function obtenerPaginaSolicitada(): string {
        $paginaSolicitada = $_GET['url'] ?? 'home';
        return $paginaSolicitada === '' ? 'home' : $paginaSolicitada;
    }

    public function renderizar(): void {
        $paginaSolicitada = $this->obtenerPaginaSolicitada();
        $rutaNotFound = $this->rutaBase . '/9.00-notfound.php';
        $rutaLayout = $this->rutaBase . '/0.00-layout.php';

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
?>