<?php
require_once __DIR__ . '/../vendor/autoload.php';  // Carga las dependencias con Composer.
require_once __DIR__ . '/../src/boot.php';  // Carga la configuración y servicios


/* FUNCIÓN MAIN */
function main () {
    require_once __DIR__ . '/../src/router.php';
}

main();
?>