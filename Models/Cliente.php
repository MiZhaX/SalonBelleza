<?php

namespace Models;

class Cliente
{
    private int $id;
    private string $nombre;
    private string $correo;
    private string $telefono;
    private string $fechaNacimiento;
    private string $password;

    // Constructor
    public function __construct(
        int $id = 0,
        string $nombre = "",
        string $correo = "",
        string $telefono = "",
        string $fechaNacimiento = "",
        string $password = ""
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->telefono = $telefono;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    // Getters y setters
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): void
    {
        $this->correo = $correo;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): void
    {
        $this->telefono = $telefono;
    }

    public function getFechaNacimiento(): string
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(string $fechaNacimiento): void
    {
        $this->fechaNacimiento = $fechaNacimiento;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function validarDatos(array $datos): array {
        $errores = [];

        // Validación del nombre
        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio.';
        }

        // Validación del correo
        if (empty($datos['correo']) || !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores['correo'] = 'El correo electrónico no es válido.';
        }

        // Validación del teléfono
        if (empty($datos['telefono']) || !preg_match('/^[0-9]{9}$/', $datos['telefono'])) {
            $errores['telefono'] = 'El teléfono debe tener 9 dígitos.';
        }

        // Validación de la fecha de nacimiento
        if (empty($datos['fecha_nacimiento'])) {
            $errores['fecha_nacimiento'] = 'La fecha de nacimiento es obligatoria.';
        }

        // Validación de la contraseña
        if (empty($datos['password'])) {
            $errores['password'] = 'La contraseña es obligatoria.';
        }

        return $errores;
    }
}
