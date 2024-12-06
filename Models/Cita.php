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
    private int $duracionMinutos;
    private string $estado;

    public function __construct(?int $id = 0, int $idCliente = 0, int $idEmpleado = 0, int $idServicio = 0, string $fecha = '', string $hora = '', int $duracionMinutos = 0, string $estado = '')
    {
        $this->id = $id;
        $this->idCliente = $idCliente;
        $this->idEmpleado = $idEmpleado;
        $this->idServicio = $idServicio;
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->duracionMinutos = $duracionMinutos;
        $this->estado = $estado;
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

    public function getDuracionMinutos(): int
    {
        return $this->duracionMinutos;
    }

    public function getEstado(): string
    {
        return $this->estado;
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

    public function setDuracionMinutos(int $duracionMinutos): void
    {
        $this->duracionMinutos = $duracionMinutos;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }


    public function validarDatos($empleadoService, $servicioService, $citaService): array
    {
        $errores = [];

        // 1. Verificar que la fecha y hora no sean anteriores a la actual
        $fechaHoraActual = new \DateTime();
        $fechaHoraCita = \DateTime::createFromFormat('Y-m-d H:i', $this->fecha . ' ' . $this->hora);
        if ($fechaHoraCita < $fechaHoraActual) {
            $errores[] = "La fecha y hora de la cita no pueden ser anteriores al día de hoy.";
        }

        // 2. Verificar que la cita esté dentro del horario de atención (por ejemplo, de 9 AM a 7 PM)
        $horaCita = (int)substr($this->hora, 0, 2);
        if ($horaCita < 9 || $horaCita > 19) {
            $errores[] = "La hora de la cita debe estar entre las 9:00 AM y las 7:00 PM.";
        }

        // 3. Verificar que el empleado esté disponible para esa fecha y hora
        if ($citaService->verificarDisponibilidadEmpleado($this->idEmpleado, $this->fecha, $this->hora, $this->duracionMinutos) != 0) {
            $errores[] = "El empleado ya está ocupado en ese horario.";
        }

        // 4. Verificar que el servicio y el empleado sean compatibles (según la especialidad)
        $empleado = $empleadoService->obtenerPorId($this->idEmpleado);
        $servicio = $servicioService->obtenerPorId($this->idServicio);

        if ($empleado->getIdEspecialidad() !== $servicio->getIdEspecialidad()) {
            $errores[] = "El empleado seleccionado no puede realizar este servicio.";
        }

        return $errores;
    }
}

