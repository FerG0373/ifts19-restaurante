<?php
namespace App\Controllers;

use App\Core\ViewRenderer;
use App\Services\AuthService;

class AuthController {
    
    private AuthService $authService;
    private ViewRenderer $viewRenderer;

    public function __construct(AuthService $authService, ViewRenderer $viewRenderer) {
        $this->viewRenderer = $viewRenderer;
        $this->authService = $authService;
    }

    // GET /login
    public function mostrarFormularioLogin(): void {
        // Verifica si hay un mensaje de error en la sesión (establecido por el POST /login).
        $error = $_SESSION['auth_error'] ?? null;
        unset($_SESSION['auth_error']);  // Limpia el error después de mostrarlo.
        
        $this->viewRenderer->renderizarVistaConDatos('1.00-login', [
            'error' => $error
        ]);
    }

    // POST /login
    public function iniciarSesion(): void {
        $username = $_POST['username'] ?? null;
        $password = $_POST['pass'] ?? null;
        
        // La lógica de verificación real irá en el AuthService.
        try {
            $this->authService->login($username, $password);
            
            // Si el login es exitoso, redirige al tablero principal
            header("Location: " . APP_BASE_URL . "home");
            exit;

        } catch (\Exception $e) {
            // Si falla, guarda el mensaje de error en la sesión y redirige al GET /login
            $_SESSION['auth_error'] = $e->getMessage();
            header("Location: " . APP_BASE_URL . "login");
            exit;
        }
    }
    
    // GET /logout
    public function cerrarSesion(): void {
        $this->authService->logout();
        
        // Redirige a la página de login después de cerrar la sesión
        header("Location: " . APP_BASE_URL . "login");
        exit;
    }
}