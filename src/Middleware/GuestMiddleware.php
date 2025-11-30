<?php
namespace App\Middleware;

use App\Core\Container;
use App\Services\AuthService;

class GuestMiddleware {
    private AuthService $authService;

    public function __construct() {
        $this->authService = Container::getService(AuthService::class);
    }
    
    public function procesar(string $ruta, array $argumentos = []): bool {
        // Si el usuario ESTÁ autenticado, redirigir (es invitado (guest) = no debería ver login).
        if ($this->authService->estaAutenticado()) {
            return false; // Middleware falla si está autenticado.
        }

        return true; // Permite acceso si NO está autenticado.
    }
}