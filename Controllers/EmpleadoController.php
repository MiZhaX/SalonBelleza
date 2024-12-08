<?php

namespace Controllers;

use Services\EmpleadoService;
use Models\Empleado;
use Lib\Pages;
use Services\EspecialidadService;
use Services\ServicioService;

class EmpleadoController
{
    private EmpleadoService $empleadoService;
    private EspecialidadService $especialidadService;
    private ServicioService $servicioService;
    private Pages $pages;

    public function __construct()
    {
        $this->empleadoService = new EmpleadoService();
        $this->especialidadService = new EspecialidadService();
        $this->servicioService = new ServicioService();
        $this->pages = new Pages();
    }

    // Registrar un empleado
    public function registrarEmpleado(): void
    {
        $errores = [];
        $mensajeExito = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Saneamiento de datos
            $datosSanitizados = [
                'nombre' => filter_var(trim($_POST['nombre']), FILTER_SANITIZE_STRING),
                'correo' => filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL),
                'telefono' => filter_var(trim($_POST['telefono']), FILTER_SANITIZE_STRING),
                'especialidad' => intval($_POST['especialidad']),
                'password' => trim($_POST['password']),
            ];

            // Validar los datos 
            $empleado = new Empleado(
                nombre: $datosSanitizados['nombre'],
                correo: $datosSanitizados['correo'],
                telefono: $datosSanitizados['telefono'],
                password: $datosSanitizados['password'],
                idEspecialidad: $datosSanitizados['especialidad']
            );

            $errores = $empleado->validarDatos();

            // Verificar si el correo ya está registrado
            $empleadoExistente = $this->empleadoService->obtenerPorCorreo($datosSanitizados['correo']);
            if ($empleadoExistente) {
                $errores[] = "El correo ya está registrado.";
            }

            // Continuar si no hay errores
            if (empty($errores)) {
                // Cifrar la contraseña
                $passwordCifrada = password_hash($datosSanitizados['password'], PASSWORD_BCRYPT);
                $empleado->setPassword($passwordCifrada);

                // Crear el empleado
                $resultado = $this->empleadoService->crearEmpleado($empleado);

                if ($resultado) {
                    $mensajeExito = "Empleado registrado con éxito.";
                } else {
                    $errores[] = "Hubo un problema al registrar el empleado. Inténtalo de nuevo.";
                }
            }
        }

        // Obtener las especialidades para el formulario
        $especialidades = $this->especialidadService->obtenerTodas();

        // Renderizar la vista con errores, mensaje de éxito o el formulario
        $this->pages->render('Empleado/agregarEmpleado', [
            'errores' => $errores,
            'mensajeExito' => $mensajeExito,
            'especialidades' => $especialidades
        ]);
    }

    // Iniciar sesión como empleado
    public function iniciarSesion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener datos del formulario
            $datos = $_POST;
            $errores = [];

            // Verificar que se han introducido los datos
            if (empty($datos['correo'])) {
                $errores[] = "El correo es obligatorio.";
            }

            if (empty($datos['password'])) {
                $errores[] = "La contraseña es obligatoria.";
            }

            if (empty($errores)) {
                // Obtener al empleado por el correo
                $empleado = $this->empleadoService->obtenerPorCorreo($datos['correo']);

                // Si los datos coinciden
                if ($empleado && password_verify($datos['password'], $empleado->getPassword())) {
                    // Iniciar sesión
                    session_start();
                    $_SESSION['tipo'] = $empleado->getIdEspecialidad() == 11 ? "administrador" : "empleado";
                    $_SESSION['nombre'] = $empleado->getNombre();
                    $_SESSION['id'] = $empleado->getId();
                    $_SESSION['especialidad'] = $empleado->getIdEspecialidad();

                    // Redirigir a la página principal
                    $this->pages->render('Layout/principal');
                    exit;
                } else {
                    $errores[] = "Correo o contraseña incorrectos.";
                }
            }

            // Mostrar la página de inicio de sesión con errores si los hay
            $this->pages->render('Empleado/iniciarSesion', ['errores' => $errores]);
        } else {
            // Renderizar la página de inicio de sesión sin errores
            $this->pages->render('Empleado/iniciarSesion');
        }
    }

    // Cerrar la sesión
    public function cerrarSesion()
    {
        session_start();
        session_unset();
        session_destroy();

        $this->pages->render('Layout/principal');
    }

    // Obtener todos los empleados de una especialidad (Para programar citas)
    public function obtenerEmpleadosPorEspecialidad(): void
    {
        if (isset($_GET['id_servicio'])) {
            $idServicio = intval($_GET['id_servicio']);

            // Obtener la especialidad del servicio
            $servicio = $this->servicioService->obtenerPorId($idServicio);

            if ($servicio) {
                $idEspecialidad = $servicio->getIdEspecialidad();

                // Obtener empleados con esa especialidad
                $empleados = $this->empleadoService->obtenerPorEspecialidad($idEspecialidad);

                header('Content-Type: application/json');
                echo json_encode($empleados);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Servicio no encontrado.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'No se proporcionó el ID del servicio.']);
        }
    }

    // Mostrar todos los empleados
    public function mostrarTodos(): void
    {
        $empleados = $this->empleadoService->obtenerTodos();

        $this->pages->render('Empleado/mostrarEmpleados', ['empleados' => $empleados, 'especialidadService' => $this->especialidadService]);
    }

    // Despedir a un empleado
    public function despedirEmpleado(): void
    {
        $mensajeExito = '';
        $mensajeError = '';

        // Verificar que el ID del empleado esté presente en la URL
        if (isset($_GET['id'])) {
            $idEmpleado = $_GET['id'];

            // Despedir al empleado usando el servicio
            $resultado = $this->empleadoService->despedirEmpleado($idEmpleado);

            if ($resultado) {
                $mensajeExito = "El empleado con ID $idEmpleado ha sido despedido exitosamente.";
            } else {
                $mensajeError = "Ocurrió un error al intentar despedir al empleado con ID $idEmpleado.";
            }
        } else {
            $mensajeError = "No se especificó un empleado para despedir.";
        }

        // Obtener la lista actualizada de empleados y redirigir a la vista
        $empleados = $this->empleadoService->obtenerTodos();
        $this->pages->render('Empleado/mostrarEmpleados', [
            'empleados' => $empleados,
            'mensajeExito' => $mensajeExito,
            'mensajeError' => $mensajeError,
            'especialidadService' => $this->especialidadService
        ]);
    }
}
