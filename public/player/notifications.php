<?php
session_start();
include __DIR__ . '/../config/funciones.php';

if (!esPlayer()) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Notificaciones | LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    <main class="container-fluid px-3 py-4">
        <h1 class="h3 mb-3">Notificaciones</h1>
        <div class="alert alert-info">Aquí se mostrarán las notificaciones relacionadas con tu equipo, estadísticas y alertas de partido.</div>
    </main>
    <?php include __DIR__ . '/../sections/footer.php'; ?>
    <script src="../assets/themes/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
</body>
</html>