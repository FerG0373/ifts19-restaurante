<?php
namespace App\Repositories;

use App\Core\DataAccess;
use App\Models\Personal;
use App\Models\Usuario;
use App\Shared\Enums\Puesto; 
use App\Shared\Enums\Sexo;
use App\Shared\Enums\PerfilAcceso;
use PDO;
use PDOException;
use DateTimeImmutable;


class PersonalRepository {
    private PDO $db;

    public function __construct(DataAccess $dataAccess) {
        $this->db = $dataAccess->getConexion();
    }


    public function listarPersonal(bool $activo): array {
        if ($activo) {
            $sql = "CALL sp_personal_select_activo()";
        } else {
            $sql = "CALL sp_personal_select_all()";
        }

        $listaDePersonal = [];
            
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            // Recorre todos los resultados
            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Crear el objeto Usuario asociado.
                $usuario = new Usuario(
                    (int)$fila['idUsuario'],      
                    $fila['pass_hash'],            
                    PerfilAcceso::from($fila['perfil_acceso']), 
                    (bool)$fila['activo']
                );
                
                // MAPEO DE FILA DE DATOS AL OBJETO. ASIGNA LOS VALORES DEL REGISTRO A LOS PARÁMETROS DEL CONSTRUCTOR (Recuperación de datos)
                $listaDePersonal[] = new Personal(
                    (int)$fila['id'],
                    $fila['dni'],
                    $fila['nombre'],
                    $fila['apellido'],
                    new DateTimeImmutable($fila['fecha_nacimiento']),
                    $fila['email'],
                    $fila['telefono'],
                    Sexo::from($fila['sexo']),
                    Puesto::from($fila['puesto']),
                    new DateTimeImmutable($fila['fecha_contratacion']),
                    $usuario
                );
            }

            $stmt->closeCursor();

            return $listaDePersonal;

        } catch (PDOException $e) {
            throw new \Exception("Error al listar personal: " . $e->getMessage());
        }
    }


    public function buscarPersonalPorId(int $id): ?Personal {
        $sql = "CALL sp_personal_select_by_id(:id)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);  // OBTIENE UNA SOLA FILA/REGISTRO (fetch) EN UN ARRAY ASOCIATIVO (::FETCH_ASSOC)

            $stmt->closeCursor(); // LIMPIA EL CURSOR (OBLIGATORIO PARA STORED PROCEDURES)

            if (!$fila) {
                return null;  // RETORNA NULL SI NO SE ENCUENTRA EL REGISTRO
            }

            if (!empty($fila['idUsuario'])) {
                $usuario = new Usuario(
                    (int)$fila['idUsuario'],
                    $fila['pass_hash'],
                    PerfilAcceso::from($fila['perfil_acceso']),
                    (bool)$fila['activo']
                );
            } else {
                $usuario = null;
            }

            // MAPEO DE FILA DE DATOS AL OBJETO. ASIGNA LOS VALORES DEL REGISTRO A LOS PARÁMETROS DEL CONSTRUCTOR (Recuperación de datos)
            return new Personal(
                (int)$fila['id'],
                $fila['dni'],
                $fila['nombre'],
                $fila['apellido'],
                new DateTimeImmutable($fila['fecha_nacimiento']),  // CONVIERTE STRING A DateTimeImmutable
                $fila['email'],
                $fila['telefono'],
                Sexo::from($fila['sexo']),  // CONVIERTE STRING A ENUM
                Puesto::from($fila['puesto']),  // CONVIERTE STRING A ENUM
                new DateTimeImmutable($fila['fecha_contratacion']),  // CONVIERTE STRING A DateTimeImmutable
                $usuario
            );
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar personal con ID {$id}: " . $e->getMessage());
        }
    }


    public function insertarPersonal(Personal $personal): Personal {
        $sql = "CALL sp_personal_insert(
            :dni, :nombre, :apellido, fechaNacimiento, :email, :telefono, :sexo, :puesto, :fechaContratacion
        )";

        try {
            $stmt = $this->db->prepare($sql);
            // MAPEO DE ATRIBUTOS DEL OBJETO A LOS PARÁMETROS DEL STORED PROCEDURE (Persistencia de datos)
            $stmt->bindValue(':dni', $personal->getDni());
            $stmt->bindValue(':nombre', $personal->getNombre());
            $stmt->bindValue(':apellido', $personal->getApellido());
            $stmt->bindValue(':fechaNacimiento', $personal->getFechaNacimiento()->format('Y-m-d'));
            $stmt->bindValue(':email', $personal->getEmail());
            $stmt->bindValue(':telefono', $personal->getTelefono());
            $stmt->bindValue(':sexo', $personal->getSexo()->value);
            $stmt->bindValue(':puesto', $personal->getPuesto()->value);
            $stmt->bindValue(':fechaContratacion', $personal->getFechaContratacion()->format('Y-m-d'));

            $stmt->execute();
            
            $idPersonal = (int)$stmt->fetchColumn();  // CAPTURAR EL ID DEVUELTO POR EL SP
            $stmt->closeCursor();            
            $personalPersistido = $this->buscarPersonalPorId($idPersonal);  // RETORNAR EL OBJETO COMPLETO CON EL ID ASIGNADO
            
            if ($personalPersistido === null) {
             throw new \RuntimeException("Registro insertado (ID: {$idPersonal}), pero no se pudo recuperar.");
            }

            return $personalPersistido;
            
        } catch (PDOException $e) {
            throw new \Exception("Error al agregar personal: " . $e->getMessage());
        }
    }


    public function existeDni(string $dni): bool {
        $sql = "CALL sp_personal_existe_dni(:dni)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':dni', $dni);
        $stmt->execute();        
        return (bool)$stmt->fetchColumn();  // fetchColumn() devuelve el valor de la primera columna (el '1') si existe la fila, o false si no
    }


    public function existeEmail(string $email): bool {
        $sql = "CALL sp_personal_existe_email(:email)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

}
?>