<h2>Programar Cita</h2>
<?php if (isset($errores) && !empty($errores)): ?>
    <div class="errores">
        <ul>
            <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (isset($mensajeExito)): ?>
    <div class="exito"><?= htmlspecialchars($mensajeExito) ?></div>
<?php endif; ?>

<form action="<?= BASE_URL ?>Cita/programarCita" method="POST">
    <label for="id_servicio">Servicio</label>
    <select id="id_servicio" name="id_servicio" required>
        <option value="">Selecciona un servicio</option>
        <?php foreach ($servicios as $servicio): ?>
            <option value="<?= $servicio->getId() ?>"><?= htmlspecialchars($servicio->getNombre()) ?> - $<?= htmlspecialchars($servicio->getPrecio()) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="id_empleado">Empleado</label>
    <select id="id_empleado" name="id_empleado" required>
        <option value="">Selecciona un empleado</option>
        <?php foreach ($empleados as $empleado): ?>
            <option value="<?= $empleado->getId() ?>"><?= htmlspecialchars($empleado->getNombre()) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="fecha">Fecha</label>
    <input type="date" id="fecha" name="fecha" required min="<?= date('Y-m-d') ?>">

    <label for="hora">Hora</label>
    <input type="time" id="hora" name="hora" required>

    <button type="submit">Programar Cita</button>
</form>
