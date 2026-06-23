<?php
session_start();
include __DIR__ . '/../config/conexion.php';
include __DIR__ . '/../config/funciones.php';

if (!esStaff()) {
    header('Location: ../login.php');
    exit;
}

$equipo = obtenerEquipoUsuario($conector, $_SESSION['usuario']['id']);
$error = '';
$mensaje = '';
$jugadores = [];

if ($equipo) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = $_POST['accion'] ?? '';
        $codJug = (int) ($_POST['codJug'] ?? 0);
        $nomJug = trim($_POST['nomJug'] ?? '');
        $edaJug = (int) ($_POST['edaJug'] ?? 0);
        $altJug = trim($_POST['altJug'] ?? '');

        if ($accion === 'crear' && $nomJug !== '') {
            $stmt = $conector->prepare('INSERT INTO Jugador (nomJug, edaJug, altJug) VALUES (?, ?, ?)');
            $stmt->bind_param('sis', $nomJug, $edaJug, $altJug);
            if ($stmt->execute()) {
                $jugId = $conector->insert_id;
                $stmt2 = $conector->prepare('INSERT INTO Jugador_Equipo (codJug, codEqui, feciniJugEqui) VALUES (?, ?, CURDATE())');
                $stmt2->bind_param('ii', $jugId, $equipo['codEqui']);
                $stmt2->execute();
                $mensaje = 'Jugador registrado en tu equipo.';
            }
        }

        if ($accion === 'editar' && $codJug > 0) {
            $stmt = $conector->prepare('UPDATE Jugador SET nomJug = ?, edaJug = ?, altJug = ? WHERE codJug = ?');
            $stmt->bind_param('sisi', $nomJug, $edaJug, $altJug, $codJug);
            if ($stmt->execute()) {
                $mensaje = 'Jugador actualizado.';
            }
        }
    }

    $sql = 'SELECT j.codJug, j.nomJug, j.edaJug, j.altJug
        FROM Jugador j
        JOIN Jugador_Equipo je ON je.codJug = j.codJug
        WHERE je.codEqui = ?
        ORDER BY j.nomJug';
    $stmt = $conector->prepare($sql);
    $stmt->bind_param('i', $equipo['codEqui']);
    $stmt->execute();
    $jugadores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Jugadores del Equipo | Staff | LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    <main class="container-fluid px-3 py-4">
        <h1 class="h3 mb-3">Jugadores de mi equipo</h1>

        <?php if ($mensaje !== '') : ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if ($error !== '') : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($equipo) : ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h5">Registrar nuevo jugador en <?php echo htmlspecialchars($equipo['nomEqui']); ?></h2>
                    <form method="post" class="row gy-3 gx-3 align-items-end">
                        <input type="hidden" name="accion" value="crear">
                        <div class="col-md-4">
                            <label class="form-label">Nombre</label>
                            <input class="form-control" name="nomJug" type="text" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Edad</label>
                            <input class="form-control" name="edaJug" type="number" min="14">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Altura</label>
                            <input class="form-control" name="altJug" type="text" placeholder="1.90">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table id="playersTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Edad</th>
                                <th>Altura</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jugadores as $jugador) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($jugador['nomJug']); ?></td>
                                    <td><?php echo htmlspecialchars($jugador['edaJug']); ?></td>
                                    <td><?php echo htmlspecialchars($jugador['altJug']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else : ?>
            <div class="alert alert-info">No tienes un equipo asignado. Pide a un administrador que te asigne uno.</div>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/../sections/footer.php'; ?>
    <script src="../assets/themes/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#playersTable').DataTable({
                "language": {
                    "url": "../assets/js/spanish.json"
                }
            });
        });
    </script>
</body>
</html>