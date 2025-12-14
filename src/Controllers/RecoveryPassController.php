<?php
namespace App\Controllers;

use App\Core\ViewRenderer;
use App\Services\PersonalService;
use App\Models\Usuario;
use InvalidArgumentException;
use RuntimeException;


class RecoveryPassController {
    private ViewRenderer $viewRenderer;
    private PersonalService $personalService;
    
    public function __construct(ViewRenderer $viewRenderer, PersonalService $personalService) {
        $this->viewRenderer = $viewRenderer;
        $this->personalService = $personalService;
    }

    // ========== MÉTODOS PÚBLICOS ==========
    
    // GET /recuperar-password
    public function mostrarFormulario(): void {
        // Renderiza la vista del formulario simple
        $this->viewRenderer->renderizarVistaConDatos('1.01-recuperar', [
            'titulo' => 'Recuperar Contraseña',
            'error' => $_SESSION['error_recuperar'] ?? null,
            'exito' => $_SESSION['exito_recuperar'] ?? null,
            // Mantiene el valor del DNI/Usuario si hubo un error en el POST.
            'username_data' => $_SESSION['username_recuperar'] ?? '' 
        ]);
        
        // Limpia los mensajes de sesión después de mostrarlos.
        unset($_SESSION['error_recuperar'], $_SESSION['exito_recuperar'], $_SESSION['username_recuperar']);
    }

    // POST /recuperar-password/procesar
    public function procesarRestablecimiento(): void {
        try {
            $this->procesarSolicitudRestablecimiento();
        } catch (InvalidArgumentException | RuntimeException $e) {
            $_SESSION['error_recuperar'] = $e->getMessage();
            $this->redirigirConError();
        } catch (\Exception $e) {
            $_SESSION['error_recuperar'] = "Error del sistema al intentar restablecer: " . $e->getMessage();
            $this->redirigirConError();
        }
    }


    // ========== MÉTODOS PRIVADOS ==========

    private function procesarSolicitudRestablecimiento(): void {
        $username = $this->obtenerInputPost('username');
        $nuevaPass = $this->obtenerInputPost('nueva_pass');
        $confirmarPass = $this->obtenerInputPost('confirmar_pass');
        
        $_SESSION['username_recuperar'] = $username;
        
        $this->validarCampos($username, $nuevaPass, $confirmarPass);
        
        $usuario = $this->obtenerUsuarioValido($username);
        $this->actualizarPasswordUsuario($usuario->getId(), $nuevaPass);
        
        $this->finalizarProcesoExitoso($username);
    }

    private function obtenerInputPost(string $key): string {
        return trim($_POST[$key] ?? '');
    }

    private function validarCampos(string $username, string $nuevaPass, string $confirmarPass): void {
        if (empty($username)) {
            throw new InvalidArgumentException("Debe ingresar su Usuario (DNI).");
        }
        
        if (empty($nuevaPass) || empty($confirmarPass)) {
            throw new InvalidArgumentException("Debe ingresar y confirmar la nueva contraseña.");
        }
        
        if ($nuevaPass !== $confirmarPass) {
            throw new InvalidArgumentException("La nueva contraseña y la confirmación no coinciden.");
        }
    }

    private function obtenerUsuarioValido(string $username): Usuario {
        $personal = $this->personalService->buscarPersonalPorDni($username);
        
        if (!$personal || !$personal->getUsuario()->isActivo()) {
            throw new RuntimeException("No se encontró el usuario o no está habilitado para restablecer la contraseña.");
        }
        
        return $personal->getUsuario();
    }

    private function actualizarPasswordUsuario(int $idUsuario, string $nuevaPass): void {
        $this->personalService->actualizarPassword($idUsuario, $nuevaPass);
    }

    private function finalizarProcesoExitoso(string $username): void {
        $_SESSION['exito_recuperar'] = "Contraseña restablecida con éxito para el usuario '{$username}'. Ya puedes iniciar sesión.";
        
        unset($_SESSION['username_recuperar']);
        
        $this->redirigir();
    }

    private function redirigir(): void {
        header("Location: " . APP_BASE_URL . "recuperar-password");
        exit;
    }

    private function redirigirConError(): void {
        $this->redirigir();
    }
}