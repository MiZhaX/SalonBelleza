<?php

namespace Controllers;

use Lib\Pages;
use Services\CitaService;
use Services\ServicioService;
use Services\EmpleadoService;
use Models\Cita;

class CitaController
{
    private CitaService $citaService;
    private ServicioService $servicioService;
    private EmpleadoService $empleadoService;
    private Pages $pages;

    public function __construct() 
    {
        $this->citaService = new CitaService();
        $this->servicioService = new ServicioService();
        $this->empleadoService = new EmpleadoService();
        $this->pages = new Pages();
        session_start();
    }

    // Mostrar formulario de programación de citas
    public function programarCita(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;

            // Crear una nueva instancia de Cita
            $cita = new Cita(
                idCliente: $_SESSION['id'],
                idEmpleado: $datos['id_empleado'],
                idServicio: $datos['id_servicio'],
                fecha: $datos['fecha'],
                hora: $datos['hora'],
                duracionMinutos: $this->servicioService->obtenerPorId($datos['id_servicio'])->getDuracionMinutos()
            );

            // Validar los datos de la cita
            $errores = $cita->validarDatos($this->empleadoService, $this->servicioService, $this->citaService);

            if (!empty($errores)) {
                // Recargar la página con los errores
                $servicios = $this->servicioService->obtenerTodos();
                $empleados = $this->empleadoService->obtenerTodos();
                $this->pages->render('Cita/programarCita', [
                    'errores' => $errores,
                    'servicios' => $servicios,
                    'empleados' => $empleados,
                ]);
                return;
            }

            // Programar la cita
            $resultado = $this->citaService->programarCita([
                'id_cliente' => $_SESSION['id'],
                'id_empleado' => $datos['id_empleado'],
                'id_servicio' => $datos['id_servicio'],
                'fecha' => $datos['fecha'],
                'hora' => $datos['hora'],
                'duracion_minutos' => $this->servicioService->obtenerPorId($datos['id_servicio'])->getDuracionMinutos(),
            ]);

            if ($resultado) {
                $this->pages->render('Cita/programarCita', [
                    'mensajeExito' => 'La cita se ha programado correctamente.',
                ]);
            } else {
                $this->pages->render('Cita/programarCita', [
                    'errores' => ['Ocurrió un error al programar la cita. Intenta nuevamente.'],
                ]);
            }
        } else {
            // Mostrar formulario para programar cita
            $servicios = $this->servicioService->obtenerTodos();
            $empleados = $this->empleadoService->obtenerTodos();
            $this->pages->render('Cita/programarCita', [
                'servicios' => $servicios,
                'empleados' => $empleados,
            ]);
        }
    }

    // Mostrar citas del cliente actual
    public function verCitasCliente(): void
    {
        $citas = $this->citaService->obtenerCitasPorCliente($_SESSION['id']);
        $this->pages->render('Cita/misCitas', [
            'citas' => $citas,
        ]);
    }

    // Mostrar citas de un empleado (como administrador o empleado)
    public function verCitasEmpleado(int $idEmpleado): void
    {
        if (!isset($_SESSION['id']) || ($_SESSION['tipo'] !== 'administrador' && $_SESSION['tipo'] !== 'empleado')) {
            header('Location: ' . BASE_URL . 'Empleado/iniciarSesion');
            exit;
        }

        $citas = $this->citaService->obtenerCitasPorEmpleado($idEmpleado);
        $this->pages->render('Cita/verCitasEmpleado', [
            'citas' => $citas,
        ]);
    }

    // Cambiar estado de una cita
    public function actualizarEstado(int $idCita, string $estado): void
    {
        if (!isset($_SESSION['id']) || ($_SESSION['tipo'] !== 'administrador' && $_SESSION['tipo'] !== 'empleado')) {
            header('Location: ' . BASE_URL . 'Empleado/iniciarSesion');
            exit;
        }

        $resultado = $this->citaService->actualizarEstadoCita($idCita, $estado);

        if ($resultado) {
            echo "Estado de la cita actualizado correctamente.";
        } else {
            echo "Error al actualizar el estado de la cita.";
        }
    }
}
