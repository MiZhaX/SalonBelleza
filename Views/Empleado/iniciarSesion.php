<h2>Iniciar sesión como empleado</h2>
<form action="<?=BASE_URL?>Empleado/iniciarSesion" method="POST">
    <label for="correo">Correo</label>
    <input type="email" id="correo" name="correo" required />
    <label for="password">Contraseña</label>
    <input type="password" id="password" name="password" required />
    <button type="submit">Iniciar sesión</button>
</form>
