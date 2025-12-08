<?php
namespace App\Core;

use App\Services\PersonalService;
use App\Services\ProductoService;
use App\Services\PedidoService;
use App\Services\MesaService;
use App\Services\FacturaService;
use App\Repositories\PersonalRepository;
use App\Repositories\ProductoRepository;
use App\Repositories\PedidoRepository;
use App\Repositories\MesaRepository;
use App\Repositories\FacturaRepository;

class Container {
    private static ?DataAccess $dataAccess = null;
    private static array $instancias = [];

    public static function getDataAccess(): DataAccess {
        if (self::$dataAccess === null) {
            self::$dataAccess = new DataAccess();
        }
        return self::$dataAccess;
    }

    public static function getService(string $claseService) {
        if (!isset(self::$instancias[$claseService])) {
            $dependencias = [
                PersonalService::class => [PersonalRepository::class],
                ProductoService::class => [ProductoRepository::class],
                PedidoService::class => [PedidoRepository::class],
                MesaService::class => [MesaRepository::class],
                FacturaService::class => [FacturaRepository::class, PedidoRepository::class, MesaRepository::class], // 3 repositorios
            ];
            
            if (isset($dependencias[$claseService])) {
                $repositorios = [];
                foreach ($dependencias[$claseService] as $claseRepo) {
                    $repositorios[] = new $claseRepo(self::getDataAccess());
                }
                
                // Usar operador de desempaquetado para pasar múltiples repositorios
                self::$instancias[$claseService] = new $claseService(...$repositorios);
            } else {
                self::$instancias[$claseService] = new $claseService();
            }
        }
        return self::$instancias[$claseService];
    }
}