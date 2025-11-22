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
    

    public function listarMesasPorUbicacion(string $ubicacion): array {
        
        // Llamada al Stored Procedure que recibe la ubicación como parámetro.
        $sql = "CALL sp_mesa_select_by_ubicacion(:ubicacion)";
        
        $listaDeMesas = [];
            
        try {
            $stmt = $this->db->prepare($sql);
            
            // Bindeo del parámetro de ubicación
            $stmt->bindParam(':ubicacion', $ubicacion, PDO::PARAM_STR);
            $stmt->execute();

            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Mapeo de la fila de datos al Objeto de Dominio Mesa
                // Nota: Eliminamos el campo 'activo' del constructor de Mesa.
                $listaDeMesas[] = new Mesa(
                    (int)$fila['id'],
                    $fila['numero_mesa'],
                    (int)$fila['capacidad'],
                    Ubicacion::from($fila['ubicacion']),
                    EstadoMesa::from($fila['estado_mesa'])
                    // El campo 'activo' ya no se mapea.
                );
            }

            $stmt->closeCursor();

            return $listaDeMesas;

        } catch (PDOException $e) {
            // Manejo de errores de base de datos
            throw new \Exception("Error al listar mesas por ubicación: " . $e->getMessage());
        }
    }
}