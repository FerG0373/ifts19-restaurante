<?php
namespace App\Services;

use App\Repositories\UsuarioRepository;
use App\Repositories\PersonalRepository;
use App\Models\Usuario;
use RuntimeException;


class AuthService {
    
    private UsuarioRepository $usuarioRepository;
    private PersonalRepository $personalRepository;

    public function __construct(UsuarioRepository $usuarioRepository, PersonalRepository $personalRepository) {
        $this->usuarioRepository = $usuarioRepository;
        $this->personalRepository = $personalRepository;
    }

    // ========== MÉTODOS PÚBLICOS ==========
    public function login(?string $username, ?string $password): void {
        if (empty($username) || empty($password)) {
            throw new RuntimeException("El DNI y la contraseña son obligatorios.");
        }

        // Valida que el DNI sea numérico.
        if (!ctype_digit($username)) {
            throw new RuntimeException("El usuario (DNI) debe contener solo números.");
        }
        
        $dni = $username;

        // Busca usuario por DNI.
        $usuario = $this->usuarioRepository->buscarUsuarioPorDni($dni);

        if (!$usuario) {
            throw new RuntimeException("Credenciales inválidas.");
        }

        // Verifica contraseña.
        if (!password_verify($password, $usuario->getPassHash())) {
            throw new RuntimeException("Credenciales inválidas.");
        }

        // Verifica estado activo.
        if (!$usuario->isActivo()) {
            throw new RuntimeException("Su cuenta ha sido desactivada. Contacte a un administrador.");
        }        

        // Autenticación exitosa: Crear sesión.
        $this->crearSesion($usuario);
    }

    // Verifica si hay una sesión de usuario activa.
    public function estaAutenticado(): bool {
        return isset($_SESSION['usuario_id']);
    }    
        
    public function logout(): void {
        // Destruye todas las variables de sesión.
        $_SESSION = [];
        
        // Si se usa cookies de sesión, también hay que destruirlas.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finalmente, destruye la sesión.
        session_destroy();
    }

    // ========== MÉTODOS PRIVADOS ==========
    private function crearSesion(Usuario $usuario): void {
        // Inicia la sesión si no está iniciada.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $personal = $this->personalRepository->obtenerPersonalPorId($usuario->getId());
        // Guarda datos del usuario en la sesión.
        $_SESSION['usuario_id'] = $usuario->getId();
        $_SESSION['perfil_acceso'] = $usuario->getPerfilAcceso()->value;
        if ($personal) {
            $_SESSION['usuario_dni'] = $personal->getDni(); 
        }
    }
}