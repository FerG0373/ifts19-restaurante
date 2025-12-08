<?php
namespace App\Models;

class PedidoDetalle {
    private ?int $id;
    private ?int $pedidoId;           // ← Cambiado a ?int
    private int $productoId;
    private string $nombreProducto;
    private int $cantidad;
    private float $precioUnitario;
    private ?string $instruccionesPreparacion;

    public function __construct(
        ?int $id = null,
        ?int $pedidoId = null,
        ?int $productoId = null,
        ?string $nombreProducto = '',
        ?int $cantidad = 0,
        ?float $precioUnitario = 0.0,
        ?string $instruccionesPreparacion = null
    ) {
        $this->id = $id;
        $this->pedidoId = $pedidoId;
        $this->productoId = $productoId ?? 0;      // Valor por defecto si es null
        $this->nombreProducto = $nombreProducto ?? '';
        $this->cantidad = $cantidad ?? 0;
        $this->precioUnitario = $precioUnitario ?? 0.0;
        $this->instruccionesPreparacion = $instruccionesPreparacion;
    }

    // GETTERS
    public function getId(): ?int {
        return $this->id;
    }

    public function getPedidoId(): ?int {        // ← Cambiado a ?int
        return $this->pedidoId;
    }

    public function getProductoId(): int {
        return $this->productoId;
    }

    public function getNombreProducto(): string {
        return $this->nombreProducto;
    }

    public function getCantidad(): int {
        return $this->cantidad;
    }

    public function getPrecioUnitario(): float {
        return $this->precioUnitario;
    }

    public function getInstruccionesPreparacion(): ?string {
        return $this->instruccionesPreparacion;
    }

    public function getSubtotal(): float {
        return $this->cantidad * $this->precioUnitario;
    }

    // SETTERS
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setPedidoId(?int $pedidoId): void {   // ← Cambiado a ?int
        $this->pedidoId = $pedidoId;
    }

    public function setProductoId(int $productoId): void {
        $this->productoId = $productoId;
    }

    public function setNombreProducto(string $nombreProducto): void {
        $this->nombreProducto = $nombreProducto;
    }

    public function setCantidad(int $cantidad): void {
        $this->cantidad = $cantidad;
    }

    public function setPrecioUnitario(float $precioUnitario): void {
        $this->precioUnitario = $precioUnitario;
    }

    public function setInstruccionesPreparacion(?string $instrucciones): void {
        $this->instruccionesPreparacion = $instrucciones;
    }
}