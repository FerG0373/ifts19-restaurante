<?php
namespace App\Controllers;

use App\Services\ProductoService;
use App\Core\ViewRenderer;
use App\DTOs\ProductoVistaDTO;
use App\DTOs\ProductoAltaDTO;
use App\DTOs\ProductoEdicionDTO;
use App\Mappers\ProductoMapper;
use InvalidArgumentException;
use RuntimeException;

class ProductoController {
    private ProductoService $productoService;
    private ViewRenderer $viewRenderer;

    public function __construct(ProductoService $productoService, ViewRenderer $viewRenderer) {
        $this->productoService = $productoService;
        $this->viewRenderer = $viewRenderer;
    }

    // GET /producto
    public function listarProductos(): void {
        $exito = $_SESSION['msj_exito'] ?? null;
        if (isset($_SESSION['msj_exito'])) {
            unset($_SESSION['msj_exito']);
        }

        try {
            $filtro = $_GET['filtro'] ?? 'activo';
            $esActivo = ($filtro === 'activo');
            
            $listaProductosModelos = [];
            
            if ($esActivo) {
                $listaProductosModelos = $this->productoService->listarProductosActivos();
                $titulo = 'Listado de Productos';
            } else {
                $listaProductosModelos = $this->productoService->listarTodosLosProductos();
                $titulo = 'Listado de Productos (Activos e Inactivos)';
            }
            
            // Mapeo de Modelo a DTO
            $listaDTOs = array_map(function($productoModelo) {
                return ProductoVistaDTO::fromModel($productoModelo);
            }, $listaProductosModelos);

            // Renderizar la vista con los datos
            $this->viewRenderer->renderizarVistaConDatos('4.00-producto', [
                'productos' => $listaDTOs,
                'titulo' => $titulo, 
                'esActivo' => $esActivo,
                'urlVerActivos' => 'producto',
                'urlVerTodos' => 'producto?filtro=todos',
                'exito' => $exito
            ]);

        } catch (\Exception $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    // POST /producto/detalle
    public function verDetalle(): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { 
                header('Location: producto');
                return;
            }
            
            $idProducto = (int)$_POST['id'];

            // Llamar al Service para obtener el objeto Producto completo
            $productoModelo = $this->productoService->mostrarDetalle($idProducto);

            // Verificar si el producto fue encontrado
            if (!$productoModelo) {
                throw new \Exception("El producto con ID {$idProducto} no fue encontrado.");
            }

            // Mapeo de Modelo a DTO
            $productoDTO = ProductoVistaDTO::fromModel($productoModelo);

            // Renderizar la vista de detalle con los datos
            $this->viewRenderer->renderizarVistaConDatos('4.01-producto-detalle', [
                'producto' => $productoDTO,
                'titulo' => 'Detalle de ' . $productoDTO->nombre
            ]);

        } catch (\Exception $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error al Cargar Detalle',
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    // POST /producto/formulario/alta
    public function altaProducto(): void {        
        $datos = $_POST;

        try {            
            $altaDTO = ProductoAltaDTO::fromArray($datos);
            $modeloProducto = ProductoMapper::fromDtoAlta($altaDTO);
            $nuevoProducto = $this->productoService->agregarProducto($modeloProducto);
           
            $_SESSION['msj_exito'] = "El nuevo producto ha sido registrado exitosamente.";
            header('Location: ' . APP_BASE_URL . 'producto');
            exit;

        } catch (InvalidArgumentException $e) {            
            $this->mostrarErrorDeAlta("Error de datos: " . $e->getMessage(), $datos);
            
        } catch (RuntimeException $e) {            
            $this->mostrarErrorDeAlta("Error de negocio: " . $e->getMessage(), $datos);
            
        } catch (\Throwable $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => 'No se pudo completar el alta. Intente nuevamente. Detalles: ' . $e->getMessage()
            ]);
        }
    }

    public function mostrarFormulario(): void {
        $this->viewRenderer->renderizarVistaConDatos('4.02-producto-formulario', [
            'titulo' => 'Alta de Nuevo Producto'
        ]);
    }

    // Método auxiliar para renderizar el formulario de alta con errores y datos precargados
    private function mostrarErrorDeAlta(string $mensajeError, array $datosPrecargados): void {
        $this->viewRenderer->renderizarVistaConDatos('4.02-producto-formulario', [
            'titulo' => 'Alta de Producto (Error)',
            'error' => $mensajeError,
            'datos' => $datosPrecargados,
            'esEdicion' => false
        ]);
    }

    public function cargarFormularioEdicion(): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { 
                header('Location: producto');
                return;
            }
            
            $idProducto = (int)$_POST['id'];

            // Obtener el Producto de la DB (usando el Service)
            $productoModelo = $this->productoService->mostrarDetalle($idProducto);

            // Verificar si el producto fue encontrado
            if (!$productoModelo) {
                throw new \Exception("El producto con ID {$idProducto} no fue encontrado.");
            }

            // Mapear Modelo a DTO de Edición para precargar la vista
            $productoDTO = ProductoMapper::toDtoEdicion($productoModelo);

            // Renderizar la vista de formulario de edición con los datos
            $this->viewRenderer->renderizarVistaConDatos('4.02-producto-formulario', [
                'titulo' => 'Editar Producto',
                'datos' => $productoDTO->toArray(),
                'esEdicion' => true 
            ]);

        } catch (\Exception $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error al Cargar Formulario de Edición',
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    // POST /producto/formulario/editar
    public function editarProducto(): void {
        $datos = $_POST;

        try {
            $DtoEdicion = ProductoEdicionDTO::fromArray($datos);
            $modeloProducto = ProductoMapper::fromDtoEdicion($DtoEdicion);
            $this->productoService->actualizarProducto($modeloProducto); 
            
            $_SESSION['msj_exito'] = "Los datos del producto han sido actualizados exitosamente.";
            header('Location: ' . APP_BASE_URL . 'producto');
            exit;

        } catch (InvalidArgumentException $e) { 
            $this->mostrarErrorDeEdicion("Error de datos: " . $e->getMessage(), $datos);
            
        } catch (RuntimeException $e) { 
            $this->mostrarErrorDeEdicion("Error de negocio: " . $e->getMessage(), $datos);
            
        } catch (\Throwable $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => 'No se pudo completar la edición. Detalles: ' . $e->getMessage()
            ]);
        }
    }

    // MÉTODO AUXILIAR para renderizar el formulario de edición con errores y datos precargados
    private function mostrarErrorDeEdicion(string $mensajeError, array $datosPrecargados): void {
        $this->viewRenderer->renderizarVistaConDatos('4.02-producto-formulario', [
            'titulo' => 'Editar Producto (Error)',
            'error' => $mensajeError,
            'datos' => $datosPrecargados,
            'esEdicion' => true
        ]);
    }
}