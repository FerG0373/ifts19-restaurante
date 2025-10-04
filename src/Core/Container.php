<?php
namespace App\Core;

use Exception;

// Contenedor para gestionar la única instancia de DataAccess (Patrón de diseño Singleton).
class Container {
    private static ?DataAccess $accesoDatos = null;

    // Método estático para obtener la única instancia de DataAccess.
    public static function getDataAccess(): DataAccess {
        if (self::$accesoDatos === null) {
            try {
                self::$accesoDatos = new DataAccess(
                    $_ENV['DB_HOST'],
                    $_ENV['DB_NAME'],
                    $_ENV['DB_USER'],
                    $_ENV['DB_PASS']
                );
                //echo '✅ Conexión a la DB exitosa!';
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
