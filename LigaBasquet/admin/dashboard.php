<?php
session_start();

// Verificar si está logueado y es admin
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rolTipo'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include __DIR__ . '/../config/funciones.php';
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrativo - LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/img/logo5.ico" type="image/x-icon">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>

    <main class="container-fluid px-3 py-4">
        <h1>Panel de Administración</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>.</p>

        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gestión de Usuarios</h5>
                        <p class="card-text">Administrar usuarios del sistema.</p>
                        <a href="users.php" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gestión de Equipos</h5>
                        <p class="card-text">Administrar equipos de la liga.</p>
                        <a href="teams.php" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gestión de Jugadores</h5>
                        <p class="card-text">Administrar jugadores.</p>
                        <a href="players.php" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Programación de Partidos</h5>
                        <p class="card-text">Programar y gestionar partidos.</p>
                        <a href="matches.php" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Más cards para resultados, noticias, estadísticas, configuración -->
    </main>

    <?php include __DIR__ . '/../sections/footer.php'; ?>

    <script src="../assets/themes/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/public.js"></script>
</body>
</html>