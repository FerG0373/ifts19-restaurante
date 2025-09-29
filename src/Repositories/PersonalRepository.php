<?php
namespace App\Repositories;

use App\Models\Personal;
use App\Enums\Puesto;
use App\Enums\Sexo;
use App\Core\DataAccess;
use PDO;
use PDOException;
use DateTimeImmutable;


class PersonalRepository {
    private PDO $db;

    public function __construct(DataAccess $dataAccess) {
        $this->db = $dataAccess->obtenerConexion();
    }

    public function agregarPersonal(Personal $personal): Personal {
        $sql = "CALL sp_personal_insert(
            :dni, :nombre, :apellido, fechaNacimiento, :email, :telefono, :sexo, :puesto, :fechaContratacion
        )";

        try {
            $stmt = $this->db->prepare($sql);
            // MAPEO DE OBJETOS PHP A PARÁMETOS DEL SP
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

            // CAPTURAR EL ID DEVUELTO POR EL SP
            $newId = (int)$stmt->fetchColumn();
            // LIMPIAR EL CURSOR (OBLIGATORIO PARA STORED PROCEDURES)
            $stmt->closeCursor();
            // 4. RETORNAR EL OBJETO COMPLETO CON EL ID ASIGNADO
            $personalPersistido = $this->findById($newId);
            if ($personalPersistido === null) {
             throw new \RuntimeException("Registro insertado (ID: {$newId}), pero no se pudo recuperar.");
            }
        
            return $personalPersistido;
        } catch (PDOException $e) {
            throw new \Exception("Error al agregar personal: " . $e->getMessage());
        }
    }

}
?>