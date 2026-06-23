<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>LeagueDan - Liga de Básquet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal público de LeagueDan: partidos, noticias, posiciones, equipos y jugadores.">

    <link rel="icon" href="assets/img/logo5.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body data-theme="light">
<?php $page = $page ?? 'home'; ?>
<div class="top-strip py-2">
    <div class="container-fluid px-3 d-flex justify-content-between align-items-center gap-3">
        <div class="d-none d-md-flex gap-3">
            <a class="link-light link-underline-opacity-0" href="index.php?page=tickets">Entradas</a>
            <a class="link-light link-underline-opacity-0" href="index.php?page=tienda">Tienda</a>
            <a class="link-light link-underline-opacity-0" href="index.php?page=league-pass">League Pass</a>
        </div>
        <div class="ms-auto d-flex align-items-center gap-2">
            <select class="form-select form-select-sm bg-dark text-white border-secondary" id="languageSelect" aria-label="Idioma">
                <option value="es" selected>ES</option>
                <option value="en">EN</option>
            </select>
            <button class="header-icon text-white border-secondary" id="themeToggle" type="button" aria-label="Cambiar tema">
                <i class="fa-solid fa-moon"></i>
            </button>
        </div>
    </div>
</div>

<header class="site-header">
    <div class="container-fluid px-3">
        <div class="d-flex align-items-center gap-3 py-2">
            <a class="d-flex align-items-center gap-2 text-decoration-none text-reset" href="index.php?page=home">
                <span class="brand-mark">LD</span>
                <span class="fw-bold">LeagueDan</span>
            </a>

            <nav class="nav main-nav flex-nowrap flex-grow-1">
                <a class="nav-link <?php echo $page === 'partidos' ? 'active' : ''; ?>" href="index.php?page=partidos">Partidos</a>
                <a class="nav-link <?php echo $page === 'calendario' ? 'active' : ''; ?>" href="index.php?page=calendario">Calendario</a>
                <a class="nav-link <?php echo $page === 'videos' ? 'active' : ''; ?>" href="index.php?page=videos">Videos</a>
                <a class="nav-link <?php echo $page === 'noticias' ? 'active' : ''; ?>" href="index.php?page=noticias">Noticias</a>
                <a class="nav-link <?php echo $page === 'tabla' ? 'active' : ''; ?>" href="index.php?page=tabla">Posiciones</a>
                <a class="nav-link <?php echo $page === 'estadisticas' ? 'active' : ''; ?>" href="index.php?page=estadisticas">Estadísticas</a>
                <a class="nav-link <?php echo $page === 'equipos' ? 'active' : ''; ?>" href="index.php?page=equipos">Equipos</a>
                <a class="nav-link <?php echo $page === 'jugadores' ? 'active' : ''; ?>" href="index.php?page=jugadores">Jugadores</a>
                <a class="nav-link <?php echo $page === 'playoffs' ? 'active' : ''; ?>" href="index.php?page=playoffs">Playoffs</a>
                <a class="nav-link <?php echo $page === 'fantasy' ? 'active' : ''; ?>" href="index.php?page=fantasy">Fantasy</a>
                <a class="nav-link <?php echo $page === 'draft' ? 'active' : ''; ?>" href="index.php?page=draft">Draft</a>
                <a class="nav-link <?php echo $page === 'tienda' ? 'active' : ''; ?>" href="index.php?page=tienda">Tienda</a>
            </nav>

            <form class="d-none d-lg-flex" role="search" action="index.php" method="get">
                <input type="hidden" name="page" value="buscar">
                <input class="form-control form-control-sm" name="q" type="search" placeholder="Buscar liga..." aria-label="Buscar">
            </form>

            <button class="header-icon" type="button" aria-label="Notificaciones">
                <i class="fa-regular fa-bell"></i>
            </button>

            <?php if (isset($_SESSION['usuario'])) : ?>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle header-icon p-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="<?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>">
                        <i class="fa-regular fa-circle-user"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#" id="myAccountLink">My Account</a></li>
                        <li><a class="dropdown-item" href="#" id="nbaIdLink">NBA ID Benefits</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <div class="dropdown-item d-flex justify-content-between align-items-center">
                                <span id="hideScoresText">Hide Scores</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="hideScoresToggle">
                                    <label class="form-check-label" for="hideScoresToggle"></label>
                                </div>
                            </div>
                        </li>
                        <li><a class="dropdown-item" href="#" id="helpLink">Help</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i><span id="signOutText">Sign Out</span></a></li>
                    </ul>
                </div>
            <?php else : ?>
                <a class="header-icon" href="login.php" aria-label="Iniciar sesión">
                    <i class="fa-regular fa-circle-user"></i>
                </a>
            <?php endif; ?>

            <a class="btn btn-danger btn-sm fw-bold d-none d-xl-inline-flex" href="index.php?page=league-pass">League Pass</a>
        </div>
    </div>
</header>
