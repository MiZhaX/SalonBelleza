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

    // Obtener todas las especialidades
    public function obtenerTodas(): array
    {
        $sql = "SELECT * FROM especialidades";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una especialidad por su Id
    public function obtenerPorId(int $id): ?Especialidad
    {
        $query = $this->conexion->prepare("SELECT * FROM especialidades WHERE id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $fila = $query->fetch(PDO::FETCH_ASSOC);

        if ($fila) {
            return new Especialidad(
                $fila['id'],
                $fila['nombre'],
            );
        }

        return null;
    }
}
