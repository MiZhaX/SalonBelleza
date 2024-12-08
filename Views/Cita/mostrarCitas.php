<h2>Lista de Citas</h2>

<?php if (isset($mensajeExito)): ?>
    <div class="exito"><?= htmlspecialchars($mensajeExito) ?></div>
<?php endif; ?>

<?php if (isset($mensajeError)): ?>
    <div class="error"><?= htmlspecialchars($mensajeError) ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Empleado</th>
            <th>Servicio</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($citas as $cita): ?>
            <tr>
                <td><?= htmlspecialchars($cita->getId()) ?></td>
                <td><?= htmlspecialchars($cita->getIdCliente()) ?></td>
                <td><?= htmlspecialchars($cita->getIdEmpleado()) ?></td>
                <td><?= htmlspecialchars($cita->getIdServicio()) ?></td>
                <td><?= htmlspecialchars($cita->getFecha()) ?></td>
                <td><?= htmlspecialchars($cita->getHora()) ?></td>
                <td><?= htmlspecialchars($cita->getEstado()) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>Cita/verResumenCita&id=<?= $cita->getId() ?>">Resumen</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
