<h2>Mis Citas</h2>

<?php if (isset($mensajeExito)): ?>
    <div class="exito"><?= htmlspecialchars($mensajeExito) ?></div>
<?php endif; ?>

<?php if (isset($mensajeError)): ?>
    <div class="error"><?= htmlspecialchars($mensajeError) ?></div>
<?php endif; ?>

<?php if (count($citas) == 0): ?>
    <p>No tienes citas asignadas</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Servicio</th>
                <?php if ($_SESSION['tipo'] === 'empleado'): ?>
                    <th>Cliente</th>
                <?php else: ?>
                    <th>Empleado</th>
                <?php endif; ?>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($citas as $cita): ?>
                <tr>
                    <td><?= htmlspecialchars($cita->getFecha()) ?></td>
                    <td><?= htmlspecialchars($cita->getHora()) ?></td>
                    <td><?= htmlspecialchars($cita->getIdServicio()) ?></td>

                    <?php if ($_SESSION['tipo'] === 'empleado'): ?>
                        <td><?= htmlspecialchars($cita->getIdCliente()) ?></td>
                    <?php else: ?>
                        <td><?= htmlspecialchars($cita->getIdEmpleado()) ?></td>
                    <?php endif; ?>

                    <td><?= htmlspecialchars($cita->getEstado()) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>Cita/verResumenCita&id=<?= $cita->getId() ?>">Resumen</a>
                        <?php if ($cita->getEstado() == 'pendiente'): ?>
                            <?php if ($_SESSION['tipo'] === 'empleado'): ?>
                                 / <a href="<?= BASE_URL ?>Cita/finalizarCita&id=<?= $cita->getId() ?>">Finalizar</a>
                            <?php endif; ?>
                             / <a href="<?= BASE_URL ?>Cita/actualizarEstado&id=<?= $cita->getId() ?>&estado=cancelada">Cancelar</a>
                        <?php endif; ?>
                        <?php if ($cita->getEstado() == 'cancelada' && $_SESSION['tipo'] === 'empleado'): ?>
                             / <a href="<?= BASE_URL ?>Cita/borrarCita&id=<?= $cita->getId() ?>">Borrar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>