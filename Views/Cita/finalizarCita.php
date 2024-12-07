<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Cita</title>
</head>
<body>
    <h1>Finalizar Cita</h1>

    <form action="<?= BASE_URL ?>Cita/guardarDetallesCita" method="POST">
        <input type="hidden" name="id_cita" value="<?= $cita->getId() ?>">
        <label for="detalles">Detalles de la cita:</label><br>
        <textarea name="detalles" id="detalles" rows="5" required></textarea><br><br>
        <button type="submit">Guardar detalles y finalizar cita</button>
    </form>
</body>
</html>
