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

    public function obtenerPorCorreo(string $correo): ?Empleado
    {
        $sql = "SELECT * FROM empleados WHERE correo = :correo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $empleado = new Empleado();
            $empleado->setId($resultado['id']);
            $empleado->setNombre($resultado['nombre']);
            $empleado->setCorreo($resultado['correo']);
            $empleado->setTelefono($resultado['telefono']);
            $empleado->setIdEspecialidad($resultado['id_especialidad']);
            $empleado->setPassword($resultado['password']);
            return $empleado;
        }

        return null;
    }

    public function insertar(Empleado $empleado): bool
    {
        // Recuperar los datos
        $nombre = $empleado->getNombre();
        $correo = $empleado->getCorreo();
        $telefono = $empleado->getTelefono();
        $password = $empleado->getPassword();
        $idEspecialidad = $empleado->getIdEspecialidad();

        // Preparar la consulta SQL
        $sql = "INSERT INTO empleados (nombre, correo, telefono, password, id_especialidad) 
            VALUES (:nombre, :correo, :telefono, :password, :id_especialidad)";

        // Preparar la sentencia
        $stmt = $this->conexion->prepare($sql);

        // Enlazar las variables
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':id_especialidad', $idEspecialidad, PDO::PARAM_INT);

        // Ejecutar la consulta
        return $stmt->execute();
    }
}
