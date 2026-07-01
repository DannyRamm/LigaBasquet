<header class="site-header bg-dark text-white">
    <div class="container-fluid px-3">
        <div class="d-flex align-items-center justify-content-between py-2">
            <a class="d-flex align-items-center gap-2 text-decoration-none text-white" href="dashboard.php">
                <span class="brand-mark">LD</span>
                <span class="fw-bold">Admin Panel</span>
            </a>

            <nav class="nav">
                <a class="nav-link text-white" href="users.php">Usuarios</a>
                <a class="nav-link text-white" href="teams.php">Equipos</a>
                <a class="nav-link text-white" href="players.php">Jugadores</a>
                <a class="nav-link text-white" href="matches.php">Partidos</a>
                <a class="nav-link text-white" href="noticias.php">Noticias</a>
                <a class="nav-link text-white" href="estadisticas.php">Estadísticas</a>
                <a class="nav-link text-white" href="configuracion.php">Configuración</a>
            </nav>

            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle text-white p-0" type="button" id="userDropdown" data-bs-toggle="dropdown">
                    <i class="fa-regular fa-circle-user"></i> <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="../logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>