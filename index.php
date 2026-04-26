<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>LeagueDan - Liga de Básquet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Liga de básquet LeagueDan - Equipos, partidos y resultados">

    <link rel="icon" href="assets/img/logo5.ico" type="image/x-icon">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }

        .hero {
            height: 80vh;
            background: url('assets/img/basquet1.jpg') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
        }

        .hero::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
        }

        .hero-content {
            position: relative;
            text-align: center;
        }

        .section {
            padding: 60px 0;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            transition: 0.3s;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-dark navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">🏀 LeagueDan</a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="#">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="#equipos">Equipos</a></li>
                <li class="nav-item"><a class="nav-link" href="#partidos">Partidos</a></li>
                <li class="nav-item"><a class="nav-link" href="#tabla">Tabla</a></li>
            </ul>

            <a href="loginT.php" class="btn btn-warning">
                <i class="bi bi-box-arrow-in-right"></i> Acceso
            </a>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <h1 class="display-4 fw-bold">Liga de Básquet LeagueDan</h1>
        <p>Vive la emoción del baloncesto local: equipos, partidos y resultados en un solo lugar</p>
        <a href="#partidos" class="btn btn-warning">Ver Partidos</a>
    </div>
</section>

<!-- EQUIPOS -->
<section id="equipos" class="section bg-light">
    <div class="container text-center">
        <h2 class="mb-4">Equipos Participantes</h2>
        <p class="mb-5">Conoce a los equipos que forman parte de la liga</p>

        <div class="row">

            <div class="col-md-4">
                <div class="card card-hover p-3">
                    <h5>Equipo A</h5>
                    <p>Jugadores, estadísticas y rendimiento</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-hover p-3">
                    <h5>Equipo B</h5>
                    <p>Trayectoria dentro de la liga</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-hover p-3">
                    <h5>Equipo C</h5>
                    <p>Resultados recientes</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- PARTIDOS -->
<section id="partidos" class="section">
    <div class="container text-center">
        <h2 class="mb-4">Próximos Encuentros</h2>
        <p class="mb-5">No te pierdas los siguientes partidos</p>

        <div class="card p-3 mb-3">
            <h5>Equipo A vs Equipo B</h5>
            <p>📅 30/04/2026 | 🕒 7:00 PM</p>
        </div>

        <div class="card p-3">
            <h5>Equipo C vs Equipo A</h5>
            <p>📅 05/05/2026 | 🕒 6:00 PM</p>
        </div>

    </div>
</section>

<!-- TABLA -->
<section id="tabla" class="section bg-light">
    <div class="container text-center">
        <h2 class="mb-4">Tabla de Posiciones</h2>
        <p class="mb-4">Clasificación actual de los equipos</p>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Equipo</th>
                    <th>PJ</th>
                    <th>PG</th>
                    <th>Pts</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Equipo A</td>
                    <td>5</td>
                    <td>4</td>
                    <td>10</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Equipo B</td>
                    <td>5</td>
                    <td>3</td>
                    <td>8</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-dark text-white text-center p-3">
    <p>© 2026 LeagueDan - Liga de Básquet</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>