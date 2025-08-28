<?php
$vista_actual = $_GET['url'] ?? 'home';

if ($vista_actual === '') {
    $vista_actual = 'home';
}

$ruta_base = __DIR__ . '/views';

$rutas = [
    'login' => $ruta_base . '/1.00-login.php',
    'home' => $ruta_base . '/1.01-home.php',
];

// Verifica si la vista actual existe en el array de rutas.
if (!isset($rutas[$vista_actual])) {
    require_once $ruta_base . '/9.00-notfound.php';
    exit();
}
$contenido_principal = $rutas[$vista_actual];

require_once $ruta_base . '/0.00-layout.php';
?>