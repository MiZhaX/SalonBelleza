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
                (float)$fila['precio'],
                (int)$fila['duracion_minutos'],
                (int)$fila['id_especialidad']
            );
        }

        return $servicios;
    }

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

    public function crear(Servicio $servicio): bool
    {
        $query = $this->conexion->prepare("INSERT INTO servicios (nombre, precio, duracion_minutos, id_especialidad) VALUES (:nombre, :precio, :duracion_minutos, :id_especialidad)");
        $query->bindValue(':nombre', $servicio->getNombre(), PDO::PARAM_STR);
        $query->bindValue(':precio', $servicio->getPrecio(), PDO::PARAM_STR);
        $query->bindValue(':duracion_minutos', $servicio->getDuracionMinutos(), PDO::PARAM_INT);
        $query->bindValue(':id_especialidad', $servicio->getIdEspecialidad(), PDO::PARAM_INT);

        return $query->execute();
    }

    public function actualizar(Servicio $servicio): bool
    {
        $query = $this->conexion->prepare("UPDATE servicios SET nombre = :nombre, precio = :precio, duracion_minutos = :duracion_minutos, id_especialidad = :id_especialidad WHERE id = :id");
        $query->bindValue(':id', $servicio->getId(), PDO::PARAM_INT);
        $query->bindValue(':nombre', $servicio->getNombre(), PDO::PARAM_STR);
        $query->bindValue(':precio', $servicio->getPrecio(), PDO::PARAM_STR);
        $query->bindValue(':duracion_minutos', $servicio->getDuracionMinutos(), PDO::PARAM_INT);
        $query->bindValue(':id_especialidad', $servicio->getIdEspecialidad(), PDO::PARAM_INT);

        return $query->execute();
    }

    public function eliminar(int $id): bool
    {
        $query = $this->conexion->prepare("DELETE FROM servicios WHERE id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        return $query->execute();
    }
}
