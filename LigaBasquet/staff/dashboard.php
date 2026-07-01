<?php
session_start();

// Verificar si está logueado y es staff
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rolTipo'] ?? '') !== 'staff') {
    header('Location: ../login.php');
    exit;
}

include __DIR__ . '/../config/funciones.php';
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Panel de Staff - LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/img/logo5.ico" type="image/x-icon">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>

    <main class="container-fluid px-3 py-4">
        <h1>Panel de Staff Técnico</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>.</p>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Administrar Jugadores</h5>
                        <p class="card-text">Gestionar jugadores de su equipo.</p>
                        <a href="players.php" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Calendario</h5>
                        <p class="card-text">Revisar calendario de partidos.</p>
                        <a href="calendar.php" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Estadísticas</h5>
                        <p class="card-text">Ver estadísticas del equipo.</p>
                        <a href="stats.php" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../sections/footer.php'; ?>

    <script src="../assets/themes/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/public.js"></script>
</body>
</html>