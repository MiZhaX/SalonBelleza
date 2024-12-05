<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Especialidad;
use PDO;

class EspecialidadRepository
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new BaseDatos();
    }

    public function obtenerTodas(): array
    {
        $sql = "SELECT * FROM especialidades";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
