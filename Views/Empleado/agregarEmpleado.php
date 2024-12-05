<?php session_start(); ?>
<h2>Registrar nuevo empleado</h2>
<form action="<?= BASE_URL ?>Empleado/registrarEmpleado" method="POST">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" required>

    <label for="correo">Correo:</label>
    <input type="email" name="correo" required>

    <label for="telefono">Teléfono:</label>
    <input type="text" name="telefono" required>

    <label for="especialidad">Especialidad:</label>
    <select name="especialidad" required>
        <?php foreach ($especialidades as $especialidad): ?>
            <option value="<?= $especialidad['id'] ?>"><?= $especialidad['nombre'] ?></option>
        <?php endforeach; ?>
    </select>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" required>

    <button type="submit">Registrar empleado</button>
</form>