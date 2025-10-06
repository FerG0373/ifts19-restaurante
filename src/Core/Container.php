<?php
namespace App\Core;

use Exception;
use App\Services\PersonalService;
use App\Repositories\PersonalRepository;

// Contenedor para gestionar la única instancia de DataAccess (Patrón de diseño Singleton).
class Container {
    private static ?DataAccess $accesoDatos = null;
    private static array $instancias = [];

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

    public static function getService(string $claseService) {
        if (!isset(self::$instancias[$claseService])) {
            // Mapeo de dependencias
            $dependencias = [
                PersonalService::class => [PersonalRepository::class],
                // Agregar acá otros services:
            ];

            if (isset($dependencias[$claseService])) {
                $claseRepository = $dependencias[$claseService][0];
                $repository = new $claseRepository(self::getDataAccess());
                self::$instancias[$claseService] = new $claseService($repository);
            } else {
                self::$instancias[$claseService] = new $claseService();
            }
        }
        return self::$instancias[$claseService];
    }
}
