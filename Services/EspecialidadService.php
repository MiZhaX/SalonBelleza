<?php

namespace Services;

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
}
