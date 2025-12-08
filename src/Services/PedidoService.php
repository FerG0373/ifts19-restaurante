<?php
namespace App\Services;

use App\Models\Pedido;
use App\Repositories\PedidoRepository;

class PedidoService {
    private PedidoRepository $pedidoRepository;

    public function __construct(PedidoRepository $pedidoRepository) {
        $this->pedidoRepository = $pedidoRepository;
    }

    public function listarTodosLosPedidos(): array {
        return $this->pedidoRepository->selectAll();
    }

    public function listarPedidosPorEstado(string $estado): array {
        return $this->pedidoRepository->selectByEstado($estado);
    }

    public function mostrarDetalle(int $id): ?Pedido {
        return $this->pedidoRepository->selectById($id);
    }

    public function obtenerMesasDisponibles(): array {
        return $this->pedidoRepository->getMesasDisponibles();
    }

    public function obtenerInfoProductos(array $productosIds): array {
        return $this->pedidoRepository->getProductosInfo($productosIds);
    }

    public function agregarPedido(Pedido $pedido): Pedido {
        return $this->pedidoRepository->insert($pedido);
    }

    public function cambiarEstado(int $idPedido, string $nuevoEstado): void {
        $this->pedidoRepository->updateEstado($idPedido, $nuevoEstado);
    }
}