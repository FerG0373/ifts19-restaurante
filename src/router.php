<?php
use App\Core\Router;
use App\Core\ViewRenderer;
use App\Core\Container; // Para obtener el DataAccess
use App\Controllers\PersonalController; // Importamos el controlador

$rutaBaseVistas = __DIR__ . '/views';

// 1. OBTENER LA INSTANCIA DE DATAACCESS (Conexión a la DB)
$dataAccess = Container::getDataAccess(); 

// 2. INYECTAR DataAccess en el Router
$enrutador = new Router($rutaBaseVistas, $dataAccess);

// 3. DEFINICIÓN DE RUTAS

// Rutas Estáticas
$enrutador->agregarRuta('login', '1.00-login.php', true);
$enrutador->agregarRuta('home', '1.01-home.php', true);

// Ruta de Controlador: Personal (MVC)
// Esta ruta activará la inyección de dependencias en el Router.
$enrutador->agregarRuta('personal', [PersonalController::class, 'mostrarListadoDePersonal'], true); 

$enrutador->agregarRuta('lista-personal', '2.01-personal-lista.php', false);

// 4. ARRANQUE DE LA APLICACIÓN
$renderizadorVistas = new ViewRenderer($rutaBaseVistas, $enrutador->getRutas());

// 5. EJECUTAR EL DESPACHO
$enrutador->despacharRuta($renderizadorVistas);
