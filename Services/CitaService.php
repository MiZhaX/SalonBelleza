<?php

namespace Services;

use Repositories\CitaRepository;
use Services\EmpleadoService;
use Services\ServicioService;
use Services\ClienteService;
use Models\Cita;

class CitaService
{
    private CitaRepository $citaRepository;
    private ServicioService $servicioService;
    private EmpleadoService $empleadoService;
    private ClienteService $clienteService;

    public function __construct()
    {
        $this->citaRepository = new CitaRepository();
        $this->servicioService = new ServicioService();
        $this->empleadoService = new EmpleadoService();
        $this->clienteService = new ClienteService();
    }

    // Programar una cita
    public function programarCita(array $datos): int
    {
        $cita = new Cita(
            id: null,
            idCliente: $datos['id_cliente'],
            idEmpleado: $datos['id_empleado'],
            idServicio: $datos['id_servicio'],
            fecha: $datos['fecha'],
            hora: $datos['hora'],
            estado: 'pendiente'
        );

        // Crear la cita
        $this->citaRepository->crearCita($cita);

        // Obtener el ID de la cita recién insertada
        $idCita = $this->citaRepository->obtenerUltimoIdInsertado();
        return $idCita;
    }

    // Obtener todas las citas
    public function obtenerTodos(): array
    {
        return $this->citaRepository->obtenerTodos();
    }

    // Obtener todas las citas de un cliente
    public function obtenerCitasPorCliente(int $idCliente): array
    {
        return $this->citaRepository->obtenerCitasPorCliente($idCliente);
    }

    // Obtener todas las citas de un empledao
    public function obtenerCitasPorEmpleado(int $idEmpleado): array
    {
        return $this->citaRepository->obtenerCitasPorEmpleado($idEmpleado);
    }

    // Obtener una cita por su Id
    public function obtenerPorId(int $idCita): Cita{
        return $this->citaRepository->obtenerPorId($idCita);
    }

    // Verificar la disponibilidad en el horario de un empleado
    public function verificarDisponibilidadEmpleado(int $idEmpleado, string $fecha, string $hora, int $idServicio): bool
    {
        return $this->citaRepository->verificarDisponibilidadEmpleado($idEmpleado, $fecha, $hora, $idServicio);
    }

    // Verificar la disponibilidad en el horario de un cliente
    public function verificarDisponibilidadCliente(int $idCliente, string $fecha, string $hora, int $idServicio): bool
    {
        return $this->citaRepository->verificarDisponibilidadCliente($idCliente, $fecha, $hora, $idServicio);
    }

    // Actualizar el estado de una cita
    public function actualizarEstadoCita(int $idCita, string $nuevoEstado): bool
    {
        return $this->citaRepository->actualizarEstadoCita($idCita, $nuevoEstado);
    }

    // Borrar una cita
    public function borrarCita(int $idCita): bool
    {
        return $this->citaRepository->eliminarCita($idCita);
    }

    // Obtener todos los servicios, empleados y clientes (Mediante sus respectivos servicios)
    public function obtenerServiciosYEmpleadosYClientes()
    {
        $servicios = $this->servicioService->obtenerTodos();
        $empleados = $this->empleadoService->obtenerTodos();
        $clientes = $this->clienteService->obtenerTodos();

        // Devolver un array con todos los objetos
        return [
            'servicios' => $servicios,
            'empleados' => $empleados,
            'clientes' => $clientes
        ];
    }

    // Obtener el correo de un cliente por su Id
    public function obtenerCorreoClientePorId(string $id): string
    {
        return $this->clienteService->obtenerPorId($id)->getCorreo();
    }

    // Validar los datos para registrar una cita
    public function validarDatosCita(Cita $cita): array
    {
        // Validación de los datos en el modelo
        $errores = $cita->validarDatos(); 

        // Verificar que el empleado esté disponible para esa fecha y hora
        if (!$this->verificarDisponibilidadEmpleado($cita->getIdEmpleado(), $cita->getFecha(), $cita->getHora(), $cita->getIdServicio())) {
            $errores[] = "El empleado ya está ocupado en ese horario.";
        }

        // Verificar que el cliente esté disponible para esa fecha y hora
        if (!$this->verificarDisponibilidadCliente($cita->getIdCliente(), $cita->getFecha(), $cita->getHora(), $cita->getIdServicio())) {
            $errores[] = "El cliente ya está ocupado en ese horario.";
        }

        // Verificar que el servicio y el empleado sean compatibles (según la especialidad)
        $empleado = $this->empleadoService->obtenerPorId($cita->getIdEmpleado());
        $servicio = $this->servicioService->obtenerPorId($cita->getIdServicio());

        if ($empleado->getIdEspecialidad() !== $servicio->getIdEspecialidad()) {
            $errores[] = "El empleado seleccionado no puede realizar este servicio.";
        }

        return $errores;
    }

    // Obtener el resumen de una cita
    public function obtenerResumenCita(int $idCita): array
    {
        // Obtener la cita por su ID
        $cita = $this->citaRepository->obtenerPorId($idCita);

        // Obtener los datos para el resumen
        $servicio = $this->servicioService->obtenerPorId($cita->getIdServicio());
        $empleado = $this->empleadoService->obtenerPorId($cita->getIdEmpleado());
        $cliente = $this->clienteService->obtenerPorId($cita->getIdCliente());

        // Array con los datos del resumen
        $resumen = [
            'servicio' => $servicio->getNombre(),
            'precio' => $servicio->getPrecio(),
            'duracion' => $servicio->getDuracionMinutos(),
            'empleado' => $empleado->getNombre(),
            'cliente' => $cliente->getNombre(),
            'fecha' => $cita->getFecha(),
            'hora' => $cita->getHora(),
            'precioTotal' => $servicio->getPrecio(),
            'detalles' => $cita->getDetalles()
        ];

        return $resumen;
    }

    // Completar una cita
    public function finalizarCita(int $idCita, string $detalles): bool
    {
        return $this->citaRepository->finalizarCita($idCita, $detalles);
    }
}
