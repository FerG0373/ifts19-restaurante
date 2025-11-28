<?php
namespace App\Repositories;

use App\Core\DataAccess;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Shared\Enums\TipoPedido;
use App\Shared\Enums\EstadoPedido;
use DateTimeImmutable;
use PDO;
use Exception;

class PedidoRepository {
    private PDO $db; 
    private DataAccess $dataAccess;
   

    public function __construct(DataAccess $dataAccess) {
          $this->db = $dataAccess->getConexion(); 
          $this->dataAccess = $dataAccess;
    }

    public function selectAll(): array {
        $query = "CALL sp_pedido_select_all()";
        $stmt = $this->db->prepare($query);  
        $stmt->execute();
        
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapearResultados($filas);
    }

    public function selectByEstado(string $estado): array {
        $query = "CALL sp_pedido_select_by_estado(:estado)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':estado', $estado);
        $stmt->execute();
        
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->mapearResultados($filas);
    }


    public function selectById(int $id): ?Pedido {
        $query = "CALL sp_pedido_select_by_id(:id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$fila) {
            return null;
        }
        return $this->mapearFila($fila);
    }


    public function insert(Pedido $pedido): Pedido {
        try {
            $this->dataAccess->beginTransaction();

            // Insertar el pedido principal
            $query = "CALL sp_pedido_insert(:mesa_id, :personal_id, :tipo_pedido, :estado_pedido, :total, :observaciones)";
            $params = [
                ':mesa_id' => $pedido->getMesaId(),
                ':personal_id' => $pedido->getPersonalId(),
                ':tipo_pedido' => $pedido->getTipoPedido()->getValue(),
                ':estado_pedido' => $pedido->getEstadoPedido()->getValue(),
                ':total' => $pedido->getTotal(),
                ':observaciones' => $pedido->getObservaciones()
            ];

            $stmt = $this->dataAccess->ejecutarConsulta($query, $params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $pedidoId = $resultado['nuevoId'];

            // Insertar los detalles del pedido
            foreach ($pedido->getDetalles() as $detalle) {
                $this->insertarDetallePedido($pedidoId, $detalle);
            }

            $this->dataAccess->commit();

            // Retornar el pedido con su nuevo ID
            return $this->selectById($pedidoId);

        } catch (Exception $e) {
            $this->dataAccess->rollback();
            throw new Exception("Error al insertar pedido: " . $e->getMessage());
        }
    }

    private function insertarDetallePedido(int $pedidoId, PedidoDetalle $detalle): void {
        $queryDetalle = "CALL sp_detalle_pedido_insert(:pedido_id, :producto_id, :cantidad, :precio_unitario, :instrucciones)";
        $paramsDetalle = [
            ':pedido_id' => $pedidoId,
            ':producto_id' => $detalle->getProductoId(),
            ':cantidad' => $detalle->getCantidad(),
            ':precio_unitario' => $detalle->getPrecioUnitario(),
            ':instrucciones' => $detalle->getInstruccionesPreparacion()
        ];
        $this->dataAccess->ejecutarConsulta($queryDetalle, $paramsDetalle);
    }

    public function updateEstado(int $idPedido, string $nuevoEstado): void {
        $query = "CALL sp_pedido_update_estado(:id, :estado)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $idPedido);
        $stmt->bindValue(':estado', $nuevoEstado);
        $stmt->execute();
    }

    public function getMesasDisponibles(): array {
        $query = "SELECT id, numero_mesa, capacidad, ubicacion FROM mesa WHERE activo = 1 ORDER BY numero_mesa";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getProductosInfo(array $productosIds): array {
        if (empty($productosIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($productosIds), '?'));
        $query = "SELECT id, nombre, precio FROM producto WHERE id IN ($placeholders) AND activo = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($productosIds);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $productos = [];
        foreach ($resultados as $p) {
            $productos[$p['id']] = $p;
        }
        
        return $productos;
    }


    private function mapearResultados(array $filas): array {
        $pedidos = [];
        foreach ($filas as $fila) {
            $pedidos[] = $this->mapearFila($fila);
        }
        return $pedidos;
    }

    private function mapearFila(array $fila): Pedido {
        return new Pedido(
            id: $fila['id'],
            mesaId: $fila['mesa_id'],
            numeroMesa: $fila['numero_mesa'],
            personalId: $fila['personal_id'],
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