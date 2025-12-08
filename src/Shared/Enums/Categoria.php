<?php
namespace App\Shared\Enums;

enum Categoria: string {
    case ENTRADA = 'entrada';
    case PRINCIPAL = 'principal';
    case GUARNICION = 'guarnicion';
    case BEBIDA = 'bebida';
    case POSTRE = 'postre';
    case CAFETERIA = "cafeteria";
}