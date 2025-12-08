<?php
use App\Core\Router;
use App\Core\ViewRenderer;
use App\Core\Container;  // Para obtener la instancia de DataAccess de Container.php
use App\Controllers\PersonalController;
use App\Controllers\MesaController;
use App\Controllers\AuthController;
use App\Controllers\ProductoController;
use App\Controllers\PedidoController;
use App\Controllers\FacturaController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\GuestMiddleware;



$directorioVistas = __DIR__ . '/views';

// Usamos la clase Container para recuperar la instancia única de DataAccess (Conexión a la DB).
$dataAccess = Container::getDataAccess();
// Inyectar DataAccess en el Router.
$enrutador = new Router($dataAccess);

// =================================================================
// RUTAS PÚBLICAS (SIN MIDDLEWARE)
// =================================================================
// Son las puertas de entrada para iniciar sesión, no pueden tener protección.
$enrutador->agregarRuta('login', [AuthController::class, 'mostrarFormularioLogin'], false, 'GET', [GuestMiddleware::class]);
$enrutador->agregarRuta('login/procesar', [AuthController::class, 'iniciarSesion'], false, 'POST', [GuestMiddleware::class]);

// =================================================================
// RUTAS PRIVADAS (REQUIEREN MIDDLEWARE DE AUTENTICACIÓN Y ROL)
// =================================================================
// Ruta de Logout - Requiere autenticación (cualquier usuario logueado).
$enrutador->agregarRuta('logout', [AuthController::class, 'cerrarSesion'], true, 'GET', [AuthMiddleware::class]);

// -----------------------------------------------------------------
// Rutas de Acceso Básico (Mozo y Encargado)
// -----------------------------------------------------------------
// Home - Vista estática.
$enrutador->agregarRuta('home', '1.01-home', true, 'GET', [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]);

// Rutas de Mesas.
$enrutador->agregarRuta('mesas', [MesaController::class, 'listarMesasSegunUbicacion'], true, 'GET', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]
);
$enrutador->agregarRuta('mesas/asignar-mozo', [MesaController::class, 'asignarMozo'], false, 'POST', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]
);

// RUTAS DE PEDIDOS (Acceso Básico - Mozo)
$enrutador->agregarRuta('pedido', [PedidoController::class, 'listarPedidos'], true, 'GET', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]
);
$enrutador->agregarRuta('pedido/detalle', [PedidoController::class, 'verDetalle'], false, 'POST', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]
);
$enrutador->agregarRuta('pedido/formulario', [PedidoController::class, 'mostrarFormulario'], false, 'GET', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]
);
$enrutador->agregarRuta('pedido/formulario/alta', [PedidoController::class, 'altaPedido'], false, 'POST', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]
);
$enrutador->agregarRuta('pedido/cambiar-estado', [PedidoController::class, 'cambiarEstado'], false, 'POST', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]
);

// RUTAS DE FACTURACIÓN (Acceso Básico - Mozo)
$enrutador->agregarRuta('factura/generar', [FacturaController::class, 'generarFactura'], false, 'POST', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]
);
$enrutador->agregarRuta('factura/ver', [FacturaController::class, 'verFactura'], false, 'GET', 
    [AuthMiddleware::class, [RoleMiddleware::class, 'mozo']]
);
$enrutador->agregarRuta('factura/pagar', [FacturaController::class, 'procesarPago'], false, 'POST', 
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
// Permite a CUALQUIER USUARIO logueado ver su propio detalle (Mozo y Encargado)
$enrutador->agregarRuta('personal/mi-detalle', [PersonalController::class, 'verMiDetalle'], false, 'GET', 
    [AuthMiddleware::class] // Solo necesita estar autenticado, no requiere rol específico.
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

// RUTAS DE PRODUCTO (Administración - Encargado)
$enrutador->agregarRuta('producto', [ProductoController::class, 'listarProductos'], true, 'GET',
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
);
$enrutador->agregarRuta('producto/detalle', [ProductoController::class, 'verDetalle'], false, 'POST',
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
);
$enrutador->agregarRuta('producto/formulario', [ProductoController::class, 'mostrarFormulario'], false, 'GET',
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
);
$enrutador->agregarRuta('producto/formulario/alta', [ProductoController::class, 'altaProducto'], false, 'POST',
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
);
$enrutador->agregarRuta('producto/formulario/cargar', [ProductoController::class, 'cargarFormularioEdicion'], false, 'POST',
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
);
$enrutador->agregarRuta('producto/formulario/editar', [ProductoController::class, 'editarProducto'], false, 'POST',
    [AuthMiddleware::class, [RoleMiddleware::class, 'encargado']]
);


$renderizadorVistas = new ViewRenderer($directorioVistas, $enrutador->getRutas());
$enrutador->despacharRuta($renderizadorVistas);