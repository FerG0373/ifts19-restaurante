<?php
namespace App\DTOs;

use InvalidArgumentException;

class FacturaPagoDTO {
    public int $facturaId;
    public string $metodoPago;

    public static function fromArray(array $datosInput): self {
        // Validar campos obligatorios
        if (empty($datosInput['factura_id'])) {
            throw new InvalidArgumentException("El ID de la factura es obligatorio.");
        }

        if (empty($datosInput['metodo_pago'])) {
            throw new InvalidArgumentException("Debe seleccionar un método de pago.");
        }

        // Validar que el método de pago sea válido
        $metodosValidos = ['efectivo', 'tarjeta_debito', 'tarjeta_credito', 'transferencia', 'mercado_pago'];
        if (!in_array($datosInput['metodo_pago'], $metodosValidos)) {
            throw new InvalidArgumentException("El método de pago seleccionado no es válido.");
        }

        $dto = new self();
        $dto->facturaId = (int)$datosInput['factura_id'];
        $dto->metodoPago = $datosInput['metodo_pago'];

        return $dto;
    }
}