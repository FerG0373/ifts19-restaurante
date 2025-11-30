<?php
namespace App\Middleware;

use App\Core\Container;
use App\Services\AuthService;

class RoleMiddleware {

    public function __construct() {
    }

    
    // Verifica que el usuario logueado tenga el rol requerido.
    public function requiereRol(string $rolRequerido, string $rutaActual): void {        
        $perfilAcceso = $_SESSION['perfil_acceso'] ?? null;

        // REGLA DE ACCESO TOTAL: El rol 'encargado' siempre pasa.
        if ($perfilAcceso === 'encargado') {
            return; 
        }
        
        // Verifica que el perfil de la sesión existe y coincide con el rol.
        if (!$perfilAcceso || $perfilAcceso !== $rolRequerido) {            
            $_SESSION['auth_error'] = "Acceso denegado. Su perfil ({$perfilAcceso}) no puede acceder a {$rutaActual}.";
            header("Location: " . APP_BASE_URL . "home"); // Redirigir al home.
            exit;
        }
    }
}