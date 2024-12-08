<?php

namespace Controllers;

use Lib\Pages;
use Services\ClienteService;
use Models\Cliente;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class ClienteController
{
    private ClienteService $clienteService;
    private Pages $pages;
    private Cliente $cliente;

    public function __construct()
    {
        $this->clienteService = new ClienteService();
        $this->pages = new Pages();
        $this->cliente = new Cliente();
    }

    // Crear un cliente
    public function crearCliente(): void
    {
        $errores = [];
        $mensajeExito = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitizar los datos de entrada
            $datosSanitizados = [
                'nombre' => filter_var(trim($_POST['nombre']), FILTER_SANITIZE_STRING),
                'correo' => filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL),
                'telefono' => filter_var(trim($_POST['telefono']), FILTER_SANITIZE_STRING),
                'fecha_nacimiento' => trim($_POST['fecha_nacimiento']),
                'password' => trim($_POST['password']),
            ];

            // Validar los datos en el modelo
            $errores = $this->cliente->validarDatos($datosSanitizados);

            $clienteExistente = $this->clienteService->obtenerClientePorCorreo($datosSanitizados['correo']);
            if ($clienteExistente) {
                $errores[] = "El correo ya está registrado.";
            }

            if (empty($errores)) {
                // Cifrar la contraseña antes de guardarla
                $passwordHashed = password_hash($datosSanitizados['password'], PASSWORD_BCRYPT);

                // Intentar crear el cliente a través del servicio
                $resultado = $this->clienteService->crearCliente([
                    'nombre' => $datosSanitizados['nombre'],
                    'correo' => $datosSanitizados['correo'],
                    'telefono' => $datosSanitizados['telefono'],
                    'fecha_nacimiento' => $datosSanitizados['fecha_nacimiento'],
                    'password' => $passwordHashed,
                ]);

                // Si el cliente se crea correctamente, mostrar mensaje de éxito
                if ($resultado) {
                    $mensajeExito = "Cliente creado correctamente. Revisa tu correo para confirmar la cuenta.";
                
                    // Enviar correo de confirmación
                    $urlConfirmacion = BASE_URL . "Cliente/confirmarCuenta?token=" . urlencode($this->clienteService->obtenerClientePorCorreo($datosSanitizados['correo'])->getTokenConfirmacion());
                    $asunto = "Confirma tu cuenta";
                    $mensaje = "<p>Gracias por registrarte. Haz clic en el siguiente enlace para confirmar tu cuenta:</p>";
                    $mensaje .= "<a href='$urlConfirmacion'>$urlConfirmacion</a>";
                
                    $this->enviarCorreo($datosSanitizados['correo'], $asunto, $mensaje);
                } else {
                    $errores[] = "Hubo un problema al crear el cliente. Inténtalo de nuevo.";
                }
            }

            // Si hay errores, mostrar el formulario nuevamente con los errores
            if (!empty($errores)) {
                $this->pages->render('Cliente/agregarCliente', ['errores' => $errores, 'datos' => $datosSanitizados]);
            } else {
                // Si no hay errores, mostrar el mensaje de éxito en la misma página
                $this->pages->render('Cliente/agregarCliente', ['mensajeExito' => $mensajeExito]);
            }
        } else {
            $this->pages->render('Cliente/agregarCliente');
        }
    }

    // Log in de un cliente
    public function iniciarSesion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;

            // Verificar si los datos requeridos están presentes
            if (empty($datos['correo'])) {
                $errores[] = "El correo es obligatorio.";
            }

            if (empty($datos['password'])) {
                $errores[] = "La contraseña es obligatoria.";
            }

            // Intentar obtener al cliente
            $cliente = $this->clienteService->obtenerClientePorCorreo($datos['correo']);

            if($cliente->getTokenConfirmacion() == null){
                if ($cliente && password_verify($datos['password'], $cliente->getPassword())) {
                    // Iniciar sesión
                    session_start();
                    $_SESSION['tipo'] = "cliente";
                    $_SESSION['nombre'] = $cliente->getNombre();
                    $_SESSION['id'] = $cliente->getId();

                    // Redirigir a la página principal
                    $this->pages->render('Layout/principal');
                } else {
                    $errores[] = "Correo o contraseña incorrectos.";
                    $this->pages->render('Cliente/iniciarSesion', ['errores' => $errores]);
                }
            } else {
                $errores[] = "Cuenta no confirmada";
                $this->pages->render('Cliente/iniciarSesion', ['errores' => $errores]);
            }
        } else {
            $this->pages->render('Cliente/iniciarSesion');
        }
    }


    public function cerrarSesion()
    {
        session_start();
        session_unset();
        session_destroy();

        $this->pages->render('Layout/principal');
    }

    private function enviarCorreo(string $correo, string $asunto, string $mensaje): void {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;
        $mail->Username = 'mbonelortiz@gmail.com'; // Cambia por tu correo
        $mail->Password = 'vwkdgauvdbsrlfid'; // Cambia por tu contraseña
        $mail->setFrom('mbonelortiz@gmail.com', 'SalónDeBelleza');
        $mail->addAddress($correo);
        $mail->Subject = $asunto;
        $mail->msgHTML($mensaje);
    
        if (!$mail->send()) {
            error_log('Error al enviar correo: ' . $mail->ErrorInfo);
        }
    }

    public function confirmarCuenta(): void {
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            $cliente = $this->clienteService->obtenerClientePorToken($token);
    
            if ($cliente) {
                $cliente->setTokenConfirmacion(null); // Elimina el token
                $resultado = $this->clienteService->activarCuenta($cliente);
    
                if ($resultado) {
                    $this->pages->render('Cliente/confirmarCuenta', ['mensajeExito' => 'Cuenta confirmada exitosamente.']);
                } else {
                    $this->pages->render('Cliente/confirmarCuenta', ['mensajeError' => 'Error al confirmar la cuenta.']);
                }
            } else {
                $this->pages->render('Cliente/confirmarCuenta', ['mensajeError' => 'Token inválido o cuenta ya confirmada.']);
            }
        }
    }    
}
