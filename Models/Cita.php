<?php
namespace Models;

class Cita
{
    private ?int $id;
    private int $idCliente;
    private int $idEmpleado;
    private int $idServicio;
    private string $fecha;
    private string $hora;
    private string $estado;
    private string $detalles;

    public function __construct(?int $id = 0, int $idCliente = 0, int $idEmpleado = 0, int $idServicio = 0, string $fecha = '', string $hora = '', string $estado = '', string $detalles = '')
    {
        $this->id = $id;
        $this->idCliente = $idCliente;
        $this->idEmpleado = $idEmpleado;
        $this->idServicio = $idServicio;
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->estado = $estado;
        $this->detalles = $detalles;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCliente(): int
    {
        return $this->idCliente;
    }

    public function getIdEmpleado(): int
    {
        return $this->idEmpleado;
    }

    public function getIdServicio(): int
    {
        return $this->idServicio;
    }

    public function getFecha(): string
    {
        return $this->fecha;
    }

    public function getHora(): string
    {
        return $this->hora;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getDetalles(): ?string
    {
        return $this->detalles;
    }

    // Setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setIdCliente(int $idCliente): void
    {
        $this->idCliente = $idCliente;
    }

    public function setIdEmpleado(int $idEmpleado): void
    {
        $this->idEmpleado = $idEmpleado;
    }

    public function setIdServicio(int $idServicio): void
    {
        $this->idServicio = $idServicio;
    }

    public function setFecha(string $fecha): void
    {
        $this->fecha = $fecha;
    }

    public function setHora(string $hora): void
    {
        $this->hora = $hora;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function setDetalles(?string $detalles): void
    {
        $this->detalles = $detalles;
    }

    public function validarDatos(): array
    {
        $errores = [];

        // 1. Validar la fecha y hora 
        $fechaHoraActual = new \DateTime();
        $fechaHoraCita = \DateTime::createFromFormat('Y-m-d H:i', $this->fecha . ' ' . $this->hora);
        if ($fechaHoraCita < $fechaHoraActual) {
            $errores[] = "La fecha y hora de la cita no pueden ser anteriores al día de hoy.";
        }

        // 2. Validar que la hora de la cita esté en el horario permitido 
        $horaCita = (int)substr($this->hora, 0, 2);
        if ($horaCita < 9 || $horaCita > 19) {
            $errores[] = "La hora de la cita debe estar entre las 9:00 AM y las 7:00 PM.";
        }

        return $errores;
    }
}

