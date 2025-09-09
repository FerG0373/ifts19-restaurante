<?php
use App\Core\Router;
use App\Core\ViewRenderer;

$rutaBaseVistas = __DIR__ . '/views';

$enrutador = new Router($rutaBaseVistas);
$enrutador->agregarRuta('login', '1.00-login.php');
$enrutador->agregarRuta('home', '1.01-home.php');

$renderizadorVistas = new ViewRenderer($rutaBaseVistas, $enrutador->getRutas());

$renderizadorVistas->renderizar();
?>