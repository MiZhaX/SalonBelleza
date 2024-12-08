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

        // Guardar la cita en la base de datos
        $this->citaRepository->crearCita($cita);

        // Obtener el ID de la cita recién insertada
        $idCita = $this->citaRepository->obtenerUltimoIdInsertado();

        // Retornar el ID de la cita
        return $idCita;
    }

    public function obtenerTodos(): array
    {
        return $this->citaRepository->obtenerTodos();
    }

    public function obtenerCitasPorCliente(int $idCliente): array
    {
        return $this->citaRepository->obtenerCitasPorCliente($idCliente);
    }

    public function obtenerCitasPorEmpleado(int $idEmpleado): array
    {
        return $this->citaRepository->obtenerCitasPorEmpleado($idEmpleado);
    }

    public function obtenerPorId(int $idCita): Cita{
        return $this->citaRepository->obtenerPorId($idCita);
    }

    public function verificarDisponibilidadEmpleado(int $idEmpleado, string $fecha, string $hora, int $idServicio): bool
    {
        return $this->citaRepository->verificarDisponibilidadEmpleado($idEmpleado, $fecha, $hora, $idServicio);
    }

    public function verificarDisponibilidadCliente(int $idCliente, string $fecha, string $hora, int $idServicio): bool
    {
        return $this->citaRepository->verificarDisponibilidadCliente($idCliente, $fecha, $hora, $idServicio);
    }

    public function actualizarEstadoCita(int $idCita, string $nuevoEstado): bool
    {
        return $this->citaRepository->actualizarEstadoCita($idCita, $nuevoEstado);
    }

    public function borrarCita(int $idCita): bool
    {
        return $this->citaRepository->eliminarCita($idCita);
    }

    public function obtenerServiciosYEmpleadosYClientes()
    {
        $servicios = $this->servicioService->obtenerTodos();
        $empleados = $this->empleadoService->obtenerTodos();
        $clientes = $this->clienteService->obtenerTodos();
        return [
            'servicios' => $servicios,
            'empleados' => $empleados,
            'clientes' => $clientes
        ];
    }

    public function obtenerCorreoClientePorId(string $id): string
    {
        return $this->clienteService->obtenerPorId($id)->getCorreo();
    }

    public function validarDatosCita(Cita $cita): array
    {
        $errores = $cita->validarDatos(); // Validación interna del modelo

        // 3. Verificar que el empleado esté disponible para esa fecha y hora
        if (!$this->verificarDisponibilidadEmpleado($cita->getIdEmpleado(), $cita->getFecha(), $cita->getHora(), $cita->getIdServicio())) {
            $errores[] = "El empleado ya está ocupado en ese horario.";
        }

        // 4. Verificar que el cliente esté disponible para esa fecha y hora
        if (!$this->verificarDisponibilidadCliente($cita->getIdCliente(), $cita->getFecha(), $cita->getHora(), $cita->getIdServicio())) {
            $errores[] = "El cliente ya está ocupado en ese horario.";
        }

        // 5. Verificar que el servicio y el empleado sean compatibles (según la especialidad)
        $empleado = $this->empleadoService->obtenerPorId($cita->getIdEmpleado());
        $servicio = $this->servicioService->obtenerPorId($cita->getIdServicio());

        if ($empleado->getIdEspecialidad() !== $servicio->getIdEspecialidad()) {
            $errores[] = "El empleado seleccionado no puede realizar este servicio.";
        }

        return $errores;
    }

    public function obtenerResumenCita(int $idCita): array
    {
        // Obtener la cita por su ID
        $cita = $this->citaRepository->obtenerPorId($idCita);

        // Aquí obtenemos los datos relacionados con la cita para el resumen
        $servicio = $this->servicioService->obtenerPorId($cita->getIdServicio());
        $empleado = $this->empleadoService->obtenerPorId($cita->getIdEmpleado());
        $cliente = $this->clienteService->obtenerPorId($cita->getIdCliente());

        // Preparamos el resumen
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

    public function finalizarCita(int $idCita, string $detalles): bool
    {
        return $this->citaRepository->finalizarCita($idCita, $detalles);
    }
}
