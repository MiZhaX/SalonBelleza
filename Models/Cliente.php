<?php

namespace Models;

use DateTime;

class Cliente
{
    private int $id;
    private string $nombre;
    private string $correo;
    private string $telefono;
    private string $fechaNacimiento;
    private string $password;
    private ?string $tokenConfirmacion;

    // Constructor
    public function __construct(
        int $id = 0,
        string $nombre = "",
        string $correo = "",
        string $telefono = "",
        string $fechaNacimiento = "",
        string $password = "",
        ?string $tokenConfirmacion = ""
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->telefono = $telefono;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->password = $password;
        $this->tokenConfirmacion = $tokenConfirmacion;
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
        $this->password = $password;
    }

    public function getTokenConfirmacion(): ?string
    {
        return $this->tokenConfirmacion;
    }

    public function setTokenConfirmacion(?string $tokenConfirmacion): void
    {
        $this->tokenConfirmacion = $tokenConfirmacion;
    }

    // Validar datos
    public function validarDatos(array $datos): array
    {
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
        } else {
            $fechaNacimiento = DateTime::createFromFormat('Y-m-d', $datos['fecha_nacimiento']);
            $fechaHoy = new DateTime();

            if (!$fechaNacimiento) {
                $errores['fecha_nacimiento'] = 'La fecha de nacimiento no tiene un formato válido.';
            } elseif ($fechaNacimiento >= $fechaHoy) {
                $errores['fecha_nacimiento'] = 'La fecha de nacimiento debe ser anterior a hoy.';
            }
        }

        // Validación de la contraseña
        if (empty($datos['password'])) {
            $errores['password'] = 'La contraseña es obligatoria.';
        } else {
            // Validar que la contraseña tenga al menos 8 caracteres, una mayúscula y un símbolo
            if (!preg_match('/^(?=.*[A-Z])(?=.*[!@#$%^&*()_\-+={}\[\]:;"\'<>,.?\/\\|`~])[A-Za-z\d!@#$%^&*()_\-+={}\[\]:;"\'<>,.?\/\\|`~]{8,}$/', $datos['password'])) {
                $errores['password'] = 'La contraseña debe tener al menos 8 caracteres, una mayúscula y un símbolo.';
            }
        }

        return $errores;
    }
}
