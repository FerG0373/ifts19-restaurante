<?php
namespace App\Repositories;

use App\Models\Personal;
use App\Enums\Puesto;
use App\Enums\Sexo;
use App\Core\DataAccess;
use PDO;
use PDOException;
use DateTimeImmutable;


class PersonalRepository {
    private PDO $db;

    public function __construct(DataAccess $dataAccess) {
        $this->db = $dataAccess->obtenerConexion();
    }

}
?>