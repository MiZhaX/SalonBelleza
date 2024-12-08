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
    public function obtenerTodos(): array
    {
        return $this->clienteRepository->obtenerTodos();
    }

    // Obtener cliente por su correo
    public function obtenerPorCorreo(string $correo): ?Cliente
    {
        return $this->clienteRepository->obtenerPorColumna('correo', $correo);
    }

    public function obtenerPorId(string $id): ?Cliente
    {
        return $this->clienteRepository->obtenerPorColumna('id', $id);
    }

    public function obtenerPorToken(string $token): ?Cliente
    {
        return $this->clienteRepository->obtenerPorColumna('token_confirmacion', $token);
    }

    // Crear un cliente
    public function crearCliente(array $datos): bool
    {
        $token = bin2hex(random_bytes(16));
        $datos['token_confirmacion'] = $token;

        $cliente = new Cliente(
            nombre: $datos['nombre'],
            correo: $datos['correo'],
            telefono: $datos['telefono'],
            fechaNacimiento: $datos['fecha_nacimiento'],
            password: $datos['password'],
            tokenConfirmacion: $token
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
            password: $passwordCifrada ?? $datos['password'],
            tokenConfirmacion: $datos['tokenConfirmacion']
        );

        return $this->clienteRepository->actualizar($cliente);
    }

    // Eliminar un cliente
    public function eliminarCliente(int $id): bool
    {
        return $this->clienteRepository->eliminar($id);
    }

    // Activar la cuenta 
    public function activarCuenta(Cliente $cliente): bool
    {
        return $this->clienteRepository->activarCuenta($cliente);
    }
}
