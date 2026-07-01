<?php
// Componente para mostrar una tarjeta de noticia
function renderCardNoticia($noticia) {
    ?>
    <div class="card noticia-card">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($noticia['titulo']); ?></h5>
            <p class="card-text"><?php echo htmlspecialchars(substr($noticia['contenido'], 0, 100)); ?>...</p>
            <small class="text-muted"><?php echo htmlspecialchars($noticia['fecha']); ?></small>
            <a href="index.php?page=noticias&noticia=<?php echo $noticia['id']; ?>" class="btn btn-link">Leer más</a>
        </div>
    </div>
    <?php
}
?>