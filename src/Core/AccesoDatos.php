<?php
namespace App\Core;


use PDO;
use PDOException;
use Dotenv\Dotenv;

class AccesoDatos {
    private ?PDO $conexion = null;

    public function __construct() {
        // 1. Crear el objeto Dotenv (createImmutable es un método estático, por eso usamos ::)
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        // 2. Cargar las variables de entorno desde el archivo .env en la superglobal $_ENV (método de instancia ->)
        $dotenv->load();
        // 3. Tomar las variables necesarias
        $host = $_ENV['DB_HOST'];
        $db   = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        // 4. Crear el DSN (Data Source Name) para PDO
        $dsn = "mysql:host=$host; dbname=$db; charset=utf8mb4";
        // 5. Instanciar PDO y configurar atributos
        try {
            $this->conexion = new PDO($dsn, $user, $pass);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
?>