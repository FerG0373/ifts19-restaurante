<?php
namespace App\Models;

use App\Shared\Enums\Categoria;

class Producto {
    // ATRIBUTOS
    private ?int $id;  // PK + AI
    private string $nombre;  // UQ
    private string $descripcion;
    private float $precio;
    private int $cantidadStock;
    private Categoria $categoria;
    private bool $activo;

    // CONSTRUCTOR
    public function __construct(
        ?int $id,
        string $nombre,
        string $descripcion,
        float $precio,
        int $cantidadStock,
        Categoria $categoria,
        bool $activo = true
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->cantidadStock = $cantidadStock;
        $this->categoria = $categoria;
        $this->activo = $activo;
    }

    // GETTERS
    public function getId(): ?int { 
        return $this->id; 
    }

    public function getNombre(): string { 
        return $this->nombre; 
    }

    public function getDescripcion(): string { 
        return $this->descripcion; 
    }

    public function getPrecio(): float { 
        return $this->precio; 
    }

    public function getCantidadStock(): int { 
        return $this->cantidadStock; 
    }

    public function getCategoria(): Categoria { 
        return $this->categoria; 
    }

    public function isActivo(): bool { 
        return $this->activo; 
    }

    // SETTERS (solo para campos modificables)
    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function setDescripcion(string $descripcion): void {
        $this->descripcion = $descripcion;
    }

    public function setPrecio(float $precio): void {
        $this->precio = $precio;
    }

    public function setCantidadStock(int $cantidadStock): void {
        $this->cantidadStock = $cantidadStock;
    }

    public function setCategoria(Categoria $categoria): void {
        $this->categoria = $categoria;
    }

    public function setActivo(bool $activo): void {
        $this->activo = $activo;
    }
}