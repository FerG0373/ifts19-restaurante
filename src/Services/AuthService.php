<?php
namespace App\Services;

use App\Repositories\UsuarioRepository;
use App\Models\Usuario;
use RuntimeException;


class AuthService {
    
    private UsuarioRepository $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository) {
        $this->usuarioRepository = $usuarioRepository;
    }

    
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
    
    
    private function crearSesion(Usuario $usuario): void {
        // Asegúrate de que session_start() se haya llamado al inicio de la aplicación
        $_SESSION['usuario_id'] = $usuario->getId();
        $_SESSION['perfil_acceso'] = $usuario->getPerfilAcceso()->value;
        // Opcional: registrar la última actividad, etc.
    }

    
    public function logout(): void {
        // Destruye todas las variables de sesión
        $_SESSION = [];
        
        // Si se usa cookies de sesión, también debe ser destruida
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finalmente, destruye la sesión
        session_destroy();
    }
}