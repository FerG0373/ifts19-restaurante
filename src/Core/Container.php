<?php
namespace App\Core;

use App\Services\PersonalService;
use App\Services\MesaService;
use App\Services\AuthService;
use App\Services\ProductoService;
use App\Services\PedidoService;
use App\Services\FacturaService;
use App\Repositories\PersonalRepository;
use App\Repositories\MesaRepository;
use App\Repositories\UsuarioRepository;
use App\Repositories\ProductoRepository;
use App\Repositories\PedidoRepository;
use App\Repositories\FacturaRepository;
use Exception;

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
    
    /**
     * Método centralizado para obtener instancias de Services (Singleton) con inyección de dependencias.
     * Si la instancia del Service ya existe, la retorna (patrón Singleton).
     * Si no existe, la crea, inyectando automáticamente sus Repositorios dependientes.
     * $claseService El nombre de la clase Service a instanciar (ej: AuthService::class).
     * Retorna La única instancia del Service solicitado.
     */
    public static function getService(string $claseService): object {        
        // Verifica si la instancia ya existe (Singleton Pattern)
        if (!isset(self::$instancias[$claseService])) {            
            // Mapeo de dependencias de Services a Repositorios.
            $dependencias = [
                // 'CLASE SERVICE' => ['DEPENDENCIAS REQUERIDAS']
                PersonalService::class => [PersonalRepository::class],
                MesaService::class => [MesaRepository::class],
                AuthService::class => [UsuarioRepository::class, PersonalRepository::class],
                ProductoService::class => [ProductoRepository::class],
                PedidoService::class => [PedidoRepository::class],
                FacturaService::class => [FacturaRepository::class, PedidoRepository::class, MesaRepository::class], // 3 repositorios
                // Agregar acá otros services y sus dependencias (Repositories).
            ];            
            $argumentos = [];
            // Verifica si el Service requiere dependencias (Repositorios).
            if (isset($dependencias[$claseService])) {                
                // Instanciar todas las dependencias requeridas (Repositorios)
                foreach ($dependencias[$claseService] as $claseRepository) {                    
                    // Instanciamos cada Repository y le inyectamos la DataAccess (conexión a la DB)
                    $argumentos[] = new $claseRepository(self::getDataAccess());
                }
            }            
            // Instancia y almacena el Service.
            // Usamos el operador `...` (spread operator) para desempaquetar el array $argumentos y pasarlos como argumentos posicionales al constructor del Service.
            self::$instancias[$claseService] = new $claseService(...$argumentos);
            
        }        
        // Retorna la instancia única (existente o recién creada)
        return self::$instancias[$claseService];
    }
}
