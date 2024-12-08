<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salón de belleza</title>
    <link rel="stylesheet" href="<?=BASE_URL?>Css/general.css">
    <link rel="stylesheet" href="<?=BASE_URL?>Css/tablas.css">
    <link rel="stylesheet" href="<?=BASE_URL?>Css/formularios.css">
</head>
<body>
    <script>
        const BASE_URL = 'http://localhost/SalonBelleza/';
    </script>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    ?>
    <h1>Salón de belleza</h1>
    
