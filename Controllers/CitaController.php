<?php

namespace Controllers;

use Lib\Pages;
use Services\CitaService;
use Models\Cita;

class CitaController
{
    private CitaService $citaService;
    private Pages $pages;

    public function __construct()
    {
        $this->citaService = new CitaService();
        $this->pages = new Pages();
        session_start();
    }

    // Mostrar formulario de programación de citas
    public function programarCita(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;

            if ($_SESSION['tipo'] == 'empleado') {
                $idCliente = $datos['id_cliente'];
            } else {
                $idCliente = $_SESSION['id'];
            }

            // Crear una nueva instancia de Cita
            $cita = new Cita(
                idCliente: $idCliente,
                idEmpleado: $datos['id_empleado'],
                idServicio: $datos['id_servicio'],
                fecha: $datos['fecha'],
                hora: $datos['hora'],
            );

            // Validar los datos de la cita usando el CitaService
            $errores = $this->citaService->validarDatosCita($cita);

            if (!empty($errores)) {
                // Delegar la obtención de servicios y empleados al CitaService
                $datosServiciosYEmpleados = $this->citaService->obtenerServiciosYEmpleadosYClientes();

                // Recargar la página con los errores
                $this->pages->render('Cita/programarCita', [
                    'errores' => $errores,
                    'servicios' => $datosServiciosYEmpleados['servicios'],
                    'empleados' => $datosServiciosYEmpleados['empleados'],
                    'clientes' => $datosServiciosYEmpleados['clientes']
                ]);
                return;
            }

            // Programar la cita
            $resultado = $this->citaService->programarCita([
                'id_cliente' => $idCliente,
                'id_empleado' => $datos['id_empleado'],
                'id_servicio' => $datos['id_servicio'],
                'fecha' => $datos['fecha'],
                'hora' => $datos['hora'],
            ]);

            if ($resultado) {
                // Obtener el resumen de la cita
                $resumenCita = $this->citaService->obtenerResumenCita($resultado);

                // Redirigir a la vista con el resumen
                $this->pages->render('Cita/resumenCita', [
                    'resumen' => $resumenCita,
                    'mensajeExito' => 'La cita se ha programado correctamente.',
                ]);
            } else {
                $this->pages->render('Cita/programarCita', [
                    'errores' => ['Ocurrió un error al programar la cita. Intenta nuevamente.'],
                ]);
            }
        } else {
            // Delegar la obtención de servicios y empleados al CitaService
            $datosServiciosYEmpleados = $this->citaService->obtenerServiciosYEmpleadosYClientes();

            // Mostrar formulario para programar cita
            $this->pages->render('Cita/programarCita', [
                'servicios' => $datosServiciosYEmpleados['servicios'],
                'empleados' => $datosServiciosYEmpleados['empleados'],
                'clientes' => $datosServiciosYEmpleados['clientes']
            ]);
        }
    }

    public function verResumenCita()
    {
        if (isset($_GET['id'])) {
            $idCita = $_GET['id'];
            $resumenCita = $this->citaService->obtenerResumenCita($idCita);

            // Redirigir a la vista con el resumen
            $this->pages->render('Cita/resumenCita', [
                'resumen' => $resumenCita,
            ]);
        }
    }

    // Mostrar citas del cliente actual
    public function verCitasCliente(): void
    {
        $citas = $this->citaService->obtenerCitasPorCliente($_SESSION['id']);
        $this->pages->render('Cita/misCitas', [
            'citas' => $citas
        ]);
    }

    // Mostrar citas de un empleado (como administrador o empleado)
    public function verCitasEmpleado(): void
    {
        $citas = $this->citaService->obtenerCitasPorEmpleado($_SESSION['id']);
        $this->pages->render('Cita/misCitas', [
            'citas' => $citas
        ]);
    }

    // Cambiar estado de una cita
    public function actualizarEstado(): void
    {
        $mensajeError = '';
        $mensajeExito = '';

        // Verificamos que los parámetros 'id' y 'estado' estén en la URL
        if (isset($_GET['id']) && isset($_GET['estado'])) {
            $idCita = $_GET['id'];
            $estado = $_GET['estado'];

            // Llamamos al servicio para actualizar el estado de la cita
            $resultado = $this->citaService->actualizarEstadoCita($idCita, $estado);

            // Guardamos el mensaje de éxito o error en la sesión
            if ($resultado) {
                $mensajeExito = "El estado de la cita con ID $idCita ha sido actualizado a '$estado'.";
            } else {
                $mensajeError = "Error al actualizar el estado de la cita.";
            }
        } else {
            $mensajeError = "Faltan parámetros necesarios para actualizar el estado de la cita.";
        }

        if ($_SESSION['tipo'] == 'empleado') {
            $citas = $this->citaService->obtenerCitasPorEmpleado($_SESSION['id']);
        } else {
            $citas = $this->citaService->obtenerCitasPorCliente($_SESSION['id']);
        }

        $this->pages->render('Cita/misCitas', [
            'citas' => $citas,
            'mensajeError' => $mensajeError,
            'mensajeExito' => $mensajeExito
        ]);
    }

    public function borrarCita(): void
    {
        $mensajeError = '';
        $mensajeExito = '';

        // Verificar que el parámetro 'id' esté presente en la URL
        if (isset($_GET['id'])) {
            $idCita = intval($_GET['id']); // Convertir a entero para mayor seguridad

            // Intentar borrar la cita
            $resultado = $this->citaService->borrarCita($idCita);

            // Mostrar mensaje según el resultado y redirigir a la vista de citas
            if ($resultado) {
                $mensajeExito = "La cita con ID $idCita ha sido eliminada con éxito.";
            } else {
                $mensajeError = "Error al intentar borrar la cita con ID $idCita.";
            }
        } else {
            $mensajeError = "No se especificó una cita para borrar.";
        }
        $this->pages->render('Cita/misCitas', [
            'mensajeExito' => $mensajeExito,
            'mensajeError' => $mensajeError,
            'citas' => $this->citaService->obtenerCitasPorCliente($_SESSION['id'])
        ]);
    }

    public function finalizarCita(): void
    {
        $idCita = $_GET['id'];
        if ($idCita) {
            // Obtener los detalles de la cita
            $cita = $this->citaService->obtenerPorId($idCita);

            // Verificar que la cita esté en estado "pendiente"
            if ($cita && $cita->getEstado() === 'pendiente') {
                // Mostrar el formulario para finalizar la cita
                $this->pages->render('Cita/finalizarCita', [
                    'cita' => $cita,
                ]);
            } else {
                $this->pages->render('Cita/misCitas', [
                    'mensajeError' => 'La cita no está disponible para finalizar.',
                ]);
            }
        }
    }

    public function guardarDetallesCita(): void
    {
        $idCita = $_POST['id_cita'];
        $detalles = $_POST['detalles'];
        $mensajeError = '';
        $mensajeExito = '';

        if ($idCita && $detalles) {
            // Obtener la cita por ID
            $cita = $this->citaService->obtenerPorId($idCita);

            // Verificar que la cita esté pendiente
            if ($cita && $cita->getEstado() === 'pendiente') {
                // Actualizar el estado y los detalles de la cita
                $resultado = $this->citaService->finalizarCita($idCita, $detalles);

                if ($resultado) {
                    // Mostrar un mensaje de éxito y redirigir al listado de citas
                    $mensajeExito = 'La cita ha sido finalizada correctamente.';
                } else {
                    // Si ocurre un error al actualizar la cita
                    $mensajeError = 'Ocurrió un error al finalizar la cita. Intenta nuevamente.';
                }
            } else {
                // Si la cita no existe o ya no está pendiente
                $mensajeError = 'La cita no está disponible para finalizar.';
            }

            if ($_SESSION['tipo'] == 'empleado') {
                $citas = $this->citaService->obtenerCitasPorEmpleado($_SESSION['id']);
            } else {
                $citas = $this->citaService->obtenerCitasPorCliente($_SESSION['id']);
            }
    
            $this->pages->render('Cita/misCitas', [
                'citas' => $citas,
                'mensajeError' => $mensajeError,
                'mensajeExito' => $mensajeExito
            ]);

        } else {
            // Si no se proporcionaron los datos necesarios
            $this->pages->render('Cita/misCitas', [
                'mensajeError' => 'No se han recibido los datos para finalizar la cita.',
            ]);
        }
    }

    public function mostrarTodos(): void
    {
        $citas = $this->citaService->obtenerTodos();

        $this->pages->render('Cita/mostrarCitas', ['citas' => $citas]);
    }
}
