<?php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');  // Instancia el objeto Dotenv (createImmutable es un método estático, por eso usamos ::)
$dotenv->load();  // Carga las variables de entorno desde el archivo .env en la superglobal $_ENV (método de instancia ->)

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// VALIDACIÓN
$variablesRequeridas = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME'];
$variablesFaltantes = [];

foreach ($variablesRequeridas as $variable) {
    if (empty($_ENV[$variable])) {
        $variablesFaltantes[] = $variable;
    }
}

if (!empty($variablesFaltantes)) {
    throw new Exception(
        "Faltan variables de entorno: " . implode(', ', $variablesFaltantes) . 
        ". Por favor, verifica el archivo .env"
    );
}


if (!defined('APP_BASE_URL')) {
    define('APP_BASE_URL', $_ENV['APP_BASE_URL'] ?? '/');  // Define la constante APP_BASE_URL con el valor de la variable de entorno o '/' si no está definida.
}
?>