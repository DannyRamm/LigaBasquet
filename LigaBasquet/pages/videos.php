<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Videos y highlights</h1>
        <span class="badge text-bg-danger">Top plays</span>
    </div>

    <div class="row g-4">
        <?php
        $videos = [
            ['cat' => 'Highlights del día', 'title' => 'Las mejores jugadas de Halcones vs Titanes', 'duration' => '03:45'],
            ['cat' => 'Resumen', 'title' => 'Cóndores vence a Leones en un cierre apretado', 'duration' => '05:18'],
            ['cat' => 'Entrevista', 'title' => 'El MVP de la fecha habla después del partido', 'duration' => '07:02'],
            ['cat' => 'Short', 'title' => 'Bloqueo espectacular en transición', 'duration' => '00:42'],
        ];
        ?>
        <?php foreach ($videos as $video) : ?>
            <div class="col-md-6 col-xl-3">
                <article class="video-card">
                    <div class="video-thumb">
                        <i class="fa-solid fa-play"></i>
                        <span><?php echo htmlspecialchars($video['duration']); ?></span>
                    </div>
                    <div class="p-3">
                        <span class="badge text-bg-dark mb-2"><?php echo htmlspecialchars($video['cat']); ?></span>
                        <h2 class="h6 fw-bold mb-0"><?php echo htmlspecialchars($video['title']); ?></h2>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</main>
