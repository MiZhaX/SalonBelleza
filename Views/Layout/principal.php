<?php if (!isset($_SESSION['id'])): ?>
    <h2>Bienvenido al salón de belleza.</h2>
    <h2>¿ERES CLIENTE?</h2>
    <ul>
        <li><a href="<?= BASE_URL ?>Cliente/crearCliente">Registrarse como cliente</a></li>
        <li><a href="<?= BASE_URL ?>Cliente/iniciarSesion">Iniciar sesión como cliente</a></li>
    </ul>

    <h2>¿ERES EMPLEADO?</h2>
    <ul>
        <li><a href="<?= BASE_URL ?>Empleado/iniciarSesion">Iniciar sesión como empleado</a></li>
    </ul>

<?php else: ?>
    <h2>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?>.</h2>
    <ul>
    <li>
        <a href="<?= BASE_URL ?>Cita/programarCita">Programar cita</a></li>
        <?php if ($_SESSION['tipo'] === 'administrador'): ?>
            <li><a href="<?= BASE_URL ?>Empleado/registrarEmpleado">Registrar nuevo empleado</a></li>
            <li><a href="<?= BASE_URL ?>Cliente/crearCliente">Registrar nuevo cliente</a></li>
            <li><a href="<?= BASE_URL ?>Cita/mostrarTodos">Ver todas las citas</a></li>
            <li><a href="<?= BASE_URL ?>Empleado/mostrarTodos">Ver empleados</a></li>
        <?php elseif ($_SESSION['tipo'] === 'empleado'): ?>
            <li><a href="<?= BASE_URL ?>Cita/verCitasEmpleado">Ver mis citas</a></li>
            <li><a href="<?= BASE_URL ?>Cliente/crearCliente">Registrar nuevo cliente</a></li>
        <?php elseif ($_SESSION['tipo'] === 'cliente'): ?>
            <li><a href="<?= BASE_URL ?>Cita/verCitasCliente">Ver mis citas</a></li>
        <?php endif; ?>
        <li><a href="<?= BASE_URL ?>Servicio/mostrarTodos">Ver servicios</a></li>
    </ul>
    <a href="<?= BASE_URL . ($_SESSION['tipo'] === 'cliente' ? 'Cliente/cerrarSesion' : 'Empleado/cerrarSesion') ?>">Cerrar sesión</a>
<?php endif; ?>
