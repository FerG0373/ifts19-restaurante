<?php
namespace App\Shared\Enums;

enum EstadoFactura: string {
    case PENDIENTE = 'pendiente';
    case PAGADA = 'pagada';
    case CANCELADA = 'cancelada';
}