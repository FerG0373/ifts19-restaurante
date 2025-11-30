<?php
namespace App\Middleware;

use App\Core\Container;
use App\Services\AuthService;

// ¿Está autenticado?" → protege rutas privadas.
class AuthMiddleware {
    private AuthService $authService;

    public function __construct() {
        $this->authService = Container::getService(AuthService::class);  // Depende de AuthService para verficar la autenticación.
    }
    
    // El método procesar recibe la ruta y un array de argumentos (no usados aquí).
    public function procesar(string $ruta, array $argumentos = []): bool {
        // SOLO verifica - retorna true/false.
        return $this->authService->estaAutenticado();
    }
}