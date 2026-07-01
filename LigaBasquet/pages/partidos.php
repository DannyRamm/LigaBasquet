<?php
$partidos = [];
include __DIR__ . '/../config/conexion.php';

$sql = "SELECT p.codMat, p.fecMat, p.estMat, p.punlocMat, p.punvisMat,
               el.nomEqui AS local, ev.nomEqui AS visitante, c.nomCan
        FROM Partido p
        INNER JOIN Equipo el ON el.codEqui = p.codequilocMat
        INNER JOIN Equipo ev ON ev.codEqui = p.codequivisMat
        LEFT JOIN Cancha c ON c.codCan = p.codCan
        ORDER BY p.fecMat DESC";
$resultado = $conector->query($sql);
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $partidos[] = $row;
    }
}

if (empty($partidos)) {
    $partidos = [
        ['local' => 'Halcones', 'visitante' => 'Titanes', 'fecMat' => '2026-05-09 20:00:00', 'estMat' => 'en vivo', 'punlocMat' => 89, 'punvisMat' => 84, 'nomCan' => 'Coliseo Principal'],
        ['local' => 'Cóndores', 'visitante' => 'Leones', 'fecMat' => '2026-05-08 19:00:00', 'estMat' => 'jugado', 'punlocMat' => 102, 'punvisMat' => 98, 'nomCan' => 'Arena Central'],
    ];
}
?>
<main class="container-fluid py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 60vh;">
    <div class="container">
        <div class="text-white mb-5">
            <h1 class="display-4 fw-bold mb-2"><i class="fas fa-calendar-alt me-3"></i>Partidos</h1>
            <p class="lead">Sigue los partidos en vivo y los resultados de la temporada</p>
        </div>

        <div class="row g-4">
            <?php foreach ($partidos as $partido) : ?>
                <?php
                    $isPendiente = strtolower($partido['estMat']) === 'pendiente';
                    $isJugado = strtolower($partido['estMat']) === 'jugado';
                    $isEnVivo = strtolower($partido['estMat']) === 'en vivo';
                    
                    $badgeClass = 'bg-warning';
                    $badgeIcon = 'fa-clock';
                    if ($isJugado) {
                        $badgeClass = 'bg-success';
                        $badgeIcon = 'fa-check-circle';
                    } elseif ($isEnVivo) {
                        $badgeClass = 'bg-danger';
                        $badgeIcon = 'fa-dot-circle';
                    }
                    
                    $fecha = new DateTime($partido['fecMat']);
                    $hoy = new DateTime();
                    $esHoy = $fecha->format('Y-m-d') === $hoy->format('Y-m-d');
                ?>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg overflow-hidden h-100" style="transition: all 0.3s ease; cursor: pointer;" 
                         onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 1.5rem 3rem rgba(0,0,0,0.2)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0.5rem 1rem rgba(0,0,0,0.15)';">
                        
                        <!-- Header -->
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center" style="padding: 1.5rem;">
                            <div>
                                <span class="badge <?php echo $badgeClass; ?> me-2">
                                    <i class="fas <?php echo $badgeIcon; ?> me-1"></i>
                                    <?php echo htmlspecialchars(ucfirst($partido['estMat'])); ?>
                                </span>
                                <?php if ($esHoy) : ?>
                                    <span class="badge bg-info ms-2">Hoy</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-light">
                                <i class="fas fa-calendar-check me-1"></i>
                                <?php echo $fecha->format('d/m/Y'); ?>
                            </small>
                        </div>
                        
                        <!-- Body -->
                        <div class="card-body p-4">
                            <!-- Equipos y Resultado -->
                            <div class="row align-items-center mb-4">
                                <!-- Local -->
                                <div class="col-md-4 text-center">
                                    <div class="mb-2">
                                        <div class="badge bg-primary p-3 mb-2" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; font-size: 1.5rem; margin: 0 auto;">
                                            <?php echo strtoupper(substr($partido['local'], 0, 1)); ?>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($partido['local']); ?></h6>
                                </div>
                                
                                <!-- Resultado -->
                                <div class="col-md-4 text-center">
                                    <?php if ($partido['punlocMat'] !== null && $partido['punvisMat'] !== null) : ?>
                                        <div class="display-5 fw-bold">
                                            <span style="font-size: 2.5rem;"><?php echo $partido['punlocMat']; ?></span>
                                            <span style="color: #ccc; margin: 0 10px;">-</span>
                                            <span style="font-size: 2.5rem;"><?php echo $partido['punvisMat']; ?></span>
                                        </div>
                                        <small class="text-muted">Final</small>
                                    <?php else : ?>
                                        <div class="text-muted" style="font-size: 2.5rem; font-weight: bold;">-</div>
                                        <small class="text-muted"><?php echo $fecha->format('H:i'); ?></small>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Visitante -->
                                <div class="col-md-4 text-center">
                                    <div class="mb-2">
                                        <div class="badge bg-success p-3 mb-2" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; font-size: 1.5rem; margin: 0 auto;">
                                            <?php echo strtoupper(substr($partido['visitante'], 0, 1)); ?>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($partido['visitante']); ?></h6>
                                </div>
                            </div>
                            
                            <!-- Información Adicional -->
                            <hr class="my-3">
                            <div class="row text-center">
                                <div class="col-12 mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <?php echo htmlspecialchars($partido['nomCan'] ?? 'Cancha por definir'); ?>
                                    </small>
                                </div>
                            </div>
                            
                            <!-- Botón -->
                            <div class="mt-4">
                                <a href="index.php?page=partido" class="btn btn-primary w-100">
                                    <i class="fas fa-arrow-right me-2"></i>Ver detalles del partido
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Si no hay partidos -->
        <?php if (empty($partidos)) : ?>
            <div class="alert alert-info text-center mt-5">
                <i class="fas fa-info-circle me-2"></i>
                No hay partidos registrados actualmente
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
    .match-card {
        transition: all 0.3s ease;
    }
    
    .match-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.3) !important;
    }
    
    .team-badge {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
        font-size: 1.8rem;
        color: white;
    }
</style>
