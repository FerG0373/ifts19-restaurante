<?php
use App\Core\Router;
use App\Core\ViewRenderer;
use App\Core\Container;  // Para obtener la instancia de DataAccess de Container.php
use App\Controllers\PersonalController;
use App\Controllers\MesaController;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;

$directorioVistas = __DIR__ . '/views';

// Usamos la clase Container para recuperar la instancia única de DataAccess (Conexión a la DB).
$dataAccess = Container::getDataAccess();
// Inyectar DataAccess en el Router.
$enrutador = new Router($dataAccess);

// Rutas estáticas.
$enrutador->agregarRuta('home', '1.01-home', true, 'GET', AuthMiddleware::class);

// ========== RUTAS DINÁMICAS CON DATOS (CONTROLLERS) ==========
// Rutas de Personal.
$enrutador->agregarRuta('personal', [PersonalController::class, 'listarPersonal'], true, 'GET', AuthMiddleware::class);  // Listado de personal (GET).
$enrutador->agregarRuta('personal/detalle', [PersonalController::class, 'verDetalle'], false, 'POST', AuthMiddleware::class);  // Detalle de personal (POST).
$enrutador->agregarRuta('personal/formulario', [PersonalController::class, 'mostrarFormulario'], false, 'GET', AuthMiddleware::class);  // GET para mostrar el formulario.
$enrutador->agregarRuta('personal/formulario/alta', [PersonalController::class, 'altaPersonal'], false, 'POST', AuthMiddleware::class);  // POST para procesar el envío de datos.
$enrutador->agregarRuta('personal/formulario/cargar', [PersonalController::class, 'cargarFormularioEdicion'], false, 'POST', AuthMiddleware::class); // POST para cargar datos.
$enrutador->agregarRuta('personal/formulario/editar', [PersonalController::class, 'editarPersonal'], false, 'POST', AuthMiddleware::class);  // POST para procesar la edición.

// Rutas de Mesas.
$enrutador->agregarRuta('mesas', [MesaController::class, 'listarMesasSegunUbicacion'], true, 'GET', AuthMiddleware::class);
$enrutador->agregarRuta('mesas/formulario', [MesaController::class, 'mostrarFormulario'], false, 'GET', AuthMiddleware::class);
$enrutador->agregarRuta('mesas/formulario/alta', [MesaController::class, 'altaMesa'], false, 'POST', AuthMiddleware::class);
$enrutador->agregarRuta('mesas/eliminar', [MesaController::class, 'bajaMesa'], false, 'POST', AuthMiddleware::class);
$enrutador->agregarRuta('mesas/asignar-mozo', [MesaController::class, 'asignarMozo'], false, 'POST', AuthMiddleware::class);

// Rutas de Autenticación - Rutas Públicas - Sin Middleware (cualquiera puede acceder).
$enrutador->agregarRuta('login', [AuthController::class, 'mostrarFormularioLogin'], true, 'GET');
$enrutador->agregarRuta('login/procesar', [AuthController::class, 'iniciarSesion'], false, 'POST');
// Ruta de Logout - Requiere autenticación.
$enrutador->agregarRuta('logout', [AuthController::class, 'cerrarSesion'], true, 'GET', AuthMiddleware::class);

$renderizadorVistas = new ViewRenderer($directorioVistas, $enrutador->getRutas());
$enrutador->despacharRuta($renderizadorVistas);