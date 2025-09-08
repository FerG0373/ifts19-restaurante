<?php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');  // Instancia el objeto Dotenv (createImmutable es un método estático, por eso usamos ::)
$dotenv->load();  // Carga las variables de entorno desde el archivo .env en la superglobal $_ENV (método de instancia ->)

?>