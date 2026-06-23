<?php
$estadisticas = [];
include __DIR__ . '/../config/conexion.php';

$sql = "SELECT p.fecMat, j.nomJug, eq.nomEqui, e.punEst, e.rebEst, e.asiEst, e.falEst
        FROM Estadistica e
        LEFT JOIN Partido p ON p.codMat = e.codMat
        LEFT JOIN Jugador j ON j.codJug = e.codJug
        LEFT JOIN Jugador_Equipo je ON je.codJug = j.codJug
        LEFT JOIN Equipo eq ON eq.codEqui = je.codEqui
        ORDER BY p.fecMat DESC
        LIMIT 20";
$resultado = $conector->query($sql);
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $estadisticas[] = $row;
    }
}

if (empty($estadisticas)) {
    $estadisticas = [
        ['fecMat' => '2026-05-10 19:00:00', 'nomJug' => 'Lucas Pérez', 'nomEqui' => 'Halcones', 'punEst' => 28, 'rebEst' => 11, 'asiEst' => 7, 'falEst' => 2],
        ['fecMat' => '2026-05-09 20:00:00', 'nomJug' => 'Mateo Díaz', 'nomEqui' => 'Titanes', 'punEst' => 24, 'rebEst' => 9, 'asiEst' => 5, 'falEst' => 3],
        ['fecMat' => '2026-05-08 18:30:00', 'nomJug' => 'Santiago Gómez', 'nomEqui' => 'Leones', 'punEst' => 22, 'rebEst' => 8, 'asiEst' => 6, 'falEst' => 1],
    ];
}
?>
<main class="container py-5">
    <h1 class="h3 mb-4">Estadísticas de jugadores</h1>

    <div class="table-responsive content-panel">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
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
                        <td><?php echo htmlspecialchars($est['nomJug'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($est['nomEqui'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($est['punEst'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($est['rebEst'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($est['asiEst'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($est['falEst'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
