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

    /**
     * Intenta iniciar sesión verificando credenciales.
     * * @param string|null $username El DNI del usuario (username).
     * @param string|null $password La contraseña en texto plano.
     * @throws \Exception Si las credenciales son inválidas o el usuario está inactivo.
     */
    public function login(?string $username, ?string $password): void {
        if (empty($username) || empty($password)) {
            throw new RuntimeException("El DNI y la contraseña son obligatorios.");
        }

        // 1. Validar que el DNI sea numérico.
        if (!ctype_digit($username)) {
            throw new RuntimeException("El usuario (DNI) debe contener solo números.");
        }
        
        $dni = (int)$username;

        // 2. Buscar usuario por DNI
        $usuario = $this->usuarioRepository->buscarUsuarioPorId($dni);

        if (!$usuario) {
            // NO ESPECIFICAMOS QUÉ FALLÓ (seguridad), solo decimos que son inválidas.
            throw new RuntimeException("Credenciales inválidas.");
        }

        // 3. Verificar estado activo
        if (!$usuario->isActivo()) {
            throw new RuntimeException("Su cuenta ha sido desactivada. Contacte a un administrador.");
        }
        
        // 4. Verificar contraseña (asumiendo que se usa password_hash() para el almacenamiento)
        if (!password_verify($password, $usuario->getPassHash())) {
            // Volvemos al mensaje genérico de seguridad
            throw new RuntimeException("Credenciales inválidas.");
        }

        // 5. Autenticación exitosa: Crear sesión
        $this->crearSesion($usuario);
    }
    
    /**
     * Crea las variables de sesión necesarias para mantener al usuario autenticado.
     */
    private function crearSesion(Usuario $usuario): void {
        // Asegúrate de que session_start() se haya llamado al inicio de la aplicación
        $_SESSION['usuario_id'] = $usuario->getId();
        $_SESSION['perfil_acceso'] = $usuario->getPerfilAcceso()->value;
        // Opcional: registrar la última actividad, etc.
    }

    /**
     * Cierra la sesión activa.
     */
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