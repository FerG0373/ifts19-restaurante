<?php
use App\Core\Router;
use App\Core\ViewRenderer;
use App\Core\Container;  // Para obtener la instancia de DataAccess de Container.php
use App\Controllers\PersonalController; // Importamos el controlador

$directorioVistas = __DIR__ . '/views';

// Usamos la clase Container para recuperar la instancia única de DataAccess (Conexión a la DB).
$dataAccess = Container::getDataAccess();
// Inyectar DataAccess en el Router.
$enrutador = new Router($dataAccess);
// Rutas estáticas.
$enrutador->agregarRuta('login', '1.00-login.php', true);
$enrutador->agregarRuta('home', '1.01-home.php', true);
$enrutador->agregarRuta('lista-personal', '2.01-personal-lista.php', false);
// Rutas dinámicas con datos (Controladores).
$enrutador->agregarRuta('personal', [PersonalController::class, 'listarPersonal'], true);  // Listado de personal (GET).
$enrutador->agregarRuta('personal/detalle', [PersonalController::class, 'verDetalle'], false, 'POST');  // Detalle de personal (POST).
$enrutador->agregarRuta('personal/alta', [PersonalController::class, 'altaPersonal'], false, 'POST');  // Formulario de alta de personal (POST).

$renderizadorVistas = new ViewRenderer($directorioVistas, $enrutador->getRutas());
$enrutador->despacharRuta($renderizadorVistas);
