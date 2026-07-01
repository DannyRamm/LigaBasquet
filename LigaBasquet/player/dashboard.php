<?php
session_start();

// Verificar si está logueado y es jugador
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rolTipo'] ?? '') !== 'player') {
    header('Location: ../login.php');
    exit;
}

include __DIR__ . '/../config/funciones.php';
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Panel de Jugador - LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/img/logo5.ico" type="image/x-icon">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>

    <main class="container-fluid px-3 py-4">
        <h1>Panel de Jugador</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>.</p>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Estadísticas Personales</h5>
                        <p class="card-text">Ver tus estadísticas.</p>
                        <a href="stats.php" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Perfil Deportivo</h5>
                        <p class="card-text">Gestionar tu perfil.</p>
                        <a href="profile.php" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Historial de Partidos</h5>
                        <p class="card-text">Ver partidos jugados.</p>
                        <a href="history.php" class="btn btn-primary">Ir</a>
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