<?php
namespace App\Enums;

enum Puesto: string {
    case ENCARGADO = 'encargado';
    case COCINERO = 'cocinero';
    case MOZO = 'mozo';
    case CAJERO = 'cajero';
    case BARTENDER = 'bartender';
}
?>