<?php
namespace App\Controllers;

use App\Services\PersonalService;
use App\Core\ViewRenderer;
use App\DTOs\PersonalAltaDTO;  // <-- Incluimos el DTO
use InvalidArgumentException;  // Excepciones del DTO/Mapeo
use RuntimeException;  // Excepciones del Service/Negocio


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
            // Determinar el filtro a partir del parámetro GET.
            $filtro = $_GET['filtro'] ?? 'activo';  // Por defecto, solo se ve el personal activo.
            $esActivo = ($filtro === 'activo');
            
            $listaPersonal = [];
            // Ejecutar lógica de negocio basada en el filtro.
            if ($esActivo) {
                $listaPersonal = $this->personalService->listarPersonalActivo();  // Llama al método que lista al personal activo (comportamiento por defecto).
                $titulo = 'Listado de Personal';                
            } else {               
                $listaPersonal = $this->personalService->listarTodoElPersonal();  // Llama al método que lista a TODO el personal (activos e inactivos).
                $titulo = 'Listado de Personal (Activos e Inactivos)';
            }
            // Renderizar la vista con los datos.
            $this->viewRenderer->renderizarVistaConDatos('2.00-personal', [
                'personal' => $listaPersonal,
                'titulo' => $titulo, // Usamos el título dinámico para reflejar el filtro.
                'esActivo' => $esActivo,
                'urlVerActivos' => 'personal',  // URL por defecto, sin parámetros
                'urlVerTodos' => 'personal?filtro=todos'  // URL con parámetro para ver a todos (incluye inactivos).
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
            // Validar y obtener el ID desde $_POST. Si no es un POST o falta el ID, redirigir a la lista o mostrar un error.
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {                
                // Para simplificar, redirigiremos.
                header('Location: personal');
                return;
            }
            // El ID viene del campo oculto del formulario de la tabla.
            $idPersonal = (int)$_POST['id'];

            // Llamar al Service para obtener el objeto Personal completo
            $personal = $this->personalService->mostrarDetalle($idPersonal);
            
            // Verificar si el personal fue encontrado.
            if (!$personal) {
                throw new \Exception("El personal con ID {$idPersonal} no fue encontrado.");
            }

            // Renderizar la vista de detalle con los datos
            $this->viewRenderer->renderizarVistaConDatos('2.01-personal-detalle', [
                'personal' => $personal,  // Pasamos el objeto completo.
                'titulo' => 'Detalle de ' . $personal->getNombre() . ' ' . $personal->getApellido()
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

    // POST /personal/alta
    public function altaPersonal(): void {        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->viewRenderer->renderizarVistaConDatos('2.02-personal-formulario', ['titulo' => 'Alta de Nuevo Personal']);
            return;
        }

        $datos = $_POST;  // Capturamos los datos del formulario.

        try {            
            $altaDTO = PersonalAltaDTO::fromArray($datos);  // Mapeo: crear el DTO y validar campos obligatorios/formato.            
            $modeloPersonal = $altaDTO->toPersonalModel();  // Conversión: DTO a Modelo de Dominio (Personal).
            $nuevoPersonal = $this->personalService->agregarPersonal($modeloPersonal);  // Service: Lógica de negocio (hasheo, unicidad) y Persistencia.
            
            // Es la forma más limpia de evitar el doble POST (Post/Redirect/Get pattern)
            header('Location: personal/detalle/' . $nuevoPersonal->getId());  // Éxito: Redirigir a la lista del personal.
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