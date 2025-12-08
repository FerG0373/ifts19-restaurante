<?php
namespace App\Shared\Enums;

enum MetodoPago: string {
    case EFECTIVO = 'efectivo';
    case TARJETA_DEBITO = 'tarjeta_debito';
    case TARJETA_CREDITO = 'tarjeta_credito';
    case TRANSFERENCIA = 'transferencia';
    case MERCADO_PAGO = 'mercado_pago';
}