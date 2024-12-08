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
    <div>
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required>
    </div>
    <div>
        <label for="correo">Correo:</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($datos['correo'] ?? '') ?>" required>
    </div>
    <div>
        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>" required>
    </div>
    <div>
        <label for="especialidad">Especialidad:</label>
        <select name="especialidad" required>
            <?php foreach ($especialidades as $especialidad): ?>
                <option value="<?= $especialidad['id'] ?>"><?= $especialidad['nombre'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" required>
    </div>
    <div>
        <button type="submit">Registrar empleado</button>
    </div>
</form>