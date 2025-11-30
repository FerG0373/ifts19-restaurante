<?php
namespace App\Middleware;

use App\Core\Container;
use App\Services\AuthService;


class AuthMiddleware {
    private AuthService $authService;

    public function __construct() {
        // El Middleware necesita el AuthService para verificar el estado.
        $this->authService = Container::getService(AuthService::class);
    }
    
    // Verifica la autenticación. Si el usuario no está logueado, redirige y termina la ejecución.
    public function requerirAutenticacion(string $rutaActual): bool {        
        // Verifica si el usuario está autenticado.
        if (!$this->authService->estaAutenticado()) {
            
            // Redirige a login y termina la ejecución.
            header("Location: " . APP_BASE_URL . "login");
            exit;
        }        
        // Si está autenticado, permite el paso.
        return true;
    }
}