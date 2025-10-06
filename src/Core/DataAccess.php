<?php
namespace App\Core;

use PDO;


class DataAccess {
    private PDO $conexion;
    
    public function __construct(string $host, string $db, string $user, string $pass) {
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $this->conexion = new PDO($dsn, $user, $pass);
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function getDataAccess(): PDO {
        return $this->conexion;
    }
}
?>