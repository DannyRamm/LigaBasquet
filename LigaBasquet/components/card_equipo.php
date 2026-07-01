<?php
// Componente para mostrar una tarjeta de equipo
function renderCardEquipo($equipo) {
    ?>
    <div class="card equipo-card">
        <div class="card-body text-center">
            <h5 class="card-title"><?php echo htmlspecialchars($equipo['nomEqui']); ?></h5>
            <p class="card-text">Equipo de la liga LeagueDan</p>
            <a href="index.php?page=equipos&equipo=<?php echo $equipo['codEqui']; ?>" class="btn btn-primary">Ver Equipo</a>
        </div>
    </div>
    <?php
}
?>