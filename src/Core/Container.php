<?php
namespace App\Core;

use Exception;
use App\Services\PersonalService;
use App\Services\MesaService;
use App\Repositories\PersonalRepository;
use App\Repositories\MesaRepository;

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

    //  * Método centralizado para obtener instancias de Services de forma dinámica y aplica el patrón Singleton.
    //  * Gestiona automáticamente las dependencias.
    public static function getService(string $claseService) {
        if (!isset(self::$instancias[$claseService])) {
            // Mapeo de dependencias
            $dependencias = [
                // "CLASE A INSTANCIAR" => ["DEPENDENCIAS QUE NECESITA"]
                PersonalService::class => [PersonalRepository::class],
                MesaService::class => [MesaRepository::class],
                // Agregar acá otros services:
            ];
            // Verifica si la clase Service en cuestión existe en el array de dependencias.
            if (isset($dependencias[$claseService])) {
                // Accede al array y de ese Service obtiene el primer elemento (el repository).
                $claseRepository = $dependencias[$claseService][0];
                $repository = new $claseRepository(self::getDataAccess());  // Instancia de ese Repository dinámico.
                self::$instancias[$claseService] = new $claseService($repository);  // Ahora puedo instanciar el Service e inyectarle su Repository.
            } else {
                self::$instancias[$claseService] = new $claseService();  // Clases que se pueden crear sin dependencias.
            }
        }
        return self::$instancias[$claseService];
    }
}
