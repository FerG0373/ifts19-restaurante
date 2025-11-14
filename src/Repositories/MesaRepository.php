<?php
namespace App\Repositories;

use App\Core\DataAccess;
use App\Models\Mesa;
use App\Shared\Enums\Ubicacion;
use App\Shared\Enums\EstadoMesa;
use PDO;
use PDOException;


class MesaRepository {
    private PDO $db;

    public function __construct(DataAccess $dataAccess) {
        $this->db = $dataAccess->getConexion();
    }


    public function listarMesas(bool $activo): array {
        if ($activo) {
            $sql = "CALL sp_mesa_select_activo()";
        } else {
            $sql = "CALL sp_mesa_select_all()";
        }

        $listaDeMesas = [];
            
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Mapeo de la fila de datos al Objeto de Dominio Mesa
                $listaDeMesas[] = new Mesa(
                    (int)$fila['id'],
                    $fila['nroMesa'],
                    (int)$fila['capacidad'],
                    Ubicacion::from($fila['ubicacion']),
                    EstadoMesa::from($fila['estadoMesa']),
                    (bool)$fila['activo']
                );
            }

            $stmt->closeCursor();

            return $listaDeMesas;

        } catch (PDOException $e) {
            throw new \Exception("Error al listar mesas: " . $e->getMessage());
        }
    }
}