<?php
namespace App\Repositories;

use App\Core\DataAccess;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Shared\Enums\TipoPedido;
use App\Shared\Enums\EstadoPedido;
use DateTimeImmutable;
use PDO;
use PDOException;
use Exception;

class PedidoRepository {
    private PDO $db;

    public function __construct(DataAccess $dataAccess) {
        $this->db = $dataAccess->getConexion();
    }

    // Listar todos los pedidos
    public function selectAll(): array {
        $query = "CALL sp_pedido_select_all()";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return $this->mapearResultados($filas);
            
        } catch (PDOException $e) {
            throw new Exception("Error al listar todos los pedidos: " . $e->getMessage());
        }
    }

    // Listar pedidos por estado
    public function selectByEstado(string $estado): array {
        $query = "CALL sp_pedido_select_by_estado(:estado)";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':estado', $estado);
            $stmt->execute();
            
            $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return $this->mapearResultados($filas);
            
        } catch (PDOException $e) {
            throw new Exception("Error al listar pedidos por estado: " . $e->getMessage());
        }
    }

    // Obtener un pedido por ID con sus detalles
    public function selectById(int $id): ?Pedido {
        $query = "CALL sp_pedido_select_by_id(:id)";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            if (!$fila) {
                return null;
            }
            
            $pedido = $this->mapearFila($fila);
            
            // Cargar los detalles del pedido
            $detalles = $this->obtenerDetallesPorPedidoId($id);
            $pedido->setDetalles($detalles);
            
            return $pedido;
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener pedido por ID: " . $e->getMessage());
        }
    }

    // Insertar un nuevo pedido con sus detalles (transacción)
    public function insert(Pedido $pedido): Pedido {
        try {
            $this->db->beginTransaction();

            // 1. Insertar el pedido principal
            $query = "CALL sp_pedido_insert(:mesa_id, :personal_id, :tipo_pedido, :total, :observaciones)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':mesa_id', $pedido->getMesaId(), PDO::PARAM_INT);
            $stmt->bindValue(':personal_id', $pedido->getPersonalId(), PDO::PARAM_INT);
            $stmt->bindValue(':tipo_pedido', $pedido->getTipoPedido()->value);
            $stmt->bindValue(':total', $pedido->getTotal());
            $stmt->bindValue(':observaciones', $pedido->getObservaciones());
            
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $pedidoId = (int)$resultado['nuevoId'];
            
            $stmt->closeCursor();

            // 2. Insertar los detalles del pedido
            foreach ($pedido->getDetalles() as $detalle) {
                $this->insertarDetallePedido($pedidoId, $detalle);
            }

            // 3. Cambiar estado de mesa a 'ocupada' (solo si NO es mesa VIRTUAL y tipo es 'mesa')
            if ($pedido->getTipoPedido()->value === 'mesa') {
                $mesaInfo = $this->obtenerInfoMesaPorId($pedido->getMesaId());
                if ($mesaInfo && $mesaInfo['numero_mesa'] !== 'VIRTUAL') {
                    $this->actualizarEstadoMesa($pedido->getMesaId(), 'ocupada');
                }
            }

            $this->db->commit();

            // 4. Retornar el pedido completo con su nuevo ID
            return $this->selectById($pedidoId);

        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Error al insertar pedido: " . $e->getMessage());
        }
    }

    // Insertar detalle de pedido
    private function insertarDetallePedido(int $pedidoId, PedidoDetalle $detalle): void {
        $sql = "CALL sp_detalle_pedido_insert(
            :pedido_id, :producto_id, :cantidad, :precio_unitario, :instrucciones
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pedido_id', $pedidoId, PDO::PARAM_INT);
        $stmt->bindValue(':producto_id', $detalle->getProductoId(), PDO::PARAM_INT);
        $stmt->bindValue(':cantidad', $detalle->getCantidad(), PDO::PARAM_INT);
        $stmt->bindValue(':precio_unitario', $detalle->getPrecioUnitario());
        $stmt->bindValue(':instrucciones', $detalle->getInstruccionesPreparacion());

        $stmt->execute();
        $stmt->closeCursor();
    }

    // Obtener detalles de un pedido
    private function obtenerDetallesPorPedidoId(int $pedidoId): array {
        $sql = "CALL sp_detalle_pedido_select_by_pedido_id(:pedido_id)";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':pedido_id', $pedidoId, PDO::PARAM_INT);
            $stmt->execute();

            $detalles = [];
            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $detalles[] = new PedidoDetalle(
                    (int)$fila['id'],
                    (int)$fila['pedido_id'],
                    (int)$fila['producto_id'],
                    $fila['nombre_producto'],
                    (int)$fila['cantidad'],
                    (float)$fila['precio_unitario'],
                    $fila['instrucciones_preparacion']
                );
            }

            $stmt->closeCursor();
            
            return $detalles;

        } catch (PDOException $e) {
            throw new Exception("Error al obtener detalles del pedido: " . $e->getMessage());
        }
    }

    // Actualizar estado del pedido
    public function updateEstado(int $idPedido, string $nuevoEstado): void {
        $query = "CALL sp_pedido_update_estado(:id, :estado)";
        
        try {
            // Obtener el pedido actual para saber qué mesa liberar
            $pedido = $this->selectById($idPedido);
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $idPedido, PDO::PARAM_INT);
            $stmt->bindValue(':estado', $nuevoEstado);
            $stmt->execute();
            $stmt->closeCursor();
            
            // Si el pedido se marca como entregado o cancelado, liberar la mesa
            if (in_array($nuevoEstado, ['entregado', 'cancelado']) && $pedido) {
                $mesaInfo = $this->obtenerInfoMesaPorId($pedido->getMesaId());
                if ($mesaInfo && $mesaInfo['numero_mesa'] !== 'VIRTUAL') {
                    $this->actualizarEstadoMesa($pedido->getMesaId(), 'libre');
                }
            }
            
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar estado del pedido: " . $e->getMessage());
        }
    }

    // Obtener información básica de una mesa
    private function obtenerInfoMesaPorId(int $mesaId): ?array {
        try {
            $stmt = $this->db->prepare("SELECT id, numero_mesa FROM mesa WHERE id = :id");
            $stmt->bindValue(':id', $mesaId, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    // Actualizar estado de mesa
    private function actualizarEstadoMesa(int $mesaId, string $nuevoEstado): void {
        try {
            $stmt = $this->db->prepare("UPDATE mesa SET estado_mesa = :estado WHERE id = :id");
            $stmt->bindValue(':id', $mesaId, PDO::PARAM_INT);
            $stmt->bindValue(':estado', $nuevoEstado);
            $stmt->execute();
        } catch (PDOException $e) {
            // No lanzar excepción para no interrumpir el flujo del pedido
            // Solo registrar el error si tienes un sistema de logs
        }
    }

    // Obtener mesas disponibles
    public function getMesasDisponibles(): array {
        $query = "SELECT id, numero_mesa, capacidad, ubicacion 
                  FROM mesa 
                  WHERE activo = 1 
                  ORDER BY CASE WHEN numero_mesa = 'VIRTUAL' THEN 1 ELSE 0 END, numero_mesa";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener mesas disponibles: " . $e->getMessage());
        }
    }

    // Obtener información de productos (para validaciones)
    public function getProductosInfo(array $productosIds): array {
        if (empty($productosIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($productosIds), '?'));
        $query = "SELECT id, nombre, precio FROM producto WHERE id IN ($placeholders) AND activo = 1";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($productosIds);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $productos = [];
            foreach ($resultados as $p) {
                $productos[$p['id']] = $p;
            }
            
            return $productos;
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener información de productos: " . $e->getMessage());
        }
    }

    // Mapear múltiples filas a objetos Pedido
    private function mapearResultados(array $filas): array {
        $pedidos = [];
        foreach ($filas as $fila) {
            $pedidos[] = $this->mapearFila($fila);
        }
        return $pedidos;
    }

    // Mapear una fila a un objeto Pedido
    private function mapearFila(array $fila): Pedido {
        return new Pedido(
            id: (int)$fila['id'],
            mesaId: (int)$fila['mesa_id'],
            numeroMesa: $fila['numero_mesa'],
            personalId: (int)$fila['personal_id'],
            nombreMozo: $fila['nombre_mozo'],
            fechaHora: new DateTimeImmutable($fila['fecha_hora']),
            tipoPedido: TipoPedido::from($fila['tipo_pedido']),
            estadoPedido: EstadoPedido::from($fila['estado_pedido']),
            total: (float)$fila['total'],
            observaciones: $fila['observaciones'] ?? null,
            detalles: []
        );
    }
}