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

    /**
     * Busca un usuario por su ID (que es el DNI).
     *
     * @param int $dni
     * @return Usuario|null
     * @throws \Exception
     */
    public function buscarUsuarioPorId(int $dni): ?Usuario {
        // Usamos el ID de la tabla 'usuario' que corresponde al DNI/ID del personal
        $sql = "SELECT id, perfil_acceso, pass_hash, activo FROM usuario WHERE id = :dni";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':dni', $dni, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!$fila) {
                return null; // Usuario no encontrado
            }

            // Mapeo a Objeto de Dominio Usuario
            return new Usuario(
                (int)$fila['id'],
                PerfilAcceso::from($fila['perfil_acceso']),
                $fila['pass_hash'],
                (bool)$fila['activo']
            );

        } catch (PDOException $e) {
            // Manejo de errores de base de datos
            throw new RuntimeException("Error de base de datos al buscar usuario con DNI {$dni}: " . $e->getMessage());
        } catch (\ValueError $e) {
            // Manejo de errores si el ENUM no existe (ej: "adminn" en la DB)
            throw new RuntimeException("Error de datos: Perfil de acceso inválido en la base de datos.");
        }
    }
}