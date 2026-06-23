<?php
$jugadores = [];
include __DIR__ . '/../config/conexion.php';

$sql = "SELECT j.codJug, j.nomJug, j.edaJug, j.altJug, e.nomEqui
        FROM Jugador j
        LEFT JOIN Jugador_Equipo je ON je.codJug = j.codJug AND je.fecfinJugEqui IS NULL
        LEFT JOIN Equipo e ON e.codEqui = je.codEqui
        ORDER BY j.nomJug";
$resultado = $conector->query($sql);
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $jugadores[] = $row;
    }
}

if (empty($jugadores)) {
    $jugadores = [
        ['nomJug' => 'Daniel Pérez', 'edaJug' => 24, 'altJug' => 1.82, 'nomEqui' => 'Halcones'],
        ['nomJug' => 'Luis Mendoza', 'edaJug' => 27, 'altJug' => 1.95, 'nomEqui' => 'Titanes'],
        ['nomJug' => 'Pedro Salas', 'edaJug' => 29, 'altJug' => 2.01, 'nomEqui' => 'Cóndores'],
    ];
}
?>
<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Jugadores</h1>
        <span class="badge text-bg-dark">Perfiles</span>
    </div>

    <div class="table-responsive content-panel">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Jugador</th>
                    <th>Equipo</th>
                    <th>Edad</th>
                    <th>Altura</th>
                    <th>Perfil</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jugadores as $jugador) : ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars($jugador['nomJug']); ?></td>
                        <td><?php echo htmlspecialchars($jugador['nomEqui'] ?? 'Agente libre'); ?></td>
                        <td><?php echo htmlspecialchars($jugador['edaJug'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($jugador['altJug'] ?? '-'); ?> m</td>
                        <td><a class="btn btn-outline-dark btn-sm" href="index.php?page=jugadores">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
