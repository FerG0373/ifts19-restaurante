<?php
namespace App\Controllers;

use App\Core\ViewRenderer;
use App\Services\AuthService; // Necesario para la lógica de autenticación


class AuthController {
    
    private ViewRenderer $viewRenderer;
    private AuthService $authService; // Para inyectar el servicio de autenticación

    public function __construct(ViewRenderer $viewRenderer, AuthService $authService) {
        $this->viewRenderer = $viewRenderer;
        $this->authService = $authService;
    }

    // GET /login
    // Muestra el formulario de inicio de sesión.
    public function mostrarFormulario(): void {
        // En este punto, puedes recuperar errores de la sesión si hubo un intento fallido
        $error = $_SESSION['auth_error'] ?? null;
        unset($_SESSION['auth_error']); // Limpia el error después de mostrarlo
        
        $this->viewRenderer->renderizarVistaConDatos('9.00-login', [
            'error' => $error,
            'titulo' => 'Iniciar Sesión',
        ]);
    }

    // POST /login
    // Procesa el formulario de inicio de sesión.
    public function iniciarSesion(): void {
        $username = $_POST['username'] ?? null;
        $password = $_POST['pass'] ?? null;
        
        // La lógica de verificación real irá en el AuthService.
        try {
            $this->authService->login($username, $password);
            
            // Si el login es exitoso, redirige al tablero principal
            header("Location: " . APP_BASE_URL . "mesas");
            exit;

        } catch (\Exception $e) {
            // Si falla, guarda el mensaje de error en la sesión y redirige al GET /login
            $_SESSION['auth_error'] = "Error de inicio de sesión: " . $e->getMessage();
            header("Location: " . APP_BASE_URL . "login");
            exit;
        }
    }
    
    // GET /logout
    // Cierra la sesión activa.
    public function cerrarSesion(): void {
        $this->authService->logout();
        
        // Redirige a la página de login después de cerrar la sesión
        header("Location: " . APP_BASE_URL . "login");
        exit;
    }
}