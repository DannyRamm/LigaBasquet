<?php
// Componente para mostrar una tarjeta de partido
function renderCardPartido($partido) {
    ?>
    <div class="card partido-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <span><?php echo htmlspecialchars($partido['local']); ?></span>
                <span class="fw-bold"><?php echo $partido['punlocMat'] !== null ? htmlspecialchars($partido['punlocMat']) : '-'; ?> - <?php echo $partido['punvisMat'] !== null ? htmlspecialchars($partido['punvisMat']) : '-'; ?></span>
                <span><?php echo htmlspecialchars($partido['visitante']); ?></span>
            </div>
            <small class="text-muted"><?php echo htmlspecialchars($partido['fecMat']); ?> - <?php echo htmlspecialchars($partido['cancha']); ?></small>
            <div class="mt-2">
                <span class="badge bg-<?php echo $partido['estMat'] === 'jugado' ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($partido['estMat']); ?></span>
            </div>
        </div>
    </div>
    <?php
}
?>