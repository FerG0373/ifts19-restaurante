<?php
// Carga las dependencias con Composer
require_once __DIR__ . '/../vendor/autoload.php';
// Carga la configuración y servicios
require_once __DIR__ . '/../src/boot.php';


/* FUNCIÓN MAIN */
function main () {
    require_once __DIR__ . '/../src/router.php';
}

main();
?>