<h2>Iniciar sesión como empleado</h2>
<!-- Mostrar errores si existen -->
<?php if (isset($errores) && !empty($errores)): ?>
    <div class="error">
        <?php foreach ($errores as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form action="<?= BASE_URL ?>Empleado/iniciarSesion" method="POST">
    <label for="correo">Correo:</label>
    <input type="email" id="correo" name="correo" required />

    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="password" required />

    <button type="submit">Iniciar sesión</button>
</form>

