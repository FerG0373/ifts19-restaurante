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
use App\Core\Container; // <-- ASEGÚRATE DE AÑADIR ESTE USE
use App\Services\PersonalService; // <-- AÑADIR ESTE USE
use App\Shared\Enums\Puesto; // <--- NUEVO: Necesario para filtrar solo mozos


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
            
            // OBTIENE EL SERVICE DE PERSONAL Y LOS MOZOS            
            $personalService = Container::getService(PersonalService::class);            
            $mozos = $personalService->listarPersonalActivo(); 
            
            // Filtrar solo Mozos (asumiendo que Puesto::Mozo existe y tiene valor 'mozo')
            $mozosParaAsignacion = array_filter($mozos, function($personal) {
                return $personal->getPuesto()->value === 'mozo'; 
            });
            
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
                'exito' => $exito,
                'mozos' => $mozosParaAsignacion, //  <-- PASA LOS MOZOS FILTRADOS A LA VISTA.
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
    
    // POST /mesas/formulario
    public function altaMesa(): void {
        $datos = $_POST;

        try {            
            $dto = MesaAltaDTO::fromArray($datos);  // DTO: Construir, mapear y validar los datos de $_POST.
            $mesaModel = MesaMapper::fromDtoAlta($dto);  // MAPPER: Conversión de DTO a Modelo de Dominio (Mesa).
            $mesaModel = $this->mesaService->agregarMesa($mesaModel);  // SERVICE: Lógica de negocio (chequeos extra) y Persistencia (Repository).

            $_SESSION['msj_exito'] = "Mesa N° {$mesaModel->getNroMesa()} creada con éxito en " . strtoupper($mesaModel->getUbicacion()->value) . ".";            
            $ubicacionRedirigir = strtolower($mesaModel->getUbicacion()->value);
            header("Location: " . APP_BASE_URL . "mesas?ubicacion={$ubicacionRedirigir}");  // Redirige al tablero de mesas, filtrando por la ubicación de la mesa recién creada.
            exit;

        } catch (InvalidArgumentException $e) {
            // Errores de Formato/Validación (vienen del DTO o el Mapper, ej: capacidad no numérica, ubicación inválida).
            $_SESSION['error_form'] = "Error de datos: " . $e->getMessage();
            $_SESSION['data_form'] = $datos;            
            // Redirige al método GET para mostrar el formulario con errores.
            header("Location: " . APP_BASE_URL . "mesas/formulario"); 
            exit;

        } catch (RuntimeException $e) { 
            // Errores de Negocio (vienen del Service/Repository, ej: numero_mesa duplicado, error de BD específico).
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

    // POST /mesas/eliminar/{id}
    public function bajaMesa(): void {        
        // El ID viene ahora del campo oculto del formulario ($_POST['id'])
        $id = (int)$_POST['id'] ?? 0;         
        if ($id <= 0) {
            // Redirigir al tablero con error si no se encuentra el ID.
            $_SESSION['error_form'] = "Error: ID de mesa no recibido.";
            header("Location: " . APP_BASE_URL . "mesas");
            exit;
        }        
        try {
            // Llama al Service con el ID obtenido del POST.
            $mesaDesactivada = $this->mesaService->eliminarMesa($id);
            // Redirección con éxito.
            $_SESSION['msj_exito'] = "La Mesa N° {$mesaDesactivada->getNroMesa()} ha sido dada de baja (Inactiva).";            
            // Redirección.
            header("Location: " . APP_BASE_URL . "mesas");
            exit;

        } catch (\RuntimeException $e) {
            // Error si la mesa no existe, o si hay un error de negocio (ej: mesa ocupada).
            $_SESSION['error_form'] = "Error al intentar dar de baja la mesa: " . $e->getMessage();
            header("Location: " . APP_BASE_URL . "mesas");
            exit;
            
        } catch (\Throwable $e) {
            // Error de sistema (DB, etc.)
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => 'Error fatal al procesar la baja de mesa: ' . $e->getMessage()
            ]);
        }
    }


    public function asignarMozo(): void {        
        // Los IDs vienen del formulario POST.
        $idMesa = (int)$_POST['id_mesa'] ?? 0;
        $idPersonal = (int)$_POST['id_personal'] ?? 0;

        if ($idMesa <= 0 || $idPersonal <= 0) {
            $_SESSION['error_form'] = "Error de asignación: Faltan datos de mesa o mozo.";
            header("Location: " . APP_BASE_URL . "mesas");
            exit;
        }
        
        try {
            // Llama al Service para que registre la asignación en la DB.
            $this->mesaService->asignarMozo($idMesa, $idPersonal); 

            $_SESSION['msj_exito'] = "Mesa asignada con éxito al mozo (ID: {$idPersonal}).";
            
        } catch (\Exception $e) {
            $_SESSION['error_form'] = "Error al asignar mozo: " . $e->getMessage();
        }
        
        header("Location: " . APP_BASE_URL . "mesas");
        exit;
    }
}