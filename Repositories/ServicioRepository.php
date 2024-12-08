<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Servicio;
use PDO;

class ServicioRepository
{
    private BaseDatos $conexion;

    public function __construct()
    {
        $this->conexion = new BaseDatos();
    }

    // Obtener todos los servicios
    public function obtenerTodos(): array
    {
        $query = $this->conexion->prepare("SELECT * FROM servicios");
        $query->execute();
        $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

        $servicios = [];
        foreach ($resultados as $fila) {
            $servicios[] = new Servicio(
                $fila['id'],
                $fila['nombre'],
                $fila['precio'],
                $fila['duracion_minutos'],
                $fila['id_especialidad']
            );
        }

        return $servicios;
    }

    // Obtener un servicio por el id
    public function obtenerPorId(int $id): ?Servicio
    {
        $query = $this->conexion->prepare("SELECT * FROM servicios WHERE id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $fila = $query->fetch(PDO::FETCH_ASSOC);

        if ($fila) {
            return new Servicio(
                $fila['id'],
                $fila['nombre'],
                (float)$fila['precio'],
                (int)$fila['duracion_minutos'],
                (int)$fila['id_especialidad']
            );
        }

        return null;
    }
}
