<?php
namespace App\Controllers;

use App\Services\MesaService;
use App\Core\ViewRenderer;
use App\DTOs\MesaVistaDTO;
use App\DTOs\MesaAltaDTO;
use App\Shared\Enums\Ubicacion;
use App\Mappers\MesaMapper;
use InvalidArgumentException;
use RuntimeException;


class MesaController {
    private MesaService $mesaService;
    private ViewRenderer $viewRenderer;

    public function __construct(MesaService $mesaService, ViewRenderer $viewRenderer) {
        $this->mesaService = $mesaService;
        $this->viewRenderer = $viewRenderer;
    }

    // GET /mesas
    public function listarMesasSegunUbicacion(): void {
        $exito = $_SESSION['msj_exito'] ?? null;
        if (isset($_SESSION['msj_exito'])) {
            unset($_SESSION['msj_exito']);
        }

        try {
            // Determina la ubicación activa de la URL
            $ubicacionActiva = $_GET['ubicacion'] ?? 'salon'; // 'salon' es el valor por defecto.

            // Valida que la ubicación sea una de las permitidas
            $ubicacionesPermitidas = ['salon', 'exterior'];
            if (!in_array($ubicacionActiva, $ubicacionesPermitidas)) {
                // Si la ubicación no es válida, usamos el valor por defecto.
                $ubicacionActiva = 'salon';
            }

            // Obtiene las mesas del Service filtradas por la ubicación activa.
            $listaMesaModelos = $this->mesaService->listarMesasPorUbicacion($ubicacionActiva);
            
            $titulo = 'Tablero de Mesas Operativas';
            
            // Mapeo de Modelo a DTO de Vista.
            $listaDTOs = array_map(function($mesaModelo) {
                return MesaVistaDTO::fromModel($mesaModelo);
            }, $listaMesaModelos);

            // Renderiza la vista con los datos y la ubicación activa.
            $this->viewRenderer->renderizarVistaConDatos('3.00-mesa', [
                'mesas' => $listaDTOs,
                'titulo' => $titulo, 
                'ubicacionActiva' => $ubicacionActiva,
                'exito' => $exito
            ]);

        } catch (\Exception $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => $e->getMessage()
            ]);
        }
    }
    
    // GET /mesas/formulario
    public function mostrarFormulario(): void {
        // Obtener los valores válidos del Enum para el selectbox. Esto crea un array como ['salon', 'exterior', 'barra']
        $ubicacionesValidas = array_map(fn($case) => $case->value, Ubicacion::cases()); 

        $this->viewRenderer->renderizarVistaConDatos('3.01-mesa-formulario', [
            'titulo' => 'Alta de Nueva Mesa',
            'ubicaciones' => $ubicacionesValidas, 
            'error' => $_SESSION['error_form'] ?? null, 
            'datos' => $_SESSION['data_form'] ?? [],
            'esEdicion' => false,
        ]);
        
        // Limpia la sesión después de renderizar (para que no aparezca el error en la próxima carga limpia).
        unset($_SESSION['error_form'], $_SESSION['data_form']);
    }


    
    // POST /mesas/formulario: Procesa el envío del formulario para crear una mesa.
    public function altaMesa(): void {
        $datos = $_POST; // Capturamos los datos del formulario.
        
        try {
            // DTO: Construir, mapear y validar los datos de $_POST.
            $dto = MesaAltaDTO::fromArray($datos);
            // MAPPER: Conversión de DTO a Modelo de Dominio (Mesa).
            // Esto convierte strings a Enums (Ubicacion, EstadoMesa) y asigna el estado inicial.
            $mesaModel = MesaMapper::fromDtoAlta($dto);
            // SERVICE: Lógica de negocio (chequeos extra) y Persistencia (Repository).
            $mesaModel = $this->mesaService->agregarMesa($mesaModel);            
            // Redirección con éxito.
            $_SESSION['msj_exito'] = "Mesa N° {$mesaModel->getNroMesa()} creada con éxito en " . strtoupper($mesaModel->getUbicacion()->value) . ".";            
            // Redirigir al tablero de mesas, filtrando por la ubicación de la mesa recién creada.
            $ubicacionRedirigir = strtolower($mesaModel->getUbicacion()->value);
            header("Location: " . APP_BASE_URL . "mesas?ubicacion={$ubicacionRedirigir}");
            exit;

        } catch (InvalidArgumentException $e) {
            // Errores de Formato/Validación (vienen del DTO o el Mapper, ej: capacidad no numérica, ubicación inválida).
            $_SESSION['error_form'] = "Error de datos: " . $e->getMessage();
            $_SESSION['data_form'] = $datos;            
            // Redirige al método GET para mostrar el formulario con errores.
            header("Location: " . APP_BASE_URL . "mesas/formulario"); 
            exit;

        } catch (RuntimeException $e) { 
            // Errores de Negocio (vienen del Service/Repository, ej: nroMesa duplicado, error de BD específico).
            $_SESSION['error_form'] = "Error de negocio: " . $e->getMessage();
            $_SESSION['data_form'] = $datos;            
            header("Location: " . APP_BASE_URL . "mesas/formulario");
            exit;

        } catch (\Throwable $e) {
            // Errores Inesperados (ej.: fallo de conexión de la DB).
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => 'No se pudo completar el alta de mesa. Intente nuevamente. Detalles: ' . $e->getMessage()
            ]);
        }
    }

}