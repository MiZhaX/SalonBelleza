<h2>Lista de Servicios</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Duración (minutos)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($servicios as $servicio): ?>
            <tr>
                <td><?= htmlspecialchars($servicio->getId()) ?></td>
                <td><?= htmlspecialchars($servicio->getNombre()) ?></td>
                <td>$<?= number_format($servicio->getPrecio(), 2) ?></td>
                <td><?= htmlspecialchars($servicio->getDuracionMinutos()) ?> min</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
