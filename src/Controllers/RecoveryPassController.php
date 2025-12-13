<?php
namespace App\Controllers;

use App\Core\ViewRenderer;
use App\Services\PersonalService; // Usaremos los métodos nuevos: buscarPersonalPorDni y actualizarPassword
use InvalidArgumentException;
use RuntimeException;


class RecoveryPassController {
    private ViewRenderer $viewRenderer;
    private PersonalService $personalService;
    
    public function __construct(ViewRenderer $viewRenderer, PersonalService $personalService) {
        $this->viewRenderer = $viewRenderer;
        $this->personalService = $personalService;
    }

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
        $username = $_POST['username'] ?? '';
        $nuevaPass = $_POST['nueva_pass'] ?? '';
        $confirmarPass = $_POST['confirmar_pass'] ?? '';
        
        // Guarda el username en sesión por si hay un error de validación y hay que redirigir.
        $_SESSION['username_recuperar'] = $username; 

        try {
            // Validaciones de entrada.
            if (empty($username)) {
                throw new InvalidArgumentException("Debe ingresar su Usuario (DNI).");
            }
            if (empty($nuevaPass) || empty($confirmarPass)) {
                throw new InvalidArgumentException("Debe ingresar y confirmar la nueva contraseña.");
            }
            if ($nuevaPass !== $confirmarPass) {
                throw new InvalidArgumentException("La nueva contraseña y la confirmación no coinciden.");
            }
            
            // BUSCAR EL USUARIO POR DNI.
            $personal = $this->personalService->buscarPersonalPorDni($username);
            
            if (!$personal || !$personal->getUsuario()->isActivo()) {
                // Usamos un mensaje genérico por si no existe o no está activo, por seguridad.
                throw new RuntimeException("No se encontró el usuario o no está habilitado para restablecer la contraseña.");
            }
            
            $idUsuario = $personal->getUsuario()->getId();

            // ACTUALIZAR LA CONTRASEÑA (el Service se encarga del hashing y el Repository de la persistencia).
            $this->personalService->actualizarPassword($idUsuario, $nuevaPass);

            $_SESSION['exito_recuperar'] = "Contraseña restablecida con éxito para el usuario '{$username}'. Ya puedes iniciar sesión.";
            
            // Limpia el dato del username después del éxito.
            unset($_SESSION['username_recuperar']);
            
            // Redirige al formulario para mostrar el mensaje de éxito.
            header("Location: " . APP_BASE_URL . "recuperar-password");
            exit;
            
        } catch (InvalidArgumentException $e) {
            // Error de validación de campos
            $_SESSION['error_recuperar'] = $e->getMessage();
            header("Location: " . APP_BASE_URL . "recuperar-password");
            exit;

        } catch (RuntimeException $e) {
            // Error de negocio (usuario no encontrado/inactivo)
            $_SESSION['error_recuperar'] = $e->getMessage();
            header("Location: " . APP_BASE_URL . "recuperar-password");
            exit;
            
        } catch (\Exception $e) {
            // Error de sistema/base de datos
            $_SESSION['error_recuperar'] = "Error del sistema al intentar restablecer: " . $e->getMessage();
            header("Location: " . APP_BASE_URL . "recuperar-password");
            exit;
        }
    }
}