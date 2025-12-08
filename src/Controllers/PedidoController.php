<?php
namespace App\Controllers;

use App\Services\PedidoService;
use App\Services\ProductoService;
use App\Core\ViewRenderer;
use App\DTOs\PedidoVistaDTO;
use App\DTOs\PedidoAltaDTO;
use App\Mappers\PedidoMapper;
use InvalidArgumentException;
use RuntimeException;

class PedidoController {
    private PedidoService $pedidoService;
    private ProductoService $productoService;
    private ViewRenderer $viewRenderer;

    public function __construct(PedidoService $pedidoService, ProductoService $productoService, ViewRenderer $viewRenderer) {
        $this->pedidoService = $pedidoService;
        $this->productoService = $productoService;
        $this->viewRenderer = $viewRenderer;
    }

    // GET /pedido
    public function listarPedidos(): void {
        $exito = $_SESSION['msj_exito'] ?? null;
        if (isset($_SESSION['msj_exito'])) {
            unset($_SESSION['msj_exito']);
        }

        try {
            $filtroEstado = $_GET['estado'] ?? 'todos';
            
            if ($filtroEstado === 'todos') {
                $listaPedidosModelos = $this->pedidoService->listarTodosLosPedidos();
                $titulo = 'Todos los Pedidos';
            } else {
                $listaPedidosModelos = $this->pedidoService->listarPedidosPorEstado($filtroEstado);
                $titulo = 'Pedidos - ' . ucfirst($filtroEstado);
            }
            
            // Mapeo de Modelo a DTO
            $listaDTOs = array_map(function($pedidoModelo) {
                return PedidoVistaDTO::fromModel($pedidoModelo);
            }, $listaPedidosModelos);

            // Renderizar la vista con los datos
            $this->viewRenderer->renderizarVistaConDatos('4.00-pedido', [
                'pedidos' => $listaDTOs,
                'titulo' => $titulo,
                'filtroEstado' => $filtroEstado,
                'exito' => $exito
            ]);

        } catch (\Exception $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    // POST /pedido/detalle
    public function verDetalle(): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { 
                header('Location: pedido');
                return;
            }
            
            $idPedido = (int)$_POST['id'];

            // Llamar al Service para obtener el objeto Pedido completo
            $pedidoModelo = $this->pedidoService->mostrarDetalle($idPedido);

            // Verificar si el pedido fue encontrado
            if (!$pedidoModelo) {
                throw new \Exception("El pedido con ID {$idPedido} no fue encontrado.");
            }

            // Mapeo de Modelo a DTO
            $pedidoDTO = PedidoVistaDTO::fromModel($pedidoModelo);

            // Renderizar la vista de detalle con los datos
            $this->viewRenderer->renderizarVistaConDatos('4.01-pedido-detalle', [
                'pedido' => $pedidoDTO,
                'titulo' => 'Pedido #' . $pedidoDTO->id . ' - Mesa ' . $pedidoDTO->numeroMesa
            ]);

        } catch (\Exception $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error al Cargar Detalle',
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    // GET /pedido/formulario (Mostrar formulario de nuevo pedido)
    public function mostrarFormulario(): void {
    try {
        // Obtener mesas disponibles
        $mesas = $this->pedidoService->obtenerMesasDisponibles();
        
        // Obtener productos activos
        $productos = $this->productoService->listarProductosActivos();
        
        // Buscar ID de mesa virtual
        $mesaVirtualId = null;
        foreach ($mesas as $mesa) {
            if ($mesa['numero_mesa'] === 'VIRTUAL') {
                $mesaVirtualId = $mesa['id'];
                break;
            }
        }
        
        $this->viewRenderer->renderizarVistaConDatos('4.02-pedido-formulario', [
            'titulo' => 'Nuevo Pedido',
            'mesas' => $mesas,
            'productos' => $productos,
            'mesaVirtualId' => $mesaVirtualId  // ⬅️ PASAR A LA VISTA
        ]);

    } catch (\Exception $e) {
        $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
            'titulo' => 'Error al Cargar Formulario',
            'mensaje' => $e->getMessage()
        ]);
    }
}

    // POST /pedido/formulario/alta
    public function altaPedido(): void {
        $datos = $_POST;

        try {
            // Crear el DTO desde los datos del formulario
            $altaDTO = PedidoAltaDTO::fromArray($datos);
            
            // Obtener información de los productos seleccionados
            $productosIds = array_column($altaDTO->detalles, 'producto_id');
            $productosInfo = $this->pedidoService->obtenerInfoProductos($productosIds);
            
            // Mapear DTO a Modelo usando el Mapper
            $modeloPedido = PedidoMapper::fromDtoAlta($altaDTO, $productosInfo);
            
            // Guardar el pedido
            $nuevoPedido = $this->pedidoService->agregarPedido($modeloPedido);
           
            $_SESSION['msj_exito'] = "El pedido #{$nuevoPedido->getId()} ha sido registrado exitosamente.";
            header('Location: ' . APP_BASE_URL . 'pedido');
            exit;

        } catch (InvalidArgumentException $e) {
            $this->mostrarErrorDeAlta("Error de datos: " . $e->getMessage(), $datos);
            
        } catch (RuntimeException $e) {
            $this->mostrarErrorDeAlta("Error de negocio: " . $e->getMessage(), $datos);
            
        } catch (\Throwable $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => 'No se pudo completar el alta del pedido. Detalles: ' . $e->getMessage()
            ]);
        }
    }

    // POST /pedido/cambiar-estado
    public function cambiarEstado(): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || empty($_POST['nuevo_estado'])) {
                header('Location: pedido');
                return;
            }

            $idPedido = (int)$_POST['id'];
            $nuevoEstado = $_POST['nuevo_estado'];

            $this->pedidoService->cambiarEstado($idPedido, $nuevoEstado);

            $_SESSION['msj_exito'] = "El estado del pedido #{$idPedido} ha sido actualizado.";
            header('Location: ' . APP_BASE_URL . 'pedido');
            exit;

        } catch (RuntimeException $e) {
            $_SESSION['msj_error'] = "Error: " . $e->getMessage();
            header('Location: ' . APP_BASE_URL . 'pedido');
            exit;

        } catch (\Throwable $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => 'No se pudo cambiar el estado del pedido. Detalles: ' . $e->getMessage()
            ]);
        }
    }

    // Método auxiliar para renderizar el formulario con errores
    private function mostrarErrorDeAlta(string $mensajeError, array $datosPrecargados): void {
        try {
            $mesas = $this->pedidoService->obtenerMesasDisponibles();
            $productos = $this->productoService->listarProductosActivos();
            
            $this->viewRenderer->renderizarVistaConDatos('4.02-pedido-formulario', [
                'titulo' => 'Nuevo Pedido (Error)',
                'error' => $mensajeError,
                'datos' => $datosPrecargados,
                'mesas' => $mesas,
                'productos' => $productos
            ]);
        } catch (\Exception $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}