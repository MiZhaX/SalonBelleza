<?php

namespace Controllers;

use Services\EmpleadoService;
use Models\Empleado;
use Lib\Pages;
use Services\EspecialidadService;

class EmpleadoController
{
    private EmpleadoService $empleadoService;
    private EspecialidadService $especialidadService;
    private Pages $pages;

    public function __construct()
    {
        $this->empleadoService = new EmpleadoService();
        $this->especialidadService = new EspecialidadService();
        $this->pages = new Pages();
    }

    // Log in de un empleado
    public function iniciarSesion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;
            $errores = [];

            // Verificar si los datos requeridos están presentes
            if (empty($datos['correo'])) {
                $errores[] = "El correo es obligatorio.";
            }

            if (empty($datos['password'])) {
                $errores[] = "La contraseña es obligatoria.";
            }

            if (empty($errores)) {
                // Intentar obtener al empleado
                $empleado = $this->empleadoService->obtenerEmpleadoPorCorreo($datos['correo']);

                if ($empleado && password_verify($datos['password'], $empleado->getPassword())) {
                    // Iniciar sesión
                    session_start();
                    $_SESSION['tipo'] = $empleado->getIdEspecialidad() == 11 ? "administrador" : "empleado";
                    $_SESSION['nombre'] = $empleado->getNombre();
                    $_SESSION['id'] = $empleado->getId();
                    $_SESSION['especialidad'] = $empleado->getIdEspecialidad();

                    // Redirigir al layout principal
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


    public function cerrarSesion()
    {
        session_start();
        session_unset();
        session_destroy();

        $this->pages->render('Layout/principal');
    }

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

            // Validar los datos en el modelo
            $empleado = new Empleado(
                nombre: $datosSanitizados['nombre'],
                correo: $datosSanitizados['correo'],
                telefono: $datosSanitizados['telefono'],
                password: $datosSanitizados['password'], // Sin cifrar aún
                idEspecialidad: $datosSanitizados['especialidad']
            );

            $errores = $empleado->validarDatos();

            // Verificar si el correo ya está registrado
            $empleadoExistente = $this->empleadoService->obtenerEmpleadoPorCorreo($datosSanitizados['correo']);
            if ($empleadoExistente) {
                $errores[] = "El correo ya está registrado.";
            }

            // Continuar si no hay errores
            if (empty($errores)) {
                // Cifrar la contraseña
                $passwordCifrada = password_hash($datosSanitizados['password'], PASSWORD_BCRYPT);
                $empleado->setPassword($passwordCifrada);

                // Intentar crear el empleado
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
}
