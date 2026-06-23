<?php
$partidos = [];
include __DIR__ . '/../config/conexion.php';

$sql = "SELECT p.codMat, p.fecMat, p.estMat, p.punlocMat, p.punvisMat,
               el.nomEqui AS local, ev.nomEqui AS visitante, c.nomCan
        FROM Partido p
        INNER JOIN Equipo el ON el.codEqui = p.codequilocMat
        INNER JOIN Equipo ev ON ev.codEqui = p.codequivisMat
        LEFT JOIN Cancha c ON c.codCan = p.codCan
        ORDER BY p.fecMat ASC";
$resultado = $conector->query($sql);
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $partidos[] = $row;
    }
}

if (empty($partidos)) {
    $partidos = [
        ['local' => 'Halcones', 'visitante' => 'Titanes', 'fecMat' => '2026-05-09 20:00:00', 'estMat' => 'pendiente', 'punlocMat' => null, 'punvisMat' => null, 'nomCan' => 'Coliseo Principal'],
        ['local' => 'Condors', 'visitante' => 'Leones', 'fecMat' => '2026-05-10 19:30:00', 'estMat' => 'pendiente', 'punlocMat' => null, 'punvisMat' => null, 'nomCan' => 'Arena Central'],
    ];
}
?>
<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Calendario de partidos</h1>
            <p class="text-secondary mb-0">Consulta los próximos encuentros y la programación de la temporada.</p>
        </div>
        <span class="badge text-bg-primary align-self-start">Total: <?php echo count($partidos); ?></span>
    </div>

    <div class="table-responsive content-panel">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Local</th>
                    <th>Visitante</th>
                    <th>Cancha</th>
                    <th>Estado</th>
                    <th>Marcador</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partidos as $partido) : ?>
                    <?php
                        $fecha = null;
                        if (!empty($partido['fecMat'])) {
                            try {
                                $fecha = new DateTime($partido['fecMat']);
                            } catch (Exception $e) {
                                $fecha = null;
                            }
                        }
                        $fechaTexto = $fecha ? $fecha->format('d/m/Y H:i') : 'Por definir';
                        $resultadoTexto = ($partido['punlocMat'] !== null && $partido['punvisMat'] !== null)
                            ? htmlspecialchars($partido['punlocMat'] . ' - ' . $partido['punvisMat'])
                            : '—';
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fechaTexto); ?></td>
                        <td><?php echo htmlspecialchars($partido['local'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($partido['visitante'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($partido['nomCan'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst((string) ($partido['estMat'] ?? 'Pendiente'))); ?></td>
                        <td><?php echo $resultadoTexto; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
