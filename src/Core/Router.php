<?php
namespace App\Core;


class Router {
    private string $rutaBaseVistas;
    private array $rutas = [];

    public function __construct(string $rutaBaseVistas) {
        $this->rutaBaseVistas = $rutaBaseVistas;
    }

    public function agregarRuta(string $nombreRuta, string $nombreArchivo): void {
        $this->rutas[$nombreRuta] = $this->rutaBaseVistas . '/' . $nombreArchivo;
    }
    
    public function getRutas(): array {
        return $this->rutas;
    }
}
?>