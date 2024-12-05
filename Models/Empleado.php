<?php

namespace Models;

class Empleado
{
    private int $id;
    private string $nombre;
    private string $correo;
    private string $telefono;
    private string $password;
    private int $idEspecialidad;

    // Constructor
    public function __construct(
        int $id = 0,
        string $nombre = '',
        string $correo = '',
        string $telefono = '',
        string $password = '',
        int $idEspecialidad = 0
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->telefono = $telefono;
        $this->password = $password;
        $this->idEspecialidad = $idEspecialidad;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getIdEspecialidad(): int
    {
        return $this->idEspecialidad;
    }

    // Setters
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setCorreo(string $correo): void
    {
        $this->correo = $correo;
    }

    public function setTelefono(string $telefono): void
    {
        $this->telefono = $telefono;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setIdEspecialidad(int $idEspecialidad): void
    {
        $this->idEspecialidad = $idEspecialidad;
    }

    // Validar los datos del empleado
    public function validarDatos(): array
    {
        $errores = [];

        if (empty($this->nombre)) {
            $errores[] = "El nombre es obligatorio.";
        }

        if (!filter_var($this->correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo electrónico no es válido.";
        }

        if (empty($this->telefono) || !preg_match('/^\+?\d{10,15}$/', $this->telefono)) {
            $errores[] = "El teléfono no es válido.";
        }

        if (strlen($this->password) < 8) {
            $errores[] = "La contraseña debe tener al menos 8 caracteres.";
        }
        
        if (empty($this->idEspecialidad)) {
            $errores[] = "La especialidad del empleado es obligatoria.";
        }

        return $errores;
    }
}
?>
