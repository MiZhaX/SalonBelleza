<?php

namespace Services;

use Repositories\EmpleadoRepository;
use Models\Empleado;

class EmpleadoService
{
    private EmpleadoRepository $empleadoRepository;

    public function __construct()
    {
        $this->empleadoRepository = new EmpleadoRepository();
    }

    public function obtenerEmpleadoPorCorreo(string $correo): ?Empleado
    {
        return $this->empleadoRepository->obtenerPorCorreo($correo);
    }

    public function crearEmpleado(Empleado $empleado): bool
    {
        return $this->empleadoRepository->insertar($empleado);
    }
}
