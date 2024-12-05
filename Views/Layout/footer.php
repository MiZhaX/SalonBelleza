    <br>
    <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == "empleado"):?>
        <a href="<?=BASE_URL?>Empleado/cerrarSesion">Cerrar sesión</a>
    <?php else: ?>
        <a href="<?=BASE_URL?>">Volver al inicio</a>
    <?php endif; ?>
    <h2>Footer</h2>
</body>
</html>