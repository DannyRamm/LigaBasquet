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
    $accion = $_POST['accion'] ?? '';
    $codNot = (int) ($_POST['codNot'] ?? 0);
    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $fecha = $_POST['fecha'] ?? date('Y-m-d');

    if ($accion === 'crear' && $titulo !== '') {
        $stmt = $conector->prepare('INSERT INTO Noticia (tituloNot, contenidoNot, fechaNot) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $titulo, $contenido, $fecha);
        if ($stmt->execute()) {
            $mensaje = 'Noticia creada correctamente.';
        } else {
            $error = 'No se pudo crear la noticia.';
        }
    }

    if ($accion === 'editar' && $codNot > 0 && $titulo !== '') {
        $stmt = $conector->prepare('UPDATE Noticia SET tituloNot = ?, contenidoNot = ?, fechaNot = ? WHERE codNot = ?');
        $stmt->bind_param('sssi', $titulo, $contenido, $fecha, $codNot);
        if ($stmt->execute()) {
            $mensaje = 'Noticia actualizada correctamente.';
        } else {
            $error = 'No se pudo actualizar la noticia.';
        }
    }

    if ($accion === 'eliminar' && $codNot > 0) {
        $stmt = $conector->prepare('DELETE FROM Noticia WHERE codNot = ?');
        $stmt->bind_param('i', $codNot);
        if ($stmt->execute()) {
            $mensaje = 'Noticia eliminada correctamente.';
        } else {
            $error = 'No se pudo eliminar la noticia.';
        }
    }
}

$noticias = [];
$result = $conector->query('SELECT codNot, tituloNot, contenidoNot, fechaNot FROM Noticia ORDER BY fechaNot DESC');
if ($result) {
    $noticias = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Noticias - Admin | LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    <main class="container-fluid px-3 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3">Gestión de Noticias</h1>
                <p class="text-secondary">Publica y administra las noticias de la liga.</p>
            </div>
        </div>

        <?php if ($mensaje !== '') : ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if ($error !== '') : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <form method="post" class="row gy-3 gx-3">
                    <input type="hidden" name="accion" value="crear">
                    <div class="col-md-4">
                        <label class="form-label">Título</label>
                        <input class="form-control" name="titulo" type="text" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contenido</label>
                        <textarea class="form-control" name="contenido" rows="2"></textarea>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha</label>
                        <input class="form-control" type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-12">
                        <button class="btn btn-primary" type="submit">Publicar Noticia</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="noticiasTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Contenido</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($noticias as $noticia) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($noticia['tituloNot']); ?></td>
                                <td><?php echo htmlspecialchars(substr($noticia['contenidoNot'], 0, 50)); ?>...</td>
                                <td><?php echo htmlspecialchars($noticia['fechaNot']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#edit-<?php echo $noticia['codNot']; ?>">Editar</button>
                                    <form method="post" class="d-inline-block" onsubmit="return confirm('Eliminar noticia?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="codNot" value="<?php echo $noticia['codNot']; ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="collapse" id="edit-<?php echo $noticia['codNot']; ?>">
                                <td colspan="4">
                                    <form method="post" class="row gy-2 gx-3 align-items-end">
                                        <input type="hidden" name="accion" value="editar">
                                        <input type="hidden" name="codNot" value="<?php echo $noticia['codNot']; ?>">
                                        <div class="col-md-4">
                                            <label class="form-label">Título</label>
                                            <input class="form-control" type="text" name="titulo" value="<?php echo htmlspecialchars($noticia['tituloNot']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Contenido</label>
                                            <textarea class="form-control" name="contenido" rows="2"><?php echo htmlspecialchars($noticia['contenidoNot']); ?></textarea>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Fecha</label>
                                            <input class="form-control" type="date" name="fecha" value="<?php echo htmlspecialchars($noticia['fechaNot']); ?>">
                                        </div>
                                        <div class="col-md-12">
                                            <button class="btn btn-success" type="submit">Actualizar</button>
                                        </div>
                                    </form>
                                </td>
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
            $('#noticiasTable').DataTable({
                "language": {
                    "url": "../assets/js/spanish.json"
                }
            });
        });
    </script>
</body>
</html>