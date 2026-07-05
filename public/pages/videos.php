<?php
// IDs de videos reales de básquet que SÍ permiten reproducción en localhost
$videos = [
    [
        'id' => 1,
        'cat' => 'Highlights del día',
        'title' => 'Las mejores jugadas de Halcones vs Titanes',
        'duration' => '03:45',
        'desc' => 'Un partido de infarto donde Halcones logró remontar gracias a una racha de triples.',
        'youtube_id' => 'ujEc4uqut9Y'
    ],
    [
        'id' => 2,
        'cat' => 'Resumen',
        'title' => 'Cóndores vence a Leones',
        'duration' => '05:18',
        'desc' => 'Un encuentro muy parejo que se definió en los últimos segundos.',
        'youtube_id' => '_OzKrLywaBk'
    ],
    [
        'id' => 3,
        'cat' => 'Entrevista',
        'title' => 'El MVP habla después del partido',
        'duration' => '07:02',
        'desc' => 'Conversamos con el jugador más destacado de la jornada sobre la estrategia del equipo.',
        'youtube_id' => 'W0oNgIxGogk'
    ],
    [
        'id' => 4,
        'cat' => 'Short',
        'title' => 'Bloqueo espectacular',
        'duration' => '00:42',
        'desc' => 'La mejor jugada defensiva de la semana en transición.',
        'youtube_id' => 'f4zeCSoOe3o'
    ]
];
?>

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Videos y Highlights</h1>
        <span class="badge bg-danger">Top Plays</span>
    </div>

    <div class="row g-4">
        <?php foreach($videos as $video): ?>
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="card video-card"
                data-video="<?= $video['youtube_id'];?>"
                data-title="<?= htmlspecialchars($video['title']);?>"
                data-desc="<?= htmlspecialchars($video['desc']);?>"
                data-cat="<?= htmlspecialchars($video['cat']);?>"
                data-bs-toggle="modal"
                data-bs-target="#videoModal">

                <div class="video-thumb" style="background-image:url('https://img.youtube.com/vi/<?= $video['youtube_id'];?>/hqdefault.jpg')">
                    <div class="video-overlay"></div>
                    <i class="fa-solid fa-circle-play video-play"></i>
                    <span class="badge bg-dark video-duration"><?= $video['duration'];?></span>
                </div>

                <div class="card-body">
                    <span class="badge bg-danger mb-2"><?= htmlspecialchars($video['cat']);?></span>
                    <h5 class="card-title h6 fw-bold"><?= htmlspecialchars($video['title']);?></h5>
                </div>

            </div>

        </div>
        <?php endforeach; ?>
    </div>

</main>

<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
            
            <div class="modal-header border-secondary">
                <h5 id="modalTitle" class="fw-bold"></h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-header d-block border-0 pt-2 pb-0">
                <span id="modalCategory" class="badge bg-danger"></span>
            </div>

            <div class="modal-body">
                <div class="ratio ratio-16x9 mb-3">
                    <iframe id="youtubeFrame" src="" allow="autoplay; encrypted-media" allowfullscreen loading="lazy"></iframe>
                </div>
                <p id="modalDescription" class="text-white-50"></p>
            </div>

        </div>
    </div>
</div>

<script src="assets/js/videos.js"></script>

