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

    public function obtenerTodos(): array
    {
        return $this->empleadoRepository->obtenerTodos();
    }

    public function obtenerEmpleadoPorCorreo(string $correo): ?Empleado
    {
        return $this->empleadoRepository->obtenerPorCorreo($correo);
    }

    public function obtenerPorId(int $id): ?Empleado
    {
        return $this->empleadoRepository->obtenerPorId($id);
    }

    public function crearEmpleado(Empleado $empleado): bool
    {
        return $this->empleadoRepository->insertar($empleado);
    }

    public function obtenerEmpleadosPorEspecialidad(int $idEspecialidad): array
    {
        return $this->empleadoRepository->obtenerPorEspecialidad($idEspecialidad);
    }

    public function despedirEmpleado(int $idEmpleado): bool
    {
        // Verificar si el empleado existe antes de intentar despedirlo
        $empleado = $this->empleadoRepository->obtenerPorId($idEmpleado);

        if (!$empleado) {
            return false; // No se encontró el empleado
        }

        // Realizar el despido (podría ser una eliminación o un cambio de estado)
        return $this->empleadoRepository->eliminar($idEmpleado);
    }
}
