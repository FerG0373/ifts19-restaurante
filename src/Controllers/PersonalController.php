<?php
namespace App\Controllers;

use App\Services\PersonalService;
use App\Core\ViewRenderer;
use App\DTOs\PersonalVistaDTO;
use App\DTOs\PersonalAltaDTO;
use InvalidArgumentException;
use RuntimeException;


class PersonalController {
    private PersonalService $personalService;
    private ViewRenderer $viewRenderer;

    public function __construct(PersonalService $personalService, ViewRenderer $viewRenderer) {
        $this->personalService = $personalService;
        $this->viewRenderer = $viewRenderer;
    }

    // GET /personal
    public function listarPersonal(): void {
        try {
            $filtro = $_GET['filtro'] ?? 'activo';
            $esActivo = ($filtro === 'activo');
            
            $listaPersonalModelos = [];  // Array de objetos Personal.
            
            if ($esActivo) {
                $listaPersonalModelos = $this->personalService->listarPersonalActivo();
                $titulo = 'Listado de Personal';
            } else {
                $listaPersonalModelos = $this->personalService->listarTodoElPersonal();
                $titulo = 'Listado de Personal (Activos e Inactivos)';
            }
            
            // Mapeo de Modelo a DTO.
            $listaDTOs = array_map(function($personalModelo) {
                return PersonalVistaDTO::fromModel($personalModelo);
            }, $listaPersonalModelos);

            // Renderizar la vista con los datos.
            $this->viewRenderer->renderizarVistaConDatos('2.00-personal', [
                'personal' => $listaDTOs,
                'titulo' => $titulo, 
                'esActivo' => $esActivo,
                'urlVerActivos' => 'personal',
                'urlVerTodos' => 'personal?filtro=todos'
            ]);

        } catch (\Exception $e) {
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    // POST /personal/detalle
    public function verDetalle(): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { 
                header('Location: personal');
                return;
            }
            
            $idPersonal = (int)$_POST['id'];

            // Llamar al Service para obtener el objeto Personal completo.
            $personalModelo = $this->personalService->mostrarDetalle($idPersonal);

            // Verificar si el personal fue encontrado.
            if (!$personalModelo) {
                throw new \Exception("El personal con ID {$idPersonal} no fue encontrado.");
            }

            // Mapeo de Modelo a DTO.
            $personalDTO = PersonalVistaDTO::fromModel($personalModelo);

            // Renderizar la vista de detalle con los datos.
            $this->viewRenderer->renderizarVistaConDatos('2.01-personal-detalle', [
                'personal' => $personalDTO,
                'titulo' => 'Detalle de ' . $personalDTO->apellido . ', ' . $personalDTO->nombre
            ]);

        } catch (\Exception $e) {
            // Manejo de errores (ej: ID no encontrado, error de DB)
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error al Cargar Detalle',
                'mensaje' => $e->getMessage()
            ]);

            return;
        }
    }

    // POST /personal/formulario/alta
    public function altaPersonal(): void {        
        $datos = $_POST;  // Capturamos los datos del formulario.

        try {            
            $altaDTO = PersonalAltaDTO::fromArray($datos);  // Mapeo: crear el DTO y validar campos obligatorios/formato.            
            $modeloPersonal = $altaDTO->toPersonalModel();  // Conversión: DTO a Modelo de Dominio (Personal).
            $nuevoPersonal = $this->personalService->agregarPersonal($modeloPersonal);  // Service: Lógica de negocio (hasheo, unicidad) y Persistencia.
           
            header('Location: ' . APP_BASE_URL . 'personal');  // Éxito: Redirigir a la lista del personal.
            exit;

        } catch (InvalidArgumentException $e) {            
            $this->mostrarErrorDeAlta("Error de datos: " . $e->getMessage(), $datos);  // Errores de Formato/Mapeo (vienen del DTO o toPersonalModel).
            
        } catch (RuntimeException $e) {            
            $this->mostrarErrorDeAlta("Error de negocio: " . $e->getMessage(), $datos);  // Errores de Negocio (vienen del Service, ej.: DNI duplicado, Email duplicado).
            
        } catch (\Throwable $e) {
            // Errores Inesperados (ej.: Error de DB en el Repository)
            $this->viewRenderer->renderizarVistaConDatos('9.01-error', [ 
                'titulo' => 'Error de Sistema',
                'mensaje' => 'No se pudo completar el alta. Intente nuevamente. Detalles: ' . $e->getMessage()
            ]);
        }
    }


    public function mostrarFormulario(): void {
        $this->viewRenderer->renderizarVistaConDatos('2.02-personal-formulario', ['titulo' => 'Alta de Nuevo Personal']);
    }

    // Método auxiliar para renderizar el formulario de alta con errores y datos precargados.
    private function mostrarErrorDeAlta(string $mensajeError, array $datosPrecargados): void {
        $this->viewRenderer->renderizarVistaConDatos('2.02-personal-formulario', [
            'titulo' => 'Alta de Personal (Error)',
            'error' => $mensajeError,
            'datos' => $datosPrecargados  // Pasamos los datos que el usuario ingresó para precargar el formulario.
        ]);
    }
}
?>