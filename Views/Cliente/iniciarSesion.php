<h2>Iniciar sesión como cliente</h2>
<!-- Mostrar errores si los hay -->
<?php if (isset($errores) && !empty($errores)): ?>
    <ul style="color: red;">
        <?php foreach ($errores as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>Cliente/iniciarSesion">
    <label for="correo">Correo:</label>
    <input type="email" id="correo" name="correo" required>
    
    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="password" required>
    
    <button type="submit">Iniciar sesión</button>
</form>
