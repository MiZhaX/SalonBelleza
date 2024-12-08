<?php

namespace Controllers;

use Lib\Pages;
use Services\CitaService;
use Models\Cita;
use DateTime;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

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

    // Programar una cita
    public function programarCita(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;

            // Obtener el id del cliente
            if ($_SESSION['tipo'] != 'cliente') {
                $idCliente = $datos['id_cliente'];
            } else {
                $idCliente = $_SESSION['id'];
            }

            // Crear la cita
            $cita = new Cita(
                idCliente: $idCliente,
                idEmpleado: $datos['id_empleado'],
                idServicio: $datos['id_servicio'],
                fecha: $datos['fecha'],
                hora: $datos['hora'],
            );

            // Validar los datos de la cita 
            $errores = $this->citaService->validarDatosCita($cita);

            if (!empty($errores)) {
                // Obtener los datos necesarios para mostrarlos en el formulario
                $datosServiciosYEmpleados = $this->citaService->obtenerServiciosYEmpleadosYClientes();

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
                $urlResumen = BASE_URL . "Cita/verResumenCita?id=" . $resultado;

                // Crear la estructura del correo electronico
                $asunto = "Resumen de tu cita en SalonBelleza";
                $mensaje = "<p>Gracias por realizar una cita con nosotros. Aquí tienes el resumen de tu cita:</p>";
                $mensaje .= "<ul>";
                $mensaje .= "<li><strong>Cliente:</strong> {$resumenCita['cliente']}</li>";
                $mensaje .= "<li><strong>Servicio:</strong> {$resumenCita['servicio']}</li>";
                $mensaje .= "<li><strong>Empleado:</strong> {$resumenCita['empleado']}</li>";
                $mensaje .= "<li><strong>Fecha:</strong> {$resumenCita['fecha']}</li>";
                $mensaje .= "<li><strong>Hora:</strong> {$resumenCita['hora']}</li>";
                $mensaje .= "</ul>";
                $mensaje .= "Ver resumen online:";
                $mensaje .= "<a href='$urlResumen'>Resumen Online</a>";
                $mensaje .= "<p>Te esperamos en nuestro salon de belleza. ¡Gracias por confiar en nosotros!</p>";

                // Enviar el correo
                $correoCliente = $this->citaService->obtenerCorreoClientePorId($idCliente);
                $this->enviarCorreo($correoCliente, $asunto, $mensaje);

                $this->pages->render('Cita/resumenCita', [
                    'resumen' => $resumenCita,
                    'mensajeExito' => 'La cita se ha programado correctamente. Se ha enviado un correo con el resumen de la cita',
                ]);
            } else {
                $this->pages->render('Cita/programarCita', [
                    'errores' => ['Ocurrió un error al programar la cita. Intenta nuevamente.'],
                ]);
            }
        } else {
            // Obtener los datos necesarios para mostrarlos en el formulario
            $datosServiciosYEmpleados = $this->citaService->obtenerServiciosYEmpleadosYClientes();

            // Mostrar formulario para programar la cita
            $this->pages->render('Cita/programarCita', [
                'servicios' => $datosServiciosYEmpleados['servicios'],
                'empleados' => $datosServiciosYEmpleados['empleados'],
                'clientes' => $datosServiciosYEmpleados['clientes']
            ]);
        }
    }

    // Ver el resumen de una cita
    public function verResumenCita()
    {
        // Obtener el Id de la cita
        if (isset($_GET['id'])) {
            $idCita = $_GET['id'];

            // Obtener el resumen de la cita
            $resumenCita = $this->citaService->obtenerResumenCita($idCita);

            $this->pages->render('Cita/resumenCita', [
                'resumen' => $resumenCita,
            ]);
        }
    }

    // Mostrar citas del cliente (Por sesión)
    public function verCitasCliente(): void
    {
        $citas = $this->citaService->obtenerCitasPorCliente($_SESSION['id']);
        $this->pages->render('Cita/misCitas', [
            'citas' => $citas
        ]);
    }

    // Mostrar citas del empleado (Por sesión)
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

        // Obtener la id de la cita y el estado al que se quiere actualizar
        if (isset($_GET['id']) && isset($_GET['estado'])) {
            $idCita = $_GET['id'];
            $estado = $_GET['estado'];

            // Actualizamos el estado de la cita
            $resultado = $this->citaService->actualizarEstadoCita($idCita, $estado);

            if ($resultado) {
                $mensajeExito = "El estado de la cita con ID $idCita ha sido actualizado a '$estado'.";
            } else {
                $mensajeError = "Error al actualizar el estado de la cita.";
            }
        } else {
            $mensajeError = "Faltan parámetros necesarios para actualizar el estado de la cita.";
        }

        // Dependiendo del tipo de la sesión, volvemos a mostrar las citas
        if ($_SESSION['tipo'] == 'empleado') {
            $citas = $this->citaService->obtenerCitasPorEmpleado($_SESSION['id']);
        } else {
            $citas = $this->citaService->obtenerCitasPorCliente($_SESSION['id']);
        }

        if ($_SESSION['tipo'] == 'administrador') {
            $citas = $this->citaService->obtenerTodos();

            $this->pages->render('Cita/mostrarCitas', ['citas' => $citas, 'mensajeError' => $mensajeError, 'mensajeExito' => $mensajeExito]);
        } else {
            if ($mensajeError != '' && $mensajeExito != '') {
                $this->pages->render('Cita/misCitas', ['citas' => $citas, 'mensajeError' => $mensajeError, 'mensajeExito' => $mensajeExito]);
            } elseif ($mensajeError == '' && $mensajeExito != '') {
                $this->pages->render('Cita/misCitas', ['citas' => $citas, 'mensajeExito' => $mensajeExito]);
            } else {
                $this->pages->render('Cita/misCitas', ['citas' => $citas, 'mensajeError' => $mensajeError]);
            }
        }
    }

    // Borrar una cita
    public function borrarCita(): void
    {
        $mensajeError = '';
        $mensajeExito = '';

        // Obtener la id de la cita que se quiere borrar
        if (isset($_GET['id'])) {
            $idCita = intval($_GET['id']); 

            // Borrar la cita
            $resultado = $this->citaService->borrarCita($idCita);

            if ($resultado) {
                $mensajeExito = "La cita con ID $idCita ha sido eliminada con éxito.";
            } else {
                $mensajeError = "Error al intentar borrar la cita con ID $idCita.";
            }
        } else {
            $mensajeError = "No se especificó una cita para borrar.";
        }

        // Dependiendo del tipo de la sesión y los errores, volvemos a mostrar las citas
        if ($mensajeError != '' && $mensajeExito != '') {
            if ($_SESSION['tipo'] == 'administrador') {
                $citas = $this->citaService->obtenerTodos();

                $this->pages->render('Cita/mostrarCitas', ['citas' => $citas, 'mensajeExito' => $mensajeExito, 'mensajeError' => $mensajeError]);
            } else {
                $this->pages->render('Cita/misCitas', ['mensajeExito' => $mensajeExito, 'mensajeError' => $mensajeError, 'citas' => $this->citaService->obtenerCitasPorEmpleado($_SESSION['id'])]);
            }
        } elseif ($mensajeError == '' && $mensajeExito != '') {
            if ($_SESSION['tipo'] == 'administrador') {
                $citas = $this->citaService->obtenerTodos();

                $this->pages->render('Cita/mostrarCitas', ['citas' => $citas, 'mensajeExito' => $mensajeExito]);
            } else {
                $this->pages->render('Cita/misCitas', ['mensajeExito' => $mensajeExito, 'citas' => $this->citaService->obtenerCitasPorEmpleado($_SESSION['id'])]);
            }
        } else {
            if ($_SESSION['tipo'] == 'administrador') {
                $citas = $this->citaService->obtenerTodos();

                $this->pages->render('Cita/mostrarCitas', ['citas' => $citas, 'mensajeError' => $mensajeError]);
            } else {
                $this->pages->render('Cita/misCitas', ['mensajeError' => $mensajeError, 'citas' => $this->citaService->obtenerCitasPorEmpleado($_SESSION['id'])]);
            }
        }
    }

    // Completar una cita
    public function finalizarCita(): void
    {
        $idCita = $_GET['id'];
        $errores = null;

        if ($idCita) {
            // Obtener los detalles de la cita
            $cita = $this->citaService->obtenerPorId($idCita);

            if ($cita) {
                $fechaHoraCita = new DateTime($cita->getFecha() . ' ' . $cita->getHora());
                $fechaHoraActual = new DateTime();

                // Validar el estado y la fecha de la cita
                if ($cita->getEstado() !== 'pendiente') {
                    $errores = 'La cita no está disponible para finalizar.';
                } elseif ($fechaHoraCita >= $fechaHoraActual) {
                    $errores = 'No se puede finalizar una cita que aún no ha ocurrido.';
                }
            } else {
                $errores = 'La cita no existe.';
            }
        } else {
            $errores = 'No se especificó una cita válida.';
        }

        // Dependiendo del tipo de la sesión y los errores, volvemos a mostrar las citas
        if ($errores != '') {
            if ($_SESSION['tipo'] == 'administrador') {
                $citas = $this->citaService->obtenerTodos();

                $this->pages->render('Cita/mostrarCitas', ['citas' => $citas, 'mensajeError' => $errores]);
            } else {
                $this->pages->render('Cita/misCitas', [
                    'mensajeError' => $errores,
                    'citas' => $this->citaService->obtenerCitasPorEmpleado($_SESSION['id']),
                ]);
            }
        } else {
            if ($_SESSION['tipo'] == 'administrador') {
                $citas = $this->citaService->obtenerTodos();

                $this->pages->render('Cita/mostrarCitas', ['citas' => $citas]);
            } else {
                $this->pages->render('Cita/finalizarCita', [
                    'cita' => $cita,
                    'citas' => $this->citaService->obtenerCitasPorEmpleado($_SESSION['id']),
                ]);
            }
        }
    }

    // Almacenar los detalles de una cita
    public function guardarDetallesCita(): void
    {
        // Obtener los datos del formulario
        $idCita = $_POST['id_cita'];
        $detalles = $_POST['detalles'];
        $mensajeError = '';
        $mensajeExito = '';

        if ($idCita && $detalles) {
            // Obtener la cita por su Id
            $cita = $this->citaService->obtenerPorId($idCita);

            // Verificar que la cita esté pendiente
            if ($cita && $cita->getEstado() === 'pendiente') {
                // Actualizar el estado y los detalles de la cita
                $resultado = $this->citaService->finalizarCita($idCita, $detalles);

                if ($resultado) {
                    $mensajeExito = 'La cita ha sido finalizada correctamente.';
                } else {
                    $mensajeError = 'Ocurrió un error al finalizar la cita. Intenta nuevamente.';
                }
            } else {
                $mensajeError = 'La cita no está disponible para finalizar.';
            }

            // Dependiendo del tipo de la sesión y los errores, volvemos a mostrar las citas
            if ($_SESSION['tipo'] == 'empleado') {
                $citas = $this->citaService->obtenerCitasPorEmpleado($_SESSION['id']);
            } else {
                $citas = $this->citaService->obtenerCitasPorCliente($_SESSION['id']);
            }

            if ($_SESSION['tipo'] == 'administrador') {
                $citas = $this->citaService->obtenerTodos();

                if ($mensajeError != '') {
                    $this->pages->render('Cita/mostrarCitas', ['citas' => $citas, 'mensajeError' => $mensajeError, 'mensajeExito' => $mensajeExito]);
                } else {
                    $this->pages->render('Cita/mostrarCitas', ['citas' => $citas, 'mensajeExito' => $mensajeExito]);
                }
            } else {
                if ($mensajeError != '') {
                    $this->pages->render('Cita/misCitas', ['citas' => $citas, 'mensajeError' => $mensajeError, 'mensajeExito' => $mensajeExito]);
                } else {
                    $this->pages->render('Cita/misCitas', ['citas' => $citas, 'mensajeExito' => $mensajeExito]);
                }
            }
        } else {
            // Si no se proporcionaron los datos necesarios
            if ($_SESSION['tipo'] == 'administrador') {
                $citas = $this->citaService->obtenerTodos();

                $this->pages->render('Cita/mostrarCitas', ['citas' => $citas, 'mensajeError' => 'No se han recibido los datos para finalizar la cita.']);
            } else {
                $this->pages->render('Cita/misCitas', [
                    'mensajeError' => 'No se han recibido los datos para finalizar la cita.',
                ]);
            }
        }
    }

    // Mostrar todas las citas
    public function mostrarTodos(): void
    {
        $citas = $this->citaService->obtenerTodos();

        $this->pages->render('Cita/mostrarCitas', ['citas' => $citas]);
    }

    // Enviar correo (Para enviar resumen de las citas)
    private function enviarCorreo(string $correo, string $asunto, string $mensaje): void
    {
        // Variables requeridas para enviar un correo
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_DIR;
        $mail->Password = EMAIL_PASS;
        $mail->setFrom(EMAIL_DIR, 'SalonDeBelleza');
        $mail->addAddress($correo);
        $mail->Subject = $asunto;
        $mail->msgHTML($mensaje);

        // Verificar que el correo se envíe correctamente
        if (!$mail->send()) {
            error_log('Error al enviar correo: ' . $mail->ErrorInfo);
        }
    }
}
