<?php
namespace App\Shared\Enums;

enum TipoPedido: string {
    case MESA = 'mesa';
    case DOMICILIO = 'domicilio';
    case LLEVAR = 'llevar';
}