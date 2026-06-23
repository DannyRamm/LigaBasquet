<?php
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$resultados = [];

if ($query !== '') {
    include __DIR__ . '/../config/conexion.php';

    // Buscar en equipos
    $stmt = $conector->prepare('SELECT "equipo" AS tipo, nomEqui AS titulo, codEqui AS id FROM Equipo WHERE nomEqui LIKE ?');
    $search = '%' . $query . '%';
    $stmt->bind_param('s', $search);
    $stmt->execute();
    $equipos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Buscar en jugadores
    $stmt = $conector->prepare('SELECT "jugador" AS tipo, CONCAT(nomJug, " ", apeJug) AS titulo, codJug AS id FROM Jugador WHERE nomJug LIKE ? OR apeJug LIKE ?');
    $stmt->bind_param('ss', $search, $search);
    $stmt->execute();
    $jugadores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $resultados = array_merge($equipos, $jugadores);
}
?>
<main class="container py-5">
    <h1 class="h3 mb-4">Búsqueda global</h1>
    <div class="content-panel mb-4">
        <form class="row g-2" action="index.php" method="get">
            <input type="hidden" name="page" value="buscar">
            <div class="col-md-10"><input class="form-control" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Busca jugadores, equipos, noticias, partidos o videos"></div>
            <div class="col-md-2"><button class="btn btn-danger w-100">Buscar</button></div>
        </form>
    </div>
    <div class="content-panel">
        <h2 class="section-title">Resultados</h2>
        <?php if ($query === '') : ?>
            <p class="mb-0 text-secondary">Tendencias: Halcones, MVP, Playoffs, Entradas, Titanes.</p>
        <?php elseif (empty($resultados)) : ?>
            <p class="mb-0">No se encontraron resultados para <strong><?php echo htmlspecialchars($query); ?></strong>.</p>
        <?php else : ?>
            <ul class="list-group">
                <?php foreach ($resultados as $resultado) : ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($resultado['tipo']); ?>:</strong> <?php echo htmlspecialchars($resultado['titulo']); ?>
                        <a href="index.php?page=<?php echo $resultado['tipo'] === 'equipo' ? 'equipos' : 'jugadores'; ?>&id=<?php echo $resultado['id']; ?>" class="btn btn-sm btn-primary float-end">Ver</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</main>
