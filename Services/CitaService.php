<?php

namespace Services;

use Repositories\CitaRepository;
use Models\Cita;

class CitaService
{
    private CitaRepository $citaRepository;

    public function __construct()
    {
        $this->citaRepository = new CitaRepository();
    }

    public function programarCita(array $datos): bool
    {
        $cita = new Cita(
            id: null,
            idCliente: $datos['id_cliente'],
            idEmpleado: $datos['id_empleado'],
            idServicio: $datos['id_servicio'],
            fecha: $datos['fecha'],
            hora: $datos['hora'],
            duracionMinutos: $datos['duracion_minutos'],
            estado: 'pendiente'
        );

        return $this->citaRepository->crearCita($cita);
    }

    public function obtenerCitasPorCliente(int $idCliente): array
    {
        return $this->citaRepository->obtenerCitasPorCliente($idCliente);
    }

    public function obtenerCitasPorEmpleado(int $idEmpleado): array
    {
        return $this->citaRepository->obtenerCitasPorEmpleado($idEmpleado);
    }

    public function verificarDisponibilidadEmpleado(int $idEmpleado, string $fecha, string $hora, int $duracionMinutos): bool
    {
        return $this->citaRepository->verificarDisponibilidadEmpleado($idEmpleado, $fecha, $hora, $duracionMinutos);
    }

    public function actualizarEstadoCita(int $idCita, string $nuevoEstado): bool
    {
        return $this->citaRepository->actualizarEstadoCita($idCita, $nuevoEstado);
    }
}
