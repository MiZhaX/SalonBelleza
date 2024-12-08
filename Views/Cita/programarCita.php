<h2>Programar Cita</h2>
<script src="<?= BASE_URL ?>js/Cita/programarCita.js"></script>

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
    <div>
        <label for="id_servicio">Servicio</label>
        <select id="id_servicio" name="id_servicio" required>
            <option value="">Selecciona un servicio</option>
            <?php foreach ($servicios as $servicio): ?>
                <option value="<?= $servicio->getId() ?>"><?= htmlspecialchars($servicio->getNombre()) ?> - $<?= htmlspecialchars($servicio->getPrecio()) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <?php if ($_SESSION['tipo'] != 'cliente'): ?>
            <label for="id_cliente">Cliente</label>
            <select id="id_cliente" name="id_cliente" required>
                <option value="">Selecciona un cliente</option>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= $cliente->getId() ?>"><?= htmlspecialchars($cliente->getNombre()) ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>
    <div>
        <label for="id_empleado">Empleado</label>
        <select id="id_empleado" name="id_empleado" required disabled>
            <option value="">Selecciona un servicio primero</option>
        </select>
    </div>
    <div>
        <label for="fecha">Fecha</label>
        <input type="date" id="fecha" name="fecha" required min="<?= date('Y-m-d') ?>" max="9999-12-31">

        <label for="hora">Hora</label>
        <input type="time" id="hora" name="hora" required>
    </div>
    <div>
        <button type="submit">Programar Cita</button>
    </div>
</form>