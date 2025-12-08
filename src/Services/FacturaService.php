<?php
namespace App\Services;

use App\Models\Factura;
use App\Repositories\FacturaRepository;
use App\Repositories\PedidoRepository;
use App\Repositories\MesaRepository;
use App\Shared\Enums\MetodoPago;
use App\Shared\Enums\EstadoMesa;
use App\Shared\Enums\EstadoFactura;
use RuntimeException;

class FacturaService {
    private FacturaRepository $facturaRepository;
    private PedidoRepository $pedidoRepository;
    private MesaRepository $mesaRepository;

    public function __construct(
        FacturaRepository $facturaRepository, 
        PedidoRepository $pedidoRepository,
        MesaRepository $mesaRepository
    ) {
        $this->facturaRepository = $facturaRepository;
        $this->pedidoRepository = $pedidoRepository;
        $this->mesaRepository = $mesaRepository;
    }

    // Generar factura desde un pedido
    public function generarFacturaDesdePedido(int $pedidoId): Factura {
        // Verificar que el pedido exista
        $pedido = $this->pedidoRepository->selectById($pedidoId);
        if (!$pedido) {
            throw new RuntimeException("El pedido no existe.");
        }

        // Verificar que el pedido esté en estado válido para facturar
        $estadosValidos = ['listo', 'entregado'];
        if (!in_array($pedido->getEstadoPedido()->value, $estadosValidos)) {
            throw new RuntimeException("El pedido debe estar en estado 'Listo' o 'Entregado' para poder facturarlo.");
        }

        // Verificar que no tenga factura ya
        if ($this->pedidoTieneFactura($pedidoId)) {
            throw new RuntimeException("Este pedido ya tiene una factura generada.");
        }

        // Calcular totales
        $subtotal = $pedido->getTotal();
        $impuestos = Factura::calcularImpuestos($subtotal, 21.0);
        $total = Factura::calcularTotal($subtotal, $impuestos);

        // Crear objeto Factura
        $nuevaFactura = new Factura(
            null, // id (se asigna al insertar)
            $pedidoId,
            new \DateTimeImmutable(), // fecha actual
            $subtotal,
            $impuestos,
            $total,
            null, // método de pago (se asigna al pagar)
            \App\Shared\Enums\EstadoFactura::PENDIENTE
        );

        // Insertar factura
        $facturaInsertada = $this->facturaRepository->insertarFactura($nuevaFactura);

        return $facturaInsertada;
    }

    // Obtener factura por pedido
    public function obtenerFacturaPorPedido(int $pedidoId): ?Factura {
        return $this->facturaRepository->obtenerFacturaPorPedidoId($pedidoId);
    }

    // Obtener factura por ID
    public function obtenerFacturaPorId(int $facturaId): ?Factura {
        return $this->facturaRepository->obtenerFacturaPorId($facturaId);
    }

    // Procesar pago de una factura
    public function procesarPago(int $facturaId, string $metodoPago): void {
        // Validar método de pago
        try {
            $metodoPagoEnum = MetodoPago::from($metodoPago);
        } catch (\ValueError $e) {
            throw new RuntimeException("Método de pago no válido.");
        }

        // Obtener factura
        $factura = $this->facturaRepository->obtenerFacturaPorId($facturaId);
        if (!$factura) {
            throw new RuntimeException("La factura no existe.");
        }

        // Verificar que esté pendiente
        if ($factura->getEstado()->value !== 'pendiente') {
            throw new RuntimeException("Esta factura ya fue procesada.");
        }

        // Procesar pago en la factura
        $this->facturaRepository->procesarPago($facturaId, $metodoPago);

        // NUEVO: Liberar la mesa si el pedido tiene mesa asignada
        $pedido = $this->pedidoRepository->selectById($factura->getPedidoId());
        if ($pedido && $pedido->getMesaId()) {
            // Cambiar estado de la mesa a LIBRE
            $this->mesaRepository->actualizarEstadoMesa($pedido->getMesaId(), EstadoMesa::LIBRE->value);
        }
    }

    // Verificar si un pedido tiene factura
    public function pedidoTieneFactura(int $pedidoId): bool {
        return $this->facturaRepository->pedidoTieneFactura($pedidoId);
    }
}