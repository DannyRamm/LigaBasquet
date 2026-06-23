<?php
session_start();
include __DIR__ . '/../config/conexion.php';
include __DIR__ . '/../config/funciones.php';

if (!esAdmin()) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clave = trim($_POST['claveConf'] ?? '');
    $valor = trim($_POST['valorConf'] ?? '');
    $descripcion = trim($_POST['descripcionConf'] ?? '');

    if ($clave !== '') {
        $stmt = $conector->prepare('INSERT INTO Configuracion (claveConf, valorConf, descripcionConf) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE valorConf = ?, descripcionConf = ?');
        $stmt->bind_param('sssss', $clave, $valor, $descripcion, $valor, $descripcion);
        if ($stmt->execute()) {
            $mensaje = 'Configuración guardada.';
        } else {
            $error = 'Error al guardar.';
        }
    }
}

$configuraciones = [];
$result = $conector->query('SELECT * FROM Configuracion ORDER BY claveConf');
if ($result) {
    $configuraciones = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Configuración - Admin | LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    <main class="container-fluid px-3 py-4">
        <h1 class="h3 mb-3">Configuración del Sistema</h1>

        <?php if ($mensaje !== '') : ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if ($error !== '') : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <form method="post" class="row gy-3 gx-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Clave</label>
                        <input class="form-control" name="claveConf" type="text" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Valor</label>
                        <input class="form-control" name="valorConf" type="text">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Descripción</label>
                        <input class="form-control" name="descripcionConf" type="text">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary w-100" type="submit">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="configTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Valor</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($configuraciones as $conf) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($conf['claveConf']); ?></td>
                                <td><?php echo htmlspecialchars($conf['valorConf']); ?></td>
                                <td><?php echo htmlspecialchars($conf['descripcionConf']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/../sections/footer.php'; ?>
    <script src="../assets/themes/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#configTable').DataTable({
                "language": {
                    "url": "../assets/js/spanish.json"
                }
            });
        });
    </script>
</body>
</html>