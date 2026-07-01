<?php
session_start();

$allowed_pages = [
    'home',
    'equipos',
    'partidos',
    'partido',
    'box-score',
    'jugada-a-jugada',
    'tabla',
    'jugadores',
    'noticias',
    'videos',
    'playoffs',
    'draft',
    'league-pass',
    'fantasy',
    'tienda',
    'tickets',
    'buscar',
];

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if (!in_array($page, $allowed_pages, true)) {
    $page = 'home';
}

$page_file = __DIR__ . "/pages/$page.php";

$games = [
    [
        'home' => 'Halcones',
        'away' => 'Titanes',
        'home_score' => 89,
        'away_score' => 84,
        'status' => 'En vivo',
        'time' => 'Q4 02:14',
        'stat' => 'Rebotes: HAL 38 | TIT 34',
    ],
    [
        'home' => 'Cóndores',
        'away' => 'Leones',
        'home_score' => 102,
        'away_score' => 98,
        'status' => 'Final',
        'time' => 'Finalizado',
        'stat' => 'Triples: CON 12 | LEO 9',
    ],
    [
        'home' => 'Guerreros',
        'away' => 'Toros',
        'home_score' => null,
        'away_score' => null,
        'status' => 'Próximo',
        'time' => 'Hoy 8:00 PM',
        'stat' => 'Coliseo Principal',
    ],
    [
        'home' => 'Águilas',
        'away' => 'Panteras',
        'home_score' => 47,
        'away_score' => 45,
        'status' => 'Descanso',
        'time' => 'Medio tiempo',
        'stat' => 'FG%: AGU 48% | PAN 44%',
    ],
];

include __DIR__ . '/sections/header.php';
include __DIR__ . '/sections/scoreboard.php';

if ($page === 'home') {
    include __DIR__ . '/pages/home.php';
} elseif (file_exists($page_file)) {
    include $page_file;
} else {
    echo '<main class="container py-5"><div class="alert alert-warning mb-0">La página solicitada no está disponible.</div></main>';
}

include __DIR__ . '/sections/footer.php';
