<?php
namespace App\Core;


class Router {
    private string $rutaBaseVistas;
    private array $rutas = [];

    public function __construct(string $rutaBaseVistas) {
        $this->rutaBaseVistas = $rutaBaseVistas;
    }

    public function agregarRuta(string $nombreRuta, string $nombreArchivo, bool $enNavHeader): void {
        $this->rutas[$nombreRuta] = [
            'archivo' => $this->rutaBaseVistas . '/' . $nombreArchivo,
            'nav' => $enNavHeader
        ];
    }
    
    public function getRutas(): array {
        return $this->rutas;
    }

    public function getRutasNav(): array {
        $rutasNav = [];
        foreach ($this->rutas as $url => $infoRuta) {
            if ($infoRuta['nav']) {
                $rutasNav[$url] = $infoRuta['archivo'];
            }
        }
        return $rutasNav;
    }
}
?>