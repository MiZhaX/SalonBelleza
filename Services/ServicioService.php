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

    // Obtener todos los servicios
    public function obtenerTodos(): array
    {
        return $this->servicioRepository->obtenerTodos();
    }

    // Obtener un servicio por id
    public function obtenerPorId(int $id): ?Servicio
    {
        return $this->servicioRepository->obtenerPorId($id);
    }
}
