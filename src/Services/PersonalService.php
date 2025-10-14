<?php
namespace App\Services;

use App\Models\Personal;
use App\Repositories\PersonalRepository;
use App\Shared\Enums\Puesto;
use App\Shared\Enums\Sexo;
use InvalidArgumentException;
use RuntimeException;
use DateTimeImmutable;


class PersonalService {
    private PersonalRepository $personalRepository;

    public function __construct(PersonalRepository $personalRepository) {
        $this->personalRepository = $personalRepository;
    }

    // Método por defecto para la ruta principal (/personal)
    public function listarPersonalActivo(): array {
        return $this->personalRepository->listarPersonal(true); 
    }


    public function listarTodoElPersonal(): array {
        return $this->personalRepository->listarPersonal(false);
    }


    public function mostrarDetalle(int $id): ?Personal {
        return $this->personalRepository->obtenerPersonalPorId($id);
    }


    public function altaPersonal(Personal $personal): Personal {
        // Validar que el objeto Usuario exista.
        $usuario = $personal->getUsuario();
        if (!$usuario) {
            throw new \InvalidArgumentException("El objeto Personal requiere un objeto Usuario asociado para el alta.");
        }
        // Validar Unicidad de DNI.
        if ($this->personalRepository->existeDni($personal->getDni())) {
            throw new \Exception("El DNI ({$personal->getDni()}) ya se encuentra registrado.");
        }
        // Validar Unicidad de Email.
        if ($this->personalRepository->existeEmail($personal->getEmail())) {
            throw new \Exception("El correo electrónico ({$personal->getEmail()}) ya se encuentra registrado.");
        }
        
        // --- 2. Tarea de Pre-Persistencia (Hasheo) ---
        // C. Asegurar que la contraseña esté hasheada. Si no lo está, la hasheamos aquí.
        if (!empty($usuario->getPassHash()) && !str_starts_with($usuario->getPassHash(), '$2y$')) {
            // Asumiendo que getPassHash() retorna la contraseña en texto plano desde el Controller.
            // Si ya estuviera hasheada, esta verificación fallaría.
            $hash = password_hash($usuario->getPassHash(), PASSWORD_DEFAULT);
            
            // 💡 NOTA: En un caso real, necesitarías un método 'setPassHash' en tu modelo Usuario
            // para actualizar el hash antes de pasarlo al Repository.
            // $usuario->setPassHash($hash); 
        }

        return $this->personalRepository->insertarPersonal($personal);
    }


    // PersonalService.php (El Service llama al Repository)  POSIBLE MÉTODO A IMPLEMENTAR
    public function crearPersonal(Personal $personal) {
    
        // 1. Lógica de Negocio: VALIDAR unicidad usando el Repository
        if ($this->personalRepository->existeDni($personal->getDni())) {
            // En un caso real, podrías lanzar una excepción o devolver un error específico
            throw new \Exception("Ya existe un empleado con el DNI proporcionado.");
        }

        if ($this->personalRepository->existeEmail($personal->getEmail())) {
            throw new \Exception("El email ya está registrado por otro empleado.");
        }
        
        // 2. Si las validaciones pasan, llamar al Repository para GUARDAR
        return $this->personalRepository->insertarPersonal($personal);
    }
}
?>