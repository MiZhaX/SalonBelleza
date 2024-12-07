<h2>Resumen de la Cita</h2>

<?php if (isset($mensajeExito)): ?>
    <div class="exito"><?= htmlspecialchars($mensajeExito) ?></div>
<?php endif; ?>

<table>
    <tr>
        <td><strong>Servicio:</strong></td>
        <td><?= htmlspecialchars($resumen['servicio']) ?></td>
    </tr>
    <tr>
        <td><strong>Precio:</strong></td>
        <td>$<?= htmlspecialchars($resumen['precio']) ?></td>
    </tr>
    <tr>
        <td><strong>Duración:</strong></td>
        <td><?= htmlspecialchars($resumen['duracion']) ?> minutos</td>
    </tr>
    <tr>
        <td><strong>Empleado:</strong></td>
        <td><?= htmlspecialchars($resumen['empleado']) ?></td>
    </tr>
    <tr>
        <td><strong>Cliente:</strong></td>
        <td><?= htmlspecialchars($resumen['cliente']) ?></td>
    </tr>
    <tr>
        <td><strong>Fecha:</strong></td>
        <td><?= htmlspecialchars($resumen['fecha']) ?></td>
    </tr>
    <tr>
        <td><strong>Hora:</strong></td>
        <td><?= htmlspecialchars($resumen['hora']) ?></td>
    </tr>
    <tr>
        <td><strong>Precio Total:</strong></td>
        <td>$<?= htmlspecialchars($resumen['precioTotal']) ?></td>
    </tr>
</table>

<?php if ($_SESSION['tipo'] === 'empleado'): ?>
    <a href="<?= BASE_URL ?>Cita/verCitasEmpleado">Volver a mis citas</a>
<?php else: ?>
    <a href="<?= BASE_URL ?>Cita/verCitasCliente">Volver a mis citas</a>
<?php endif; ?>