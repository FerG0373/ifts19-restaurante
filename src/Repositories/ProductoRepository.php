<?php
namespace App\Repositories;

use App\Core\DataAccess;
use App\Models\Producto;
use App\Shared\Enums\Categoria;
use PDO;
use PDOException;

class ProductoRepository {
    private PDO $db;

    public function __construct(DataAccess $dataAccess) {
        $this->db = $dataAccess->getConexion();
    }

    public function listarProductos(bool $activo): array {
        if ($activo) {
            $sql = "CALL sp_producto_select_activo()";
        } else {
            $sql = "CALL sp_producto_select_all()";
        }

        $listaDeProductos = [];
            
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            // Recorre todos los resultados
            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // MAPEO DE FILA DE DATOS AL OBJETO
                $listaDeProductos[] = new Producto(
                    (int)$fila['id'],
                    $fila['nombre'],
                    $fila['descripcion'] ?? '',
                    (float)$fila['precio'],
                    (int)$fila['cantidad_stock'],
                    Categoria::from($fila['categoria']),
                    (bool)$fila['activo']
                );
            }

            $stmt->closeCursor();

            return $listaDeProductos;

        } catch (PDOException $e) {
            throw new \Exception("Error al listar productos: " . $e->getMessage());
        }
    }

    public function obtenerProductoPorId(int $id): ?Producto {
        $sql = "CALL sp_producto_select_by_id(:id)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            if (!$fila) {
                return null;  // Retorna NULL si no se encuentra el registro
            }

            // MAPEO DE FILA DE DATOS AL OBJETO
            return new Producto(
                (int)$fila['id'],
                $fila['nombre'],
                $fila['descripcion'] ?? '',
                (float)$fila['precio'],
                (int)$fila['cantidad_stock'],
                Categoria::from($fila['categoria']),
                (bool)$fila['activo']
            );
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar producto con ID {$id}: " . $e->getMessage());
        }
    }

    public function insertarProducto(Producto $producto): Producto {
        $sql = "CALL sp_producto_insert(
            :nombre, :descripcion, :precio, :cantidad_stock, :categoria
        )";

        try {
            $stmt = $this->db->prepare($sql);
            // MAPEO DE ATRIBUTOS DEL OBJETO A LOS PARÁMETROS DEL STORED PROCEDURE
            $stmt->bindValue(':nombre', $producto->getNombre());
            $stmt->bindValue(':descripcion', $producto->getDescripcion());
            $stmt->bindValue(':precio', $producto->getPrecio());
            $stmt->bindValue(':cantidad_stock', $producto->getCantidadStock(), PDO::PARAM_INT);
            $stmt->bindValue(':categoria', $producto->getCategoria()->value);

            $stmt->execute();
            
            $idProducto = (int)$stmt->fetchColumn();  // CAPTURAR EL ID DEVUELTO POR EL SP
            $stmt->closeCursor();
            
            $nuevoProducto = $this->obtenerProductoPorId($idProducto);  // RETORNAR EL OBJETO COMPLETO
            
            if ($nuevoProducto === null) {
                throw new \RuntimeException("Registro insertado (ID: {$idProducto}), pero no se pudo recuperar.");
            }

            return $nuevoProducto;
            
        } catch (PDOException $e) {
            throw new \Exception("Error al agregar producto: " . $e->getMessage());
        }
    }

    public function existeNombre(string $nombre): bool {
        $sql = "CALL sp_producto_existe_nombre(:nombre)";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->execute();
            $resultado = (bool)$stmt->fetchColumn();
            $stmt->closeCursor();
            return $resultado;
        } catch (PDOException $e) {
            throw new \Exception("Error al verificar existencia de nombre: " . $e->getMessage());
        }
    }

    public function updateProducto(Producto $producto): void {
        $sql = "CALL sp_producto_update(
            :id, :nombre, :descripcion, :precio, :cantidad_stock, :categoria, :activo
        )";

        try {
            $stmt = $this->db->prepare($sql);
            // MAPEO DE ATRIBUTOS DEL OBJETO A LOS PARÁMETROS DEL STORED PROCEDURE
            $stmt->bindValue(':id', $producto->getId(), PDO::PARAM_INT);
            $stmt->bindValue(':nombre', $producto->getNombre());
            $stmt->bindValue(':descripcion', $producto->getDescripcion());
            $stmt->bindValue(':precio', $producto->getPrecio());
            $stmt->bindValue(':cantidad_stock', $producto->getCantidadStock(), PDO::PARAM_INT);
            $stmt->bindValue(':categoria', $producto->getCategoria()->value);
            $stmt->bindValue(':activo', $producto->isActivo(), PDO::PARAM_BOOL);

            $stmt->execute();
            $stmt->closeCursor();
            
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar producto con ID {$producto->getId()}: " . $e->getMessage());
        }
    }
}