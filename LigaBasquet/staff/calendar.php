<?php
session_start();
include __DIR__ . '/../config/conexion.php';
include __DIR__ . '/../config/funciones.php';

if (!esStaff()) {
    header('Location: ../login.php');
    exit;
}

$equipo = obtenerEquipoUsuario($conector, $_SESSION['usuario']['id']);
$partidos = [];

if ($equipo) {
    $sql = 'SELECT p.codMat, p.fecMat, p.estMat, p.punlocMat, p.punvisMat, el.nomEqui AS local, ev.nomEqui AS visitante, c.nomCan
            FROM Partido p
            LEFT JOIN Equipo el ON el.codEqui = p.codequilocMat
            LEFT JOIN Equipo ev ON ev.codEqui = p.codequivisMat
            LEFT JOIN Cancha c ON c.codCan = p.codCan
            WHERE p.codequilocMat = ? OR p.codequivisMat = ?
            ORDER BY p.fecMat DESC';
    $stmt = $conector->prepare($sql);
    $stmt->bind_param('ii', $equipo['codEqui'], $equipo['codEqui']);
    $stmt->execute();
    $partidos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Calendario Staff | LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    <main class="container-fluid px-3 py-4">
        <h1 class="h3 mb-3">Calendario de mi equipo</h1>

        <?php if ($equipo) : ?>
            <p class="text-secondary">Partidos en los que participa <?php echo htmlspecialchars($equipo['nomEqui']); ?>.</p>
            <div class="card">
                <div class="card-body">
                    <table id="calendarTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Local</th>
                                <th>Visitante</th>
                                <th>Cancha</th>
                                <th>Estado</th>
                                <th>Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($partidos as $partido) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($partido['fecMat']); ?></td>
                                    <td><?php echo htmlspecialchars($partido['local']); ?></td>
                                    <td><?php echo htmlspecialchars($partido['visitante']); ?></td>
                                    <td><?php echo htmlspecialchars($partido['nomCan']); ?></td>
                                    <td><?php echo htmlspecialchars($partido['estMat']); ?></td>
                                <td><?php echo ($partido['punlocMat'] !== null || $partido['punvisMat'] !== null) ? htmlspecialchars($partido['punlocMat'] . ' - ' . $partido['punvisMat']) : '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else : ?>
            <div class="alert alert-info">No tienes un equipo asignado. Pide a un administrador que te asigne uno.</div>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/../sections/footer.php'; ?>
    <script src="../assets/themes/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#calendarTable').DataTable({
                "language": {
                    "url": "../assets/js/spanish.json"
                }
            });
        });
    </script>
</body>
</html>