<?php
session_start();
include __DIR__ . '/../config/conexion.php';
include __DIR__ . '/../config/funciones.php';

if (!esAdmin()) {
    header('Location: ../login.php');
    exit;
}

$estadisticas = [];
$result = $conector->query('SELECT e.codEst, p.fecMat, j.nomJug, eq.nomEqui, e.punEst, e.rebEst, e.asiEst, e.falEst
    FROM Estadistica e
    LEFT JOIN Partido p ON p.codMat = e.codMat
    LEFT JOIN Jugador j ON j.codJug = e.codJug
    LEFT JOIN Jugador_Equipo je ON je.codJug = j.codJug
    LEFT JOIN Equipo eq ON eq.codEqui = je.codEqui
    ORDER BY p.fecMat DESC');
if ($result) {
    $estadisticas = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas - Admin | LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    <main class="container-fluid px-3 py-4">
        <h1 class="h3 mb-3">Estadísticas de Jugadores</h1>
        <div class="card">
            <div class="card-body">
                <table id="estadisticasTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha Partido</th>
                            <th>Jugador</th>
                            <th>Equipo</th>
                            <th>Puntos</th>
                            <th>Rebotes</th>
                            <th>Asistencias</th>
                            <th>Faltas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estadisticas as $est) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($est['fecMat']); ?></td>
                                <td><?php echo htmlspecialchars($est['nomJug']); ?></td>
                                <td><?php echo htmlspecialchars($est['nomEqui']); ?></td>
                                <td><?php echo htmlspecialchars($est['punEst']); ?></td>
                                <td><?php echo htmlspecialchars($est['rebEst']); ?></td>
                                <td><?php echo htmlspecialchars($est['asiEst']); ?></td>
                                <td><?php echo htmlspecialchars($est['falEst']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/../sections/footer.php'; ?>
    <script src="../assets/themes/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#estadisticasTable').DataTable({
                "language": {
                    "url": "../assets/js/spanish.json"
                }
            });
        });
    </script>
</body>
</html>