<?php
namespace App\Middleware;


class RoleMiddleware {
    
    // El método procesar recibe la ruta y un array de argumentos (el primer argumento es el rol requerido).
    public function procesar(string $ruta, array $argumentos = []): bool {
        
        $rolRequerido = $argumentos[0] ?? null;
        if (!$rolRequerido) {
            throw new \Exception("RoleMiddleware requiere un rol como argumento en la ruta: {$ruta}.");
        }
        
        // Verificar que el usuario está autenticado (AuthMiddleware ya pasó)
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['auth_error'] = "Debe estar autenticado para verificar roles.";
            return false;
        }

        $perfilAcceso = $_SESSION['perfil_acceso'] ?? null;

        // Si no hay perfil de acceso (esto indica que AuthMiddleware falló o fue omitido, 
        // pero por seguridad, no debemos lanzar error fatal, sino fallar el chequeo de rol).
        if (!$perfilAcceso) {
            // Establecemos un mensaje de error genérico.
            $_SESSION['auth_error'] = "Error de autenticación: No se pudo verificar el perfil de acceso.";
            return false;
        }

        // REGLA DE ACCESO TOTAL: El rol 'encargado' siempre pasa.
        if ($perfilAcceso === 'encargado') {
            return true; 
        }
        
        // Verifica coincidencia de rol
        if ($perfilAcceso === $rolRequerido) {
            return true;
        }

        // Fallo de autorización: El perfil existe, pero no coincide ni es 'encargado'.
        $_SESSION['auth_error'] = "Acceso denegado. Su perfil ({$perfilAcceso}) no puede acceder a esta funcionalidad (Rol requerido: {$rolRequerido}).";

        return false;
    }
}