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

    // Obtener todos los empleados
    public function obtenerTodos(): array
    {
        return $this->empleadoRepository->obtenerTodos();
    }

    // Obtener todos los empleados por de una especialidad
    public function obtenerPorEspecialidad(int $idEspecialidad): array
    {
        return $this->empleadoRepository->obtenerTodosPorEspecialidad($idEspecialidad);
    }

    // Obtener un empleado por su correo
    public function obtenerPorCorreo(string $correo): ?Empleado
    {
        return $this->empleadoRepository->obtenerPorColumna('correo', $correo);
    }

    // Obtener un empleado por su id
    public function obtenerPorId(int $id): ?Empleado
    {
        return $this->empleadoRepository->obtenerPorColumna('id', $id);
    }

    // Registrar un empleado
    public function crearEmpleado(Empleado $empleado): bool
    {
        return $this->empleadoRepository->insertar($empleado);
    }

    // Despedir a un empleado
    public function despedirEmpleado(int $idEmpleado): bool
    {
        // Verificar si el empleado existe antes de intentar despedirlo
        $empleado = $this->empleadoRepository->obtenerPorColumna('id', $idEmpleado);

        if (!$empleado) {
            return false; // No se encontró el empleado
        }

        // Realizar el despido (podría ser una eliminación o un cambio de estado)
        return $this->empleadoRepository->eliminar($idEmpleado);
    }
}
