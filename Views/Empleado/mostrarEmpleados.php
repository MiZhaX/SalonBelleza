<h2>Lista de Empleados</h2>

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
            <th>Nombre</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Especialidad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($empleados as $empleado): ?>
            <tr>
                <td><?= htmlspecialchars($empleado->getId()) ?></td>
                <td><?= htmlspecialchars($empleado->getNombre()) ?></td>
                <td><?= htmlspecialchars($empleado->getCorreo()) ?></td>
                <td><?= htmlspecialchars($empleado->getTelefono()) ?></td>
                <td><?= htmlspecialchars($especialidadService->obtenerPorId($empleado->getIdEspecialidad())->getNombre()) ?></td>
                <?php if($empleado->getId() != $_SESSION['id']): ?>
                    <td><a href="<?= BASE_URL ?>Empleado/despedirEmpleado&id=<?= $empleado->getId() ?>">Despedir</a></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
