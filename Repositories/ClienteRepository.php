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

        // Crear un nuevo cliente y almacenarlo por cada fila detectada
        foreach ($clientesData as $clienteData) {
            $cliente = new Cliente(
                id: $clienteData['id'],
                nombre: $clienteData['nombre'],
                correo: $clienteData['correo'],
                telefono: $clienteData['telefono'],
                fechaNacimiento: $clienteData['fecha_nacimiento'],
                password: $clienteData['password'],
                tokenConfirmacion: $clienteData['token_confirmacion']
            );
            $clientes[] = $cliente;
        }

        return $clientes;
    }

    // Obtener datos de un cliente según una columna
    public function obtenerPorColumna(string $columna, string $valor): ?Cliente
    {
        $sql = "SELECT * FROM clientes WHERE {$columna} = :valor";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Crear y devolver los datos del cliente encontrado
        if ($resultado) {
            return new Cliente(
                id: $resultado['id'],
                nombre: $resultado['nombre'],
                correo: $resultado['correo'],
                telefono: $resultado['telefono'],
                fechaNacimiento: $resultado['fecha_nacimiento'],
                password: $resultado['password'],
                tokenConfirmacion: $resultado['token_confirmacion']
            );
        }

        return null;
    }

    // Insertar un cliente
    public function insertar(Cliente $cliente): bool
    {
        // Obtener los datos del cliente
        $nombre = $cliente->getNombre();
        $correo = $cliente->getCorreo();
        $telefono = $cliente->getTelefono();
        $fechaNacimiento = $cliente->getFechaNacimiento();
        $password = $cliente->getPassword();
        $token = $cliente->getTokenConfirmacion();

        $sql = "INSERT INTO clientes (nombre, correo, telefono, fecha_nacimiento, password, token_confirmacion) 
            VALUES (:nombre, :correo, :telefono, :fecha_nacimiento, :password, :token)";

        $stmt = $this->conexion->prepare($sql);

        // Enlazar los parametros con los valores del Cliente
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_nacimiento', $fechaNacimiento, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Actualizar un cliente
    public function actualizar(Cliente $cliente): bool
    {
        $sql = "UPDATE clientes SET nombre = :nombre, correo = :correo, telefono = :telefono, 
                    fecha_nacimiento = :fecha_nacimiento, password = :password, token_confirmacion = :token
                    WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);

        $stmt->bindParam(':id', $cliente->getId(), PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $cliente->getNombre(), PDO::PARAM_STR);
        $stmt->bindParam(':correo', $cliente->getCorreo(), PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $cliente->getTelefono(), PDO::PARAM_STR);
        $stmt->bindParam(':fecha_nacimiento', $cliente->getFechaNacimiento(), PDO::PARAM_STR);
        $stmt->bindParam(':password', $cliente->getPassword(), PDO::PARAM_STR);
        $stmt->bindParam(':token', $cliente->getTokenConfirmacion(), PDO::PARAM_STR);

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

    // Actualizar el token de un cliente (Activar cuenta)
    public function activarCuenta(Cliente $cliente): bool
    {
        $query = "UPDATE clientes SET token_confirmacion = NULL WHERE id = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindValue(':id', $cliente->getId());
        return $stmt->execute();
    }
}
