<?php
namespace App\Repositories;

use App\Core\DataAccess;
use App\Models\Factura;
use App\Shared\Enums\MetodoPago;
use App\Shared\Enums\EstadoFactura;
use PDO;
use PDOException;
use DateTimeImmutable;

class FacturaRepository {
    private PDO $db;

    public function __construct(DataAccess $dataAccess) {
        $this->db = $dataAccess->getConexion();
    }

    // Obtener factura por ID de pedido
    public function obtenerFacturaPorPedidoId(int $pedidoId): ?Factura {
        $sql = "CALL sp_factura_select_by_pedido_id(:pedido_id)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':pedido_id', $pedidoId, PDO::PARAM_INT);
            $stmt->execute();
            
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!$fila) {
                return null;
            }

            return $this->mapearFila($fila);

        } catch (PDOException $e) {
            throw new \Exception("Error al obtener factura por pedido: " . $e->getMessage());
        }
    }

    // Obtener factura por ID
    public function obtenerFacturaPorId(int $id): ?Factura {
        $sql = "CALL sp_factura_select_by_id(:id)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!$fila) {
                return null;
            }

            return $this->mapearFila($fila);

        } catch (PDOException $e) {
            throw new \Exception("Error al obtener factura: " . $e->getMessage());
        }
    }

    // Insertar nueva factura
    public function insertarFactura(Factura $factura): Factura {
        $sql = "CALL sp_factura_insert(:pedido_id, :subtotal, :impuestos, :total)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':pedido_id', $factura->getPedidoId(), PDO::PARAM_INT);
            $stmt->bindValue(':subtotal', $factura->getSubtotal());
            $stmt->bindValue(':impuestos', $factura->getImpuestos());
            $stmt->bindValue(':total', $factura->getTotal());
            
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $facturaId = (int)$resultado['nuevoId'];
            
            $stmt->closeCursor();

            // Retornar la factura completa
            $nuevaFactura = $this->obtenerFacturaPorId($facturaId);
            
            if ($nuevaFactura === null) {
                throw new \RuntimeException("Factura insertada (ID: {$facturaId}), pero no se pudo recuperar.");
            }

            return $nuevaFactura;

        } catch (PDOException $e) {
            throw new \Exception("Error al insertar factura: " . $e->getMessage());
        }
    }

    // Procesar pago
    public function procesarPago(int $facturaId, string $metodoPago): void {
        $sql = "CALL sp_factura_procesar_pago(:factura_id, :metodo_pago)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':factura_id', $facturaId, PDO::PARAM_INT);
            $stmt->bindValue(':metodo_pago', $metodoPago);
            $stmt->execute();
            $stmt->closeCursor();

        } catch (PDOException $e) {
            throw new \Exception("Error al procesar pago: " . $e->getMessage());
        }
    }

    // Verificar si un pedido ya tiene factura
    public function pedidoTieneFactura(int $pedidoId): bool {
        $sql = "CALL sp_factura_existe_por_pedido(:pedido_id)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':pedido_id', $pedidoId, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return (int)$resultado['existe'] > 0;

        } catch (PDOException $e) {
            throw new \Exception("Error al verificar factura: " . $e->getMessage());
        }
    }

    // Mapear fila a objeto Factura
    private function mapearFila(array $fila): Factura {
        return new Factura(
            (int)$fila['id'],
            (int)$fila['pedido_id'],
            new DateTimeImmutable($fila['fecha_emision']),
            (float)$fila['subtotal'],
            (float)$fila['impuestos'],
            (float)$fila['total'],
            $fila['metodo_pago'] ? MetodoPago::from($fila['metodo_pago']) : null,
            EstadoFactura::from($fila['estado'])
        );
    }
}