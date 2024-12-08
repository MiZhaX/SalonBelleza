<h2>Registrarse</h2>

<?php if (isset($mensajeExito)): ?>
    <div class="exito">
        <?= htmlspecialchars($mensajeExito); ?>
    </div>
<?php endif; ?>

<?php if (isset($errores) && !empty($errores)): ?>
    <div class="error">
            <?php foreach ($errores as $error): ?>
                <p><?= htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="">
    <div>
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="<?= isset($datos['nombre']) ? htmlspecialchars($datos['nombre']) : ''; ?>" required />
    </div>

    <div>
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" value="<?= isset($datos['correo']) ? htmlspecialchars($datos['correo']) : ''; ?>" required />
    </div>

    <div>
        <label for="telefono">Teléfono</label>
        <input type="text" id="telefono" name="telefono" value="<?= isset($datos['telefono']) ? htmlspecialchars($datos['telefono']) : ''; ?>" required />
    </div>

    <div>
        <label for="fecha_nacimiento">Fecha de nacimiento</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= isset($datos['fecha_nacimiento']) ? htmlspecialchars($datos['fecha_nacimiento']) : ''; ?>" required />
    </div>

    <div>
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required />
    </div>

    <div>
        <button type="submit">Registrar cliente</button>
    </div>
</form>
