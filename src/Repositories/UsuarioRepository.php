<?php
namespace App\Repositories;

use App\Core\DataAccess;
use App\Models\Usuario;
use App\Enums\PerfilAcceso;
use PDO;
use PDOException;

class UsuarioRepository {
    private PDO $db;

    public function __construct(DataAccess $dataAccess) {
        $this->db = $dataAccess->getConexion();
    }

}
