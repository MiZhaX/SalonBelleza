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

    public function obtenerTodas(): array
    {
        return $this->especialidadRepository->obtenerTodas();
    }

    public function obtenerPorId(int $id): ?Especialidad
    {
        return $this->especialidadRepository->obtenerPorId($id);
    }
}
