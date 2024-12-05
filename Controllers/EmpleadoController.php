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

            if (!isset($datos['correo'], $datos['password'])) {
                echo "Faltan datos requeridos";
                return;
            }

            $empleado = $this->empleadoService->obtenerEmpleadoPorCorreo($datos['correo']);

            if ($empleado && password_verify($datos['password'], $empleado->getPassword())) {
                // Iniciar sesión
                session_start();
                $_SESSION['tipo'] = "empleado";
                $_SESSION['nombre'] = $empleado->getNombre();
                $_SESSION['id'] = $empleado->getId();
                $_SESSION['especialidad'] = $empleado->getidEspecialidad();

                // Redirigir al dashboard del administrador si es administrador
                if ($_SESSION['especialidad'] === 11) {
                    $this->pages->render('Empleado/adminDashboard');
                } else {
                    $this->pages->render('Empleado/empleadoDashboard');
                }
                exit;
            } else {
                echo "Correo o contraseña incorrectos";
            }
        } else {
            $this->pages->render('Empleado/iniciarSesion');
        }
    }

    public function cerrarSesion(){
        session_start(); 
        session_unset();
        session_destroy();

        $this->pages->render('Layout/principal');
    }

    public function registrarEmpleado(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recoger los datos del formulario
        $datos = $_POST;

        // Validación básica
        if (!isset($datos['nombre'], $datos['correo'], $datos['telefono'], $datos['especialidad'], $datos['password'])) {
            echo "Faltan datos requeridos";
            return;
        }

        // Validar que el correo y el teléfono no estén ya registrados
        $empleadoExistente = $this->empleadoService->obtenerEmpleadoPorCorreo($datos['correo']);
        if ($empleadoExistente) {
            echo "El correo ya está registrado.";
            return;
        }

        // Cifrar la contraseña
        $passwordCifrada = password_hash($datos['password'], PASSWORD_BCRYPT);

        // Crear el empleado
        $empleado = new Empleado(
            nombre: $datos['nombre'],
            correo: $datos['correo'],
            telefono: $datos['telefono'],
            password: $passwordCifrada,
            idEspecialidad: $datos['especialidad']
        );

        // Llamar al servicio para insertar el empleado
        $resultado = $this->empleadoService->crearEmpleado($empleado);

        // Verificar el resultado
        if ($resultado) {
            echo "Empleado registrado con éxito.";
        } else {
            echo "Hubo un error al registrar el empleado.";
        }
    } else {
        $especialidades = $this->especialidadService->obtenerTodas();

        $this->pages->render('Empleado/agregarEmpleado', ['especialidades' => $especialidades]);
    }
}
}
