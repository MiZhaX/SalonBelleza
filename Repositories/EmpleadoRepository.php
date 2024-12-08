<?php

namespace Repositories;

use Models\Empleado;
use Lib\BaseDatos;
use PDO;

class EmpleadoRepository
{
    private BaseDatos $conexion;

    public function __construct()
    {
        $this->conexion = new BaseDatos();
    }

    // Obtener todos los empleados
    public function obtenerTodos(): array
    {
        $sql = "SELECT * FROM empleados";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        $empleadosData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $empleados = [];

        // Crear un nuevo empleado y almacenarlo por cada fila detectada
        foreach ($empleadosData as $empleadoData) {
            $empleado = new Empleado(
                id: $empleadoData['id'],
                nombre: $empleadoData['nombre'],
                correo: $empleadoData['correo'],
                telefono: $empleadoData['telefono'],
                password: $empleadoData['password'],
                idEspecialidad: $empleadoData['id_especialidad']
            );
            $empleados[] = $empleado;
        }

        return $empleados;
    }

    // Obtener todos los empleados de una especialidad
    public function obtenerTodosPorEspecialidad(int $idEspecialidad): array
    {
        $query = "SELECT id, nombre FROM empleados WHERE id_especialidad = :id_especialidad";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_especialidad', $idEspecialidad, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener datos de un empleado según una columna
    public function obtenerPorColumna(string $columna, string $valor): ?Empleado
    {
        $sql = "SELECT * FROM empleados WHERE {$columna} = :valor";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Crear y devolver los datos del empleado encontrado
        if ($resultado) {
            $empleado = new Empleado(
                id: $resultado['id'],
                nombre: $resultado['nombre'],
                correo: $resultado['correo'],
                telefono: $resultado['telefono'],
                password: $resultado['password'],
                idEspecialidad: $resultado['id_especialidad']
            );
            return $empleado;
        }

        return null;
    }

    // Insertar empleado
    public function insertar(Empleado $empleado): bool
    {
        // Obtener los datos del empleado
        $nombre = $empleado->getNombre();
        $correo = $empleado->getCorreo();
        $telefono = $empleado->getTelefono();
        $password = $empleado->getPassword();
        $idEspecialidad = $empleado->getIdEspecialidad();

        $sql = "INSERT INTO empleados (nombre, correo, telefono, password, id_especialidad) 
            VALUES (:nombre, :correo, :telefono, :password, :id_especialidad)";

        $stmt = $this->conexion->prepare($sql);

        // Enlazar las variables con los datos del empleado
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':id_especialidad', $idEspecialidad, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Eliminar empleado
    public function eliminar(int $idEmpleado): bool
    {
        $query = "DELETE FROM empleados WHERE id = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $idEmpleado, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
