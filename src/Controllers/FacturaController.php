<?php
namespace App\Controllers;

use App\Services\FacturaService;
use App\Services\PedidoService;
use App\Core\ViewRenderer;
use App\DTOs\FacturaVistaDTO;
use App\DTOs\PedidoVistaDTO;
use App\DTOs\FacturaPagoDTO;
use InvalidArgumentException;
use RuntimeException;

class FacturaController {
    private FacturaService $facturaService;
    private PedidoService $pedidoService;
    private ViewRenderer $viewRenderer;

    public function __construct(FacturaService $facturaService, PedidoService $pedidoService, ViewRenderer $viewRenderer) {
        $this->facturaService = $facturaService;
        $this->pedidoService = $pedidoService;
        $this->viewRenderer = $viewRenderer;
    }

    // POST /factura/generar - Generar factura desde pedido
    public function generarFactura(): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['pedido_id'])) {
                header('Location: ' . APP_BASE_URL . 'pedido');
                exit;
            }

            $pedidoId = (int)$_POST['pedido_id'];

            // Generar factura
            $factura = $this->facturaService->generarFacturaDesdePedido($pedidoId);

            $_SESSION['msj_exito'] = "Factura generada exitosamente. Total: $" . number_format($factura->getTotal(), 2);
            
            // Redirigir a ver la factura
            header('Location: ' . APP_BASE_URL . 'factura/ver?pedido_id=' . $pedidoId);
            exit;

        } catch (RuntimeException $e) {
            $_SESSION['msj_error'] = "Error: " . $e->getMessage();
            header('Location: ' . APP_BASE_URL . 'pedido');
            exit;

        } catch (\Throwable $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [
                'titulo' => 'Error de Sistema',
                'mensaje' => 'No se pudo generar la factura. Detalles: ' . $e->getMessage()
            ]);
        }
    }

    // GET /factura/ver - Ver factura de un pedido
    public function verFactura(): void {
        try {
            $pedidoId = (int)($_GET['pedido_id'] ?? 0);

            if ($pedidoId <= 0) {
                throw new \Exception("ID de pedido inválido.");
            }

            // Obtener pedido
            $pedido = $this->pedidoService->mostrarDetalle($pedidoId);
            if (!$pedido) {
                throw new \Exception("Pedido no encontrado.");
            }

            // Obtener factura
            $factura = $this->facturaService->obtenerFacturaPorPedido($pedidoId);
            if (!$factura) {
                throw new \Exception("Este pedido no tiene factura generada.");
            }

            // Mapear a DTOs
            $facturaDTO = FacturaVistaDTO::fromModel($factura);
            $pedidoDTO = PedidoVistaDTO::fromModel($pedido);

            // Mensajes de sesión
            $exito = $_SESSION['msj_exito'] ?? null;
            $error = $_SESSION['msj_error'] ?? null;
            unset($_SESSION['msj_exito'], $_SESSION['msj_error']);

            $this->viewRenderer->renderizarVistaConDatos('5.00-factura', [
                'titulo' => 'Factura #' . $facturaDTO->id,
                'factura' => $facturaDTO,
                'pedido' => $pedidoDTO,
                'exito' => $exito,
                'error' => $error
            ]);

        } catch (\Exception $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [
                'titulo' => 'Error al Cargar Factura',
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    // POST /factura/pagar - Procesar pago
    public function procesarPago(): void {
        $datos = $_POST;

        try {
            $pagoDTO = FacturaPagoDTO::fromArray($datos);
            
            $this->facturaService->procesarPago($pagoDTO->facturaId, $pagoDTO->metodoPago);

            $_SESSION['msj_exito'] = "¡Pago procesado exitosamente!";
            
            // Obtener pedido_id para redirigir
            $factura = $this->facturaService->obtenerFacturaPorId($pagoDTO->facturaId);
            header('Location: ' . APP_BASE_URL . 'factura/ver?pedido_id=' . $factura->getPedidoId());
            exit;

        } catch (InvalidArgumentException $e) {
            $_SESSION['msj_error'] = "Error de datos: " . $e->getMessage();
            header('Location: ' . APP_BASE_URL . 'pedido');
            exit;

        } catch (RuntimeException $e) {
            $_SESSION['msj_error'] = "Error: " . $e->getMessage();
            header('Location: ' . APP_BASE_URL . 'pedido');
            exit;

        } catch (\Throwable $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [
                'titulo' => 'Error de Sistema',
                'mensaje' => 'No se pudo procesar el pago. Detalles: ' . $e->getMessage()
            ]);
        }
    }
}