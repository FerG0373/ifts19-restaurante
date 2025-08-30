<?php
namespace App\Services;

use App\Core\AccesoDatos;
use Exception;


class DatabaseService {
    private AccesoDatos $accesoDatos;

    public function __construct() {
        try {
            $this->accesoDatos = new AccesoDatos();
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

    public function getAccesoDatos(): AccesoDatos {
        return $this->accesoDatos;
    }
}
?>