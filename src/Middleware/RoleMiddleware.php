<?php
namespace App\Middleware;


class RoleMiddleware {
    // El método procesar recibe la ruta y un array de argumentos (el primer argumento es el rol requerido).
    public function procesar(string $ruta, array $argumentos = []): bool {
        $rolRequerido = $argumentos[0] ?? null;
        if (!$rolRequerido) {
            throw new \Exception("RoleMiddleware requiere un rol como argumento.");
        }
        
        $perfilAcceso = $_SESSION['perfil_acceso'] ?? null;

        // REGLA DE ACCESO TOTAL: El rol 'encargado' siempre pasa.
        if ($perfilAcceso === 'encargado') {
            return true; 
        }
        
        // Verifica coincidencia de rol
        return $perfilAcceso && $perfilAcceso === $rolRequerido;
    }
}