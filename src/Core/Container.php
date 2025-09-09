<?php
namespace App\Core;

use Exception;


class Container {
    private static ?DataAccess $accesoDatos = null;

    public static function getDataAccess(): DataAccess {
        if (self::$accesoDatos === null) {
            try {
                self::$accesoDatos = new DataAccess(
                    $_ENV['DB_HOST'],
                    $_ENV['DB_NAME'],
                    $_ENV['DB_USER'],
                    $_ENV['DB_PASS']
                );
                echo '✅ Conexión a la DB exitosa!';
            } catch (Exception $e) {
                $mensajeGenerico = '❌ Ha ocurrido un error de conexión. Intenta más tarde.';
                if ($_ENV['APP_ENV'] === 'development') {
                    die('❌ Error de conexión: ' . $e->getMessage());
                } else {
                    die($mensajeGenerico);
                }
            }
        }
        return self::$accesoDatos;
    }
}
