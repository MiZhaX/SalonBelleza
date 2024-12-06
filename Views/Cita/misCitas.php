<h2>Mis Citas</h2>

<?php if (isset($mensajeExito)): ?>
    <div class="exito"><?= htmlspecialchars($mensajeExito) ?></div>
<?php endif; ?>

<?php if (empty($citas)): ?>
    <p>No tienes citas programadas.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Servicio</th>
                <th>Empleado</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($citas as $cita): ?>
                <tr>
                    <td><?= htmlspecialchars($cita->getFecha()) ?></td>
                    <td><?= htmlspecialchars($cita->getHora()) ?></td>
                    <td><?= htmlspecialchars($cita->getServicio()->getNombre()) ?></td>
                    <td><?= htmlspecialchars($cita->getEmpleado()->getNombre()) ?></td>
                    <td><?= htmlspecialchars($cita->getEstado()) ?></td>
                    <td>
                        <!-- Se podría agregar un botón para cancelar o modificar la cita -->
                        <?php if ($cita->getEstado() !== 'cancelada'): ?>
                            <a href="<?= BASE_URL ?>Cita/cancelarCita/<?= $cita->getId() ?>">Cancelar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
