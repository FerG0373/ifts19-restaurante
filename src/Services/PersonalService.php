<?php
namespace App\Services;

use App\Models\Personal;
use App\Models\Usuario;
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


    public function actualizarPersonal(Personal $personalActualizado): void {    
        // Obteniene los datos actuales (antiguos) de la DB. Usamos obtenerPersonalPorId para cargar el objeto completo, incluyendo el Usuario.
        $personalAntiguo = $this->personalRepository->obtenerPersonalPorId($personalActualizado->getId());

        if (!$personalAntiguo) {
            throw new RuntimeException("No se pudo encontrar al personal con ID {$personalActualizado->getId()} para actualizar.");
        }
        
        $usuarioAntiguo = $personalAntiguo->getUsuario();
        $usuarioActualizado = $personalActualizado->getUsuario();

        // Verifica que el Email no esté duplicado (si se cambió).
        if ($personalActualizado->getEmail() !== $personalAntiguo->getEmail()) {
            if ($this->personalRepository->existeEmail($personalActualizado->getEmail())) {
                throw new RuntimeException("El Email '{$personalActualizado->getEmail()}' ya está registrado en el sistema.");
            }
        }
        // FUSIONAR DATOS DE USUARIO: Usamos los valores antiguos para Contraseña e ID del Usuario.
        $usuarioFusionado = new Usuario(
            $usuarioAntiguo->getId(),
            $usuarioActualizado->getPerfilAcceso(),
            $usuarioAntiguo->getPassHash(),
            $usuarioActualizado->isActivo()
        );

        // FUSIONAR DATOS DE PERSONAL: Usamos la Fecha de Contratación antigua.
        $personalFusionado = new Personal(
            $personalActualizado->getId(),
            $personalActualizado->getDni(),
            $personalActualizado->getNombre(),
            $personalActualizado->getApellido(),
            $personalActualizado->getFechaNacimiento(),
            $personalActualizado->getEmail(),
            $personalActualizado->getTelefono(),
            $personalActualizado->getSexo(),
            $personalActualizado->getPuesto(),
            $personalAntiguo->getFechaContratacion(),
            $usuarioFusionado
        );

        // Persistencia: Enviar el objeto fusionado al Repository para la actualización.
        $this->personalRepository->updatePersonal($personalFusionado);
    }
}
?>