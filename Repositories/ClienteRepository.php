<?php

namespace Repositories;

use Models\Cliente;
use Lib\BaseDatos;
use PDO;

class ClienteRepository
{

    private BaseDatos $conexion;

    public function __construct()
    {
        $this->conexion = new BaseDatos();
    }

    // Obtener todos los clientes
    public function obtenerTodos(): array
    {
        $sql = "SELECT * FROM clientes";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        $clientesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $clientes = [];

        foreach ($clientesData as $clienteData) {
            $cliente = new Cliente(
                id: $clienteData['id'],
                nombre: $clienteData['nombre'],
                correo: $clienteData['correo'],
                telefono: $clienteData['telefono'],
                fechaNacimiento: $clienteData['fecha_nacimiento'],
                password: $clienteData['password']
            );
            $clientes[] = $cliente;
        }

        return $clientes;
    }

    // Obtener cliente por su correo
    public function obtenerPorCorreo(string $correo): ?Cliente
    {
        $sql = "SELECT * FROM clientes WHERE correo = :correo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $cliente = new Cliente();
            $cliente->setId($resultado['id']);
            $cliente->setNombre($resultado['nombre']);
            $cliente->setCorreo($resultado['correo']);
            $cliente->setTelefono($resultado['telefono']);
            $cliente->setFechaNacimiento($resultado['fecha_nacimiento']);
            $cliente->setPassword($resultado['password']);
            return $cliente;
        }

        return null;
    }


    // Insertar un cliente
    public function insertar(Cliente $cliente): bool
    {
        // Recuperamos los valores en variables
        $nombre = $cliente->getNombre();
        $correo = $cliente->getCorreo();
        $telefono = $cliente->getTelefono();
        $fechaNacimiento = $cliente->getFechaNacimiento();
        $password = $cliente->getPassword();

        // Preparar la consulta SQL
        $sql = "INSERT INTO clientes (nombre, correo, telefono, fecha_nacimiento, password) 
            VALUES (:nombre, :correo, :telefono, :fecha_nacimiento, :password)";

        // Preparamos la sentencia
        $stmt = $this->conexion->prepare($sql);

        // Enlazamos las variables
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_nacimiento', $fechaNacimiento, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);

        // Ejecutamos la consulta
        return $stmt->execute();
    }


    // Actualizar un cliente
    public function actualizar(Cliente $cliente): bool
    {
        $sql = "UPDATE clientes SET nombre = :nombre, correo = :correo, telefono = :telefono, 
                    fecha_nacimiento = :fecha_nacimiento, password = :password
                    WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);

        $stmt->bindParam(':id', $cliente->getId(), PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $cliente->getNombre(), PDO::PARAM_STR);
        $stmt->bindParam(':correo', $cliente->getCorreo(), PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $cliente->getTelefono(), PDO::PARAM_STR);
        $stmt->bindParam(':fecha_nacimiento', $cliente->getFechaNacimiento(), PDO::PARAM_STR);
        $stmt->bindParam(':password', $cliente->getPassword(), PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Eliminar un cliente
    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM clientes WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
