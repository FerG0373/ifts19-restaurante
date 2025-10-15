<?php
namespace App\Services;

use App\Models\Personal;
use App\Repositories\PersonalRepository;
use InvalidArgumentException;
use RuntimeException;


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


    public function agregarPersonal(Personal $personal): Personal {
        // Validar Unicidad de DNI.
        if ($this->personalRepository->existeDni($personal->getDni())) {
            throw new \RuntimeException("El DNI ({$personal->getDni()}) ya se encuentra registrado.");
        }
        // Validar Unicidad de Email.
        if ($this->personalRepository->existeEmail($personal->getEmail())) {
            throw new \RuntimeException("El correo electrónico ({$personal->getEmail()}) ya se encuentra registrado.");
        }
        // Validar que el objeto Usuario exista.
        $usuario = $personal->getUsuario();
        if (!$usuario) {
            throw new \InvalidArgumentException("El objeto Personal requiere un objeto Usuario asociado para el alta.");
        }

        $passTextoPlano = $usuario->getPassHash();

        // Validar que se haya proporcionado una contraseña.
        if (empty($passTextoPlano)) {
            throw new \InvalidArgumentException("Debe proporcionar una contraseña para el nuevo usuario.");
        }        
        
        $passHash = password_hash($passTextoPlano, PASSWORD_DEFAULT);  // Hasheamos la contraseña.
                
        $usuario->setPassHash($passHash);  // Actualizamos el objeto Usuario con el HASH.
             
        return $this->personalRepository->insertarPersonal($personal);  // Persistencia: el Repository ahora insertará el HASH en vez de texto plano.
    }
}
?>