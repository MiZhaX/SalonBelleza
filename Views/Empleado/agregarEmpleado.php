<h2>Registrar nuevo empleado</h2>
<?php if (!empty($errores)): ?>
    <ul>
        <?php foreach ($errores as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($mensajeExito)): ?>
    <p><?= htmlspecialchars($mensajeExito) ?></p>
<?php endif; ?>

<form action="<?= BASE_URL ?>Empleado/registrarEmpleado" method="POST">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required>

    <label for="correo">Correo:</label>
    <input type="email" name="correo" value="<?= htmlspecialchars($datos['correo'] ?? '') ?>" required>

    <label for="telefono">Teléfono:</label>
    <input type="text" name="telefono" value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>" required>

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
