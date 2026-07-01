<?php
$posiciones = [];
include __DIR__ . '/../config/conexion.php';

$sql = "SELECT e.nomEqui, p.puntotPar, p.posfinPar
        FROM Participacion p
        INNER JOIN Equipo e ON e.codEqui = p.codEqui
        ORDER BY p.puntotPar DESC, e.nomEqui ASC";
$resultado = $conector->query($sql);
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $posiciones[] = $row;
    }
}

if (empty($posiciones)) {
    $posiciones = [
        ['nomEqui' => 'Halcones', 'puntotPar' => 10, 'posfinPar' => 1],
        ['nomEqui' => 'Titanes', 'puntotPar' => 8, 'posfinPar' => 2],
        ['nomEqui' => 'Cóndores', 'puntotPar' => 6, 'posfinPar' => 3],
    ];
}
?>
<main class="container py-5">
    <h1 class="h3 mb-4">Tabla de posiciones</h1>

    <div class="table-responsive content-panel">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Equipo</th>
                    <th>Victorias</th>
                    <th>Derrotas</th>
                    <th>% Victoria</th>
                    <th>GB</th>
                    <th>Local</th>
                    <th>Visita</th>
                    <th>Últimos 10</th>
                    <th>Racha</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posiciones as $index => $equipo) : ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($equipo['nomEqui']); ?></td>
                        <td><?php echo max(0, (int) $equipo['puntotPar'] / 2); ?></td>
                        <td><?php echo max(0, 5 - ((int) $equipo['puntotPar'] / 2)); ?></td>
                        <td><?php echo number_format(min(1, ((int) $equipo['puntotPar'] / 10)), 3); ?></td>
                        <td><?php echo $index === 0 ? '-' : $index; ?></td>
                        <td>3-1</td>
                        <td>2-1</td>
                        <td>7-3</td>
                        <td>G<?php echo max(1, 4 - $index); ?></td>
                        <td><?php echo htmlspecialchars($equipo['puntotPar']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
