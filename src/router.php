<?php
use App\Core\Router;
use App\Core\ViewRenderer;
use App\Core\Container;  // Para obtener la instancia de DataAccess de Container.php
use App\Controllers\PersonalController;
use App\Controllers\MesaController;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;


$directorioVistas = __DIR__ . '/views';

// Usamos la clase Container para recuperar la instancia única de DataAccess (Conexión a la DB).
$dataAccess = Container::getDataAccess();
// Inyectar DataAccess en el Router.
$enrutador = new Router($dataAccess);

// =================================================================
// RUTAS PÚBLICAS (SIN MIDDLEWARE)
// =================================================================
// Son las puertas de entrada para iniciar sesión, no pueden tener protección.
$enrutador->agregarRuta('login', [AuthController::class, 'mostrarFormularioLogin'], true, 'GET');
$enrutador->agregarRuta('login/procesar', [AuthController::class, 'iniciarSesion'], false, 'POST');

// =================================================================
// RUTAS PRIVADAS (REQUIEREN MIDDLEWARE DE AUTENTICACIÓN Y ROL)
// =================================================================
// Ruta de Logout - Requiere autenticación (cualquier usuario logueado).
$enrutador->agregarRuta('logout', [AuthController::class, 'cerrarSesion'], true, 'GET', 
    [AuthMiddleware::class]
);

// -----------------------------------------------------------------
// Rutas de Acceso Básico (Mozo y Encargado)
// -----------------------------------------------------------------
// Home - Vista estática.
$enrutador->agregarRuta('home', '1.01-home', true, 'GET', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]
);

// Rutas de Mesas.
$enrutador->agregarRuta('mesas', [MesaController::class, 'listarMesasSegunUbicacion'], true, 'GET', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]
);

$enrutador->agregarRuta('mesas/asignar-mozo', [MesaController::class, 'asignarMozo'], false, 'POST', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]
);

// -----------------------------------------------------------------
// Rutas Administrativas (Solo Encargado)
// -----------------------------------------------------------------
// Rutas de Personal (Listado, Detalle y Formularios).
$enrutador->agregarRuta('personal', [PersonalController::class, 'listarPersonal'], true, 'GET', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
); 
$enrutador->agregarRuta('personal/detalle', [PersonalController::class, 'verDetalle'], false, 'POST', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
); 
$enrutador->agregarRuta('personal/formulario', [PersonalController::class, 'mostrarFormulario'], false, 'GET', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
);
$enrutador->agregarRuta('personal/formulario/alta', [PersonalController::class, 'altaPersonal'], false, 'POST', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
);
$enrutador->agregarRuta('personal/formulario/cargar', [PersonalController::class, 'cargarFormularioEdicion'], false, 'POST', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
); 
$enrutador->agregarRuta('personal/formulario/editar', [PersonalController::class, 'editarPersonal'], false, 'POST', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
); 

// Rutas de Mesas (Gestión/Administración).
$enrutador->agregarRuta('mesas/formulario', [MesaController::class, 'mostrarFormulario'], false, 'GET', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
);
$enrutador->agregarRuta('mesas/formulario/alta', [MesaController::class, 'altaMesa'], false, 'POST', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
);
$enrutador->agregarRuta('mesas/eliminar', [MesaController::class, 'bajaMesa'], false, 'POST', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
);


$renderizadorVistas = new ViewRenderer($directorioVistas, $enrutador->getRutas());
$enrutador->despacharRuta($renderizadorVistas);