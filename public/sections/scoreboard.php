<section class="scoreboard">
    <div class="container-fluid px-3">
        <div class="d-flex justify-content-between align-items-center pt-2">
            <h2 class="h6 mb-0 fw-bold text-uppercase">Marcador en vivo</h2>
            <span class="small text-white-50">Actualización automática</span>
        </div>

        <div class="score-track" id="scoreTrack">
            <?php foreach ($games as $game) : ?>
                <?php
                    $home_initials = strtoupper(substr($game['home'], 0, 3));
                    $away_initials = strtoupper(substr($game['away'], 0, 3));
                    $status_class = $game['status'] === 'En vivo' ? 'status-live' : ($game['status'] === 'Próximo' ? 'status-upcoming' : 'status-final');
                ?>
                <article class="game-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($game['status']); ?></span>
                        <span class="small text-secondary"><?php echo htmlspecialchars($game['time']); ?></span>
                    </div>
                    <div class="team-row mb-2">
                        <span class="team-logo"><?php echo htmlspecialchars($home_initials); ?></span>
                        <span><?php echo htmlspecialchars($game['home']); ?></span>
                        <span class="score-display"><?php echo $game['home_score'] ?? '-'; ?></span>
                    </div>
                    <div class="team-row">
                        <span class="team-logo"><?php echo htmlspecialchars($away_initials); ?></span>
                        <span><?php echo htmlspecialchars($game['away']); ?></span>
                        <span class="score-display"><?php echo $game['away_score'] ?? '-'; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 gap-2">
                        <small class="text-secondary"><?php echo htmlspecialchars($game['stat']); ?></small>
                        <a class="btn btn-outline-dark btn-sm" href="index.php?page=partido">Centro</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
