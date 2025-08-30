<?php
use App\Core\AccesoDatos;

// Crea una instancia de la conexión a la base de datos
try {
    $db = new AccesoDatos();
    echo "Conexión a la DB exitosa!<br>";
} catch (\Exception $e) {
    die("Error de inicialización de la DB: " . $e->getMessage());
}