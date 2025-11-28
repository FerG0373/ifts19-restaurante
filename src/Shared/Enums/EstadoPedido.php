<?php
namespace App\Shared\Enums;

enum EstadoPedido: string {
    case PENDIENTE = 'pendiente';
    case PREPARACION = 'preparacion';
    case LISTO = 'listo';
    case ENTREGADO = 'entregado';
    case CANCELADO = 'cancelado';
}