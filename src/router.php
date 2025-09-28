<?php
use App\Core\Router;
use App\Core\ViewRenderer;

$rutaBaseVistas = __DIR__ . '/views';

$enrutador = new Router($rutaBaseVistas);
$enrutador->agregarRuta('login', '1.00-login.php', true);
$enrutador->agregarRuta('home', '1.01-home.php', true);
$enrutador->agregarRuta('personal', '2.00-personal.php', true);

$enrutador->agregarRuta('lista-personal', '2.01-personal-lista.php', false);

$renderizadorVistas = new ViewRenderer($rutaBaseVistas, $enrutador->getRutas());

$renderizadorVistas->renderizar();
?>