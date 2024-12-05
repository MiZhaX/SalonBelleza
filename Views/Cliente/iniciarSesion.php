<h2>Iniciar sesión</h2>
<form method="POST" action="<?=BASE_URL?>Cliente/iniciarSesion">
    <label for="correo">Correo:</label>
    <input type="email" id="correo" name="correo" required>
    
    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="password" required>
    
    <button type="submit">Iniciar sesión</button>
</form>
