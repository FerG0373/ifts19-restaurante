<?php
namespace App\Shared\Enums;


enum EstadoMesa: string
{
    case LIBRE = 'libre';
    case OCUPADA = 'ocupada';
    case RESERVADA = 'reservada';
    case INHABILITADA = 'inhabilitada';
}