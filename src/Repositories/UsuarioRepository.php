<?php
namespace App\Repositories;

use App\Core\DataAccess;
use App\Models\Usuario;
use App\Shared\Enums\PerfilAcceso;
use PDO;
use PDOException;
use RuntimeException;


class UsuarioRepository {
    private PDO $db;

    public function __construct(DataAccess $dataAccess) {
        $this->db = $dataAccess->getConexion();
    }

    
    public function buscarUsuarioPorDni(string $dni): ?Usuario {
        $sql = "CALL sp_usuario_select_by_dni(:dni)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':dni', $dni, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!$fila) {
                return null; // Usuario no encontrado.
            }

            // Mapeo a Objeto de Dominio Usuario.
            return new Usuario(
                (int)$fila['id'],
                PerfilAcceso::from($fila['perfil_acceso']),
                $fila['pass_hash'],
                (bool)$fila['activo']
            );

        } catch (PDOException $e) {
            throw new RuntimeException("Error de base de datos al buscar usuario con DNI {$dni}: " . $e->getMessage());
        } catch (\ValueError $e) {
            throw new RuntimeException("Error de datos: Perfil de acceso inválido en la base de datos.");
        }
    }
}