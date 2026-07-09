<?php
require_once __DIR__ . '/../../LigaBasquet/config/conexion.php';

/*=====================================================
=            FUNCIONES AUXILIARES                     =
=====================================================*/

function e($texto)
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function fechaBonita($fecha)
{
    if (!$fecha || strtotime($fecha) === false) {
        return "Fecha desconocida";
    }

    $meses = [
        'Jan' => 'Ene',
        'Feb' => 'Feb',
        'Mar' => 'Mar',
        'Apr' => 'Abr',
        'May' => 'May',
        'Jun' => 'Jun',
        'Jul' => 'Jul',
        'Aug' => 'Ago',
        'Sep' => 'Sep',
        'Oct' => 'Oct',
        'Nov' => 'Nov',
        'Dec' => 'Dic'
    ];

    $fechaFormateada = date('d M Y', strtotime($fecha));

    return str_replace(array_keys($meses), array_values($meses), $fechaFormateada);
}

function resumen($texto, $limite = 150)
{
    $texto = strip_tags($texto);

    if (mb_strlen($texto) <= $limite) {
        return $texto;
    }

    return mb_substr($texto, 0, $limite) . "...";
}

function badgeCategoria($categoria)
{
    return match ($categoria) {

        'Breaking' => 'danger',

        'Equipos' => 'primary',

        'Jugadores' => 'success',

        'Fichajes' => 'warning text-dark',

        'Táctica' => 'info text-dark',

        default => 'secondary'

    };
}

function obtenerIdNoticia(array $noticia): string
{
    if (!empty($noticia['codNot'])) {
        return (string) $noticia['codNot'];
    }

    if (!empty($noticia['idNot'])) {
        return (string) $noticia['idNot'];
    }

    return '';
}

function obtenerImagen($noticia)
{
    $baseUrl = 'assets/img/noticias/';
    $basePath = __DIR__ . '/../assets/img/noticias/';

    if (!empty($noticia['imagenNot'])) {
        $rutaFisica = $basePath . $noticia['imagenNot'];
        if (file_exists($rutaFisica)) {
            return $baseUrl . $noticia['imagenNot'];
        }
    }

    $imagenesPorCategoria = [
        'Breaking' => 'assets/img/noticias/b1.jpg',
        'Equipos' => 'assets/img/noticias/b2.jpg',
        'Jugadores' => 'assets/img/noticias/b3.jpg',
        'Fichajes' => 'assets/img/noticias/bas.jpg',
        'Táctica' => 'assets/img/noticias/bas1.jpg',
    ];

    $categoria = $noticia['categoriaNot'] ?? '';
    return $imagenesPorCategoria[$categoria] ?? 'assets/img/noticias/bas.jpg';
}

/*=====================================================
=                 CONSULTA                            =
=====================================================*/

$noticias = [];
$error = null;

$sql = "
SELECT *
FROM Noticia
ORDER BY fechaNot DESC
";

$resultado = $conector->query($sql);

if ($resultado) {

    while ($fila = $resultado->fetch_assoc()) {

        $noticias[] = $fila;

    }

    $resultado->free();

} else {

    $error = $conector->error;

}

$conector->close();

/*=====================================================
=          SEPARAR NOTICIAS                           =
=====================================================*/

$destacada = null;
$laterales = [];

if (!empty($noticias)) {

    $destacada = array_shift($noticias);

    $laterales = array_splice($noticias, 0, 4);

}
?>

<link rel="stylesheet" href="assets/css/estilos1.css">

<main class="container py-5">

    <!--=========================
            TITULO
    ==========================-->

    <div class="row mb-5 align-items-center">

        <div class="col-lg-8">

            <h1 class="display-5 fw-bold">
                🏀 Noticias LeagueDan
            </h1>

            <p class="lead text-muted">

                Mantente informado con las últimas novedades, resultados,
                fichajes y acontecimientos de la Liga LeagueDan.

            </p>

        </div>

        <div class="col-lg-4 text-lg-end">

            <span class="badge bg-danger me-2">Breaking</span>

            <span class="badge bg-primary me-2">Equipos</span>

            <span class="badge bg-success me-2">Jugadores</span>

            <span class="badge bg-warning text-dark me-2">Fichajes</span>

            <span class="badge bg-info text-dark">Táctica</span>

        </div>

    </div>

    <?php if ($error): ?>

        <div class="alert alert-danger">

            <?= e($error) ?>

        </div>

    <?php endif; ?>

    <?php if(empty($destacada)): ?>

        <div class="alert alert-info">

            No existen noticias registradas.

        </div>

    <?php else: ?>

    <!--=====================================
            NOTICIA PRINCIPAL
    ======================================-->

    <section class="row g-4 mb-5">

        <div class="col-lg-8">

            <article class="card hero-card border-0 shadow-lg overflow-hidden">

                <img
                    src="<?= obtenerImagen($destacada) ?>"
                    class="card-img-top hero-img"
                    alt="<?= e($destacada['tituloNot']) ?>"
                >

                <div class="hero-overlay"></div>

                <div class="hero-content">

                    <span class="badge bg-<?= badgeCategoria($destacada['categoriaNot']) ?>">

                        <?= e($destacada['categoriaNot']) ?>

                    </span>

                    <h2 class="mt-3">

                        <?= e($destacada['tituloNot']) ?>

                    </h2>

                    <p>

                        <?= resumen($destacada['contenidoNot'],220) ?>

                    </p>

                    <div class="hero-footer">

                        <span>

                            <i class="fas fa-calendar"></i>

                            <?= fechaBonita($destacada['fechaNot']) ?>

                        </span>

                        <a
                            href="index.php?page=noticias&noticia=<?= e(obtenerIdNoticia($destacada)) ?>"
                            class="btn btn-danger"
                        >

                            Leer noticia

                        </a>

                    </div>

                </div>

            </article>

        </div>

        <!--=====================================
                NOTICIAS LATERALES
        ======================================-->

        <div class="col-lg-4">

            <div class="row g-3">
                                <?php foreach ($laterales as $noticia): ?>

                    <div class="col-12">

                        <article class="card side-card border-0 shadow-sm h-100">

                            <div class="row g-0 h-100">

                                <div class="col-4">

                                    <img
                                        src="<?= obtenerImagen($noticia) ?>"
                                        class="img-fluid h-100 w-100 side-img"
                                        alt="<?= e($noticia['tituloNot']) ?>"
                                    >

                                </div>

                                <div class="col-8">

                                    <div class="card-body">

                                        <span class="badge bg-<?= badgeCategoria($noticia['categoriaNot']) ?>">

                                            <?= e($noticia['categoriaNot']) ?>

                                        </span>

                                        <h6 class="fw-bold mt-2 mb-2">

                                            <?= e($noticia['tituloNot']) ?>

                                        </h6>

                                        <p class="small text-muted mb-2">

                                            <?= resumen($noticia['contenidoNot'],80) ?>

                                        </p>

                                        <small class="text-secondary">

                                            <i class="fas fa-calendar-alt me-1"></i>

                                            <?= fechaBonita($noticia['fechaNot']) ?>

                                        </small>

                                    </div>

                                </div>

                            </div>

                        </article>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

    </section>

    <!--=====================================
            TODAS LAS NOTICIAS
    ======================================-->

    <?php if (!empty($noticias)): ?>

    <section>

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="fw-bold mb-1">

                    Últimas noticias

                </h3>

                <p class="text-muted mb-0">

                    Resultados, entrevistas, fichajes y mucho más.

                </p>

            </div>

            <span class="badge bg-dark fs-6">

                <?= count($noticias) + count($laterales) + 1 ?>

                Noticias

            </span>

        </div>

        <div class="row g-4">

            <?php foreach($noticias as $noticia): ?>

                <div class="col-md-6 col-xl-4">

                    <article class="card news-card border-0 shadow-sm h-100">

                        <img
                            src="<?= obtenerImagen($noticia) ?>"
                            class="card-img-top news-thumb"
                            alt="<?= e($noticia['tituloNot']) ?>"
                        >

                        <div class="card-body d-flex flex-column">

                            <div class="d-flex justify-content-between mb-3">

                                <span class="badge bg-<?= badgeCategoria($noticia['categoriaNot']) ?>">

                                    <?= e($noticia['categoriaNot']) ?>

                                </span>

                                <small class="text-muted">

                                    <?= fechaBonita($noticia['fechaNot']) ?>

                                </small>

                            </div>

                            <h5 class="fw-bold">

                                <?= e($noticia['tituloNot']) ?>

                            </h5>

                            <p class="text-muted flex-grow-1">

                                <?= resumen($noticia['contenidoNot'],120) ?>

                            </p>

                            <a
                                href="index.php?page=noticias&noticia=<?= e(obtenerIdNoticia($noticia)) ?>"
                                class="btn btn-outline-danger mt-auto"
                            >

                                Leer más

                            </a>

                        </div>

                    </article>

                </div>

            <?php endforeach; ?>

        </div>

    </section>

    <?php endif; ?>

    <?php endif; ?>

</main>