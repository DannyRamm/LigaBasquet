<?php
$equipos = [];
include __DIR__ . '/../config/conexion.php';

$resultado = $conector->query("SELECT codEqui, nomEqui, ciuEqui FROM Equipo ORDER BY nomEqui");
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $equipos[] = $row;
    }
}

if (empty($equipos)) {
    $equipos = [
        ['codEqui' => 1, 'nomEqui' => 'Halcones', 'ciuEqui' => 'Lima'],
        ['codEqui' => 2, 'nomEqui' => 'Titanes', 'ciuEqui' => 'Callao'],
        ['codEqui' => 3, 'nomEqui' => 'Cóndores', 'ciuEqui' => 'Cusco'],
    ];
}
?>
<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Equipos</h1>
        <span class="badge text-bg-dark">Perfil de equipos</span>
    </div>

    <div class="row g-4">
        <?php foreach ($equipos as $equipo) : ?>
            <div class="col-md-6 col-xl-4">
                <article class="content-panel">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="team-logo team-logo-lg"><?php echo strtoupper(substr($equipo['nomEqui'], 0, 3)); ?></span>
                        <div>
                            <h2 class="h5 fw-bold mb-1"><?php echo htmlspecialchars($equipo['nomEqui']); ?></h2>
                            <p class="mb-0 text-secondary"><?php echo htmlspecialchars($equipo['ciuEqui'] ?? 'Ciudad por definir'); ?></p>
                        </div>
                    </div>
                    <p class="small text-secondary">Roster, calendario, estadísticas, lesiones e historia del equipo.</p>
                    <a class="btn btn-outline-dark btn-sm" href="index.php?page=partido">Ver perfil</a>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</main>
