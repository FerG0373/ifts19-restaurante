<?php
namespace App\Shared\Enums;

enum PerfilAcceso: string {
    case ADMIN = 'admin';
    case ENCARGADO = 'encargado';
    case MOZO = 'mozo';
}
?>