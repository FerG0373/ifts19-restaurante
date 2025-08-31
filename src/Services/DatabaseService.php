<?php
namespace App\Services;

use App\Core\DataAccess;
use Exception;


class DatabaseService {
    private DataAccess $accesoDatos;

    public function __construct() {
        try {
            $this->accesoDatos = new DataAccess();
            echo '✅ Conexión a la DB exitosa!';
        } catch (Exception $e) {
            $mensajeGenerico = '❌ Ha ocurrido un error de conexión. Intenta más tarde.';
            if ($_ENV['APP_ENV'] === 'development') {
                die('❌ Error de conexión a la DB: ' . $e->getMessage());
            } else {
                die($mensajeGenerico);
            }    
        }
    }

    public function getAccesoDatos(): DataAccess {
        return $this->accesoDatos;
    }
}
?>