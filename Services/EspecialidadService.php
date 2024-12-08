<?php

namespace Services;

use Models\Especialidad;
use Repositories\EspecialidadRepository;

class EspecialidadService
{

    private EspecialidadRepository $especialidadRepository;

    public function __construct()
    {
        $this->especialidadRepository = new EspecialidadRepository();
    }

    // Obtener todas las especialidades
    public function obtenerTodas(): array
    {
        return $this->especialidadRepository->obtenerTodas();
    }

    // Obtener especialidades por su Id
    public function obtenerPorId(int $id): ?Especialidad
    {
        return $this->especialidadRepository->obtenerPorId($id);
    }
}
