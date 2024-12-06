<?php

namespace Services;

use Repositories\ServicioRepository;
use Models\Servicio;

class ServicioService
{
    private ServicioRepository $servicioRepository;

    public function __construct()
    {
        $this->servicioRepository = new ServicioRepository();
    }

    public function obtenerTodos(): array
    {
        return $this->servicioRepository->obtenerTodos();
    }

    public function obtenerPorId(int $id): ?Servicio
    {
        return $this->servicioRepository->obtenerPorId($id);
    }

    public function crearServicio(array $datos): bool
    {
        $servicio = new Servicio(
            id: null,
            nombre: $datos['nombre'],
            precio: (float)$datos['precio'],
            duracionMinutos: (int)$datos['duracion_minutos'],
            idEspecialidad: (int)$datos['id_especialidad']
        );

        return $this->servicioRepository->crear($servicio);
    }

    public function actualizarServicio(Servicio $servicio): bool
    {
        return $this->servicioRepository->actualizar($servicio);
    }

    public function eliminarServicio(int $id): bool
    {
        return $this->servicioRepository->eliminar($id);
    }
}
