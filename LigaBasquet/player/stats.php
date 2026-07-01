<?php
session_start();
include __DIR__ . '/../config/conexion.php';
include __DIR__ . '/../config/funciones.php';

if (!esPlayer()) {
    header('Location: ../login.php');
    exit;
}

$usuarioEquipo = obtenerEquipoUsuario($conector, $_SESSION['usuario']['id']);
$estadisticas = [];

if ($usuarioEquipo) {
    $sql = 'SELECT p.codMat, p.fecMat, p.estMat, p.punlocMat, p.punvisMat, el.nomEqui AS local, ev.nomEqui AS visitante
            FROM Partido p
            LEFT JOIN Equipo el ON el.codEqui = p.codequilocMat
            LEFT JOIN Equipo ev ON ev.codEqui = p.codequivisMat
            WHERE p.codequilocMat = ? OR p.codequivisMat = ?
            ORDER BY p.fecMat DESC LIMIT 8';
    $stmt = $conector->prepare($sql);
    $stmt->bind_param('ii', $usuarioEquipo['codEqui'], $usuarioEquipo['codEqui']);
    $stmt->execute();
    $estadisticas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas Jugador | LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    <main class="container-fluid px-3 py-4">
        <h1 class="h3 mb-3">Estadísticas del jugador</h1>
        <?php if ($usuarioEquipo) : ?>
            <p class="text-secondary">Datos básicos de tu equipo y últimos partidos.</p>
            <div class="card">
                <div class="card-body">
                    <table id="statsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Local</th>
                                <th>Visitante</th>
                                <th>Resultado</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estadisticas as $item) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['fecMat']); ?></td>
                                    <td><?php echo htmlspecialchars($item['local']); ?></td>
                                    <td><?php echo htmlspecialchars($item['visitante']); ?></td>
                                    <td><?php echo ($item['punlocMat'] !== null || $item['punvisMat'] !== null) ? htmlspecialchars($item['punlocMat'] . ' - ' . $item['punvisMat']) : '-'; ?></td>
                                    <td><?php echo htmlspecialchars($item['estMat']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else : ?>
            <div class="alert alert-info">No estás asignado a ningún equipo. Pide a un administrador que te asigne uno.</div>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/../sections/footer.php'; ?>
    <script src="../assets/themes/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#statsTable').DataTable({
                "language": {
                    "url": "../assets/js/spanish.json"
                }
            });
        });
    </script>
</body>
</html>