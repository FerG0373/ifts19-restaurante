<?php
namespace App\Repositories;

use App\Core\DataAccess;
use App\Models\Mesa;
use App\Shared\Enums\Ubicacion;
use App\Shared\Enums\EstadoMesa;
use PDO;
use PDOException;
use InvalidArgumentException;
use RuntimeException;


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
            
            // Bindeo del parámetro ubicación.
            $stmt->bindParam(':ubicacion', $ubicacion, PDO::PARAM_STR);
            $stmt->execute();

            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Mapeo de la fila de datos al Objeto de Dominio Mesa.
                $listaDeMesas[] = new Mesa(
                    (int)$fila['id'],
                    $fila['numero_mesa'],
                    (int)$fila['capacidad'],
                    Ubicacion::from($fila['ubicacion']),
                    EstadoMesa::from($fila['estado_mesa'])
                );
            }

            $stmt->closeCursor();

            return $listaDeMesas;

        } catch (PDOException $e) {
            throw new \Exception("Error al listar mesas por ubicación: " . $e->getMessage());
        }
    }


    public function obtenerMesaPorId(int $id): ?Mesa {
        $sql = "CALL sp_mesa_select_by_id(:id)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            if (!$fila) {
                return null;
            }

            // MAPEO DE FILA DE DATOS AL OBJETO MESA.
            return new Mesa(
                (int)$fila['id'],
                $fila['numero_mesa'],
                (int)$fila['capacidad'],
                Ubicacion::from($fila['ubicacion']), // CONVIERTE STRING A ENUM.
                EstadoMesa::from($fila['estado_mesa']) // CONVIERTE STRING A ENUM.
            );
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar mesa con ID {$id}: " . $e->getMessage());
        }
    }


    public function insertarMesa(Mesa $mesa): Mesa {
        $sql = "CALL sp_mesa_insert(
            :p_numero_mesa, :p_capacidad, :p_ubicacion
        )";

        try {
            $stmt = $this->db->prepare($sql);
            
            // MAPEO DE ATRIBUTOS DEL OBJETO A LOS PARÁMETROS DEL STORED PROCEDURE
            $stmt->bindValue(':p_numero_mesa', $mesa->getNroMesa());
            $stmt->bindValue(':p_capacidad', $mesa->getCapacidad());
            // Usamos ->value para obtener el string subyacente del Backed Enum
            $stmt->bindValue(':p_ubicacion', $mesa->getUbicacion()->value);
            
            $stmt->execute();
            
            $idMesa = (int)$stmt->fetchColumn(); // CAPTURAR EL ID DEVUELTO POR EL SP
            $stmt->closeCursor(); // Cierra el conjunto de resultados para permitir más consultas
            
            // Si tu SP devuelve el ID, usamos ese ID para recuperar el objeto completo.
            $nuevaMesa = $this->obtenerMesaPorId($idMesa); 
            
            if ($nuevaMesa === null) {
                throw new RuntimeException("Registro insertado (ID: {$idMesa}), pero no se pudo recuperar de la DB.");
            }

            return $nuevaMesa;
            
        } catch (PDOException $e) {
            // Manejo de error de clave duplicada (común en MySQL al usar UNIQUE constraint)
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'nroMesa')) {
                throw new InvalidArgumentException("Ya existe una mesa con el número '{$mesa->getNroMesa()}' en la ubicación '{$mesa->getUbicacion()->value}'.");
            }
            throw new \Exception("Error de base de datos al dar de alta la mesa: " . $e->getMessage());
        }
    }
}