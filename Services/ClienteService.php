<?php

namespace Services;

use Repositories\ClienteRepository;
use Models\Cliente;

class ClienteService
{

    private ClienteRepository $clienteRepository;

    public function __construct()
    {
        $this->clienteRepository = new ClienteRepository();
    }

    // Obtener todos los clientes
    public function obtenerTodosClientes(): array
    {
        return $this->clienteRepository->obtenerTodos();
    }

    // Obtener cliente por su correo
    public function obtenerClientePorCorreo(string $correo): ?Cliente
    {
        return $this->clienteRepository->obtenerPorCorreo($correo);
    }


    // Crear un cliente
    public function crearCliente(array $datos): bool
    {
        // Cifrar la contraseña usando password_hash
        $passwordCifrada = password_hash($datos['password'], PASSWORD_BCRYPT);

        $cliente = new Cliente(
            nombre: $datos['nombre'],
            correo: $datos['correo'],
            telefono: $datos['telefono'],
            fechaNacimiento: $datos['fecha_nacimiento'],
            password: $passwordCifrada
        );

        return $this->clienteRepository->insertar($cliente);
    }

    // Actualizar un cliente
    public function actualizarCliente(array $datos): bool
    {
        // Si la contraseña fue modificada, la ciframos nuevamente
        $passwordCifrada = !empty($datos['password']) ? password_hash($datos['password'], PASSWORD_BCRYPT) : null;

        $cliente = new Cliente(
            id: $datos['id'],
            nombre: $datos['nombre'],
            correo: $datos['correo'],
            telefono: $datos['telefono'],
            fechaNacimiento: $datos['fecha_nacimiento'],
            password: $passwordCifrada ?? $datos['password']
        );

        return $this->clienteRepository->actualizar($cliente);
    }

    // Eliminar un cliente
    public function eliminarCliente(int $id): bool
    {
        return $this->clienteRepository->eliminar($id);
    }
}
