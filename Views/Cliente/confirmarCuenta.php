<h2>Confirmación de Cuenta</h2>

<?php if (isset($mensajeExito)): ?>
    <div class="exito"><?= htmlspecialchars($mensajeExito) ?></div>
<?php endif; ?>

<?php if (isset($mensajeError)): ?>
    <div class="error"><?= htmlspecialchars($mensajeError) ?></div>
<?php endif; ?>
