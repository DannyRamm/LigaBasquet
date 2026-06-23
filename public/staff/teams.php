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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $equipo) {
    $nomEqui = trim($_POST['nomEqui'] ?? '');
    $ciuEqui = trim($_POST['ciuEqui'] ?? '');
    if ($nomEqui !== '') {
        $stmt = $conector->prepare('UPDATE Equipo SET nomEqui = ?, ciuEqui = ? WHERE codEqui = ?');
        $stmt->bind_param('ssi', $nomEqui, $ciuEqui, $equipo['codEqui']);
        if ($stmt->execute()) {
            $mensaje = 'Equipo actualizado.';
            $equipo = obtenerEquipoUsuario($conector, $_SESSION['usuario']['id']);
        } else {
            $error = 'No se pudo guardar el equipo.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Equipo Staff | LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    <main class="container-fluid px-3 py-4">
        <h1 class="h3 mb-3">Mi Equipo</h1>
        <?php if ($mensaje !== '') : ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if ($error !== '') : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($equipo) : ?>
            <div class="card mb-4">
                <div class="card-body">
                    <form method="post">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre del equipo</label>
                                <input class="form-control" name="nomEqui" type="text" value="<?php echo htmlspecialchars($equipo['nomEqui']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ciudad</label>
                                <input class="form-control" name="ciuEqui" type="text" value="<?php echo htmlspecialchars($equipo['ciuEqui']); ?>">
                            </div>
                            <div class="col-md-2 align-self-end">
                                <button class="btn btn-primary w-100">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php else : ?>
            <div class="alert alert-info">No tienes un equipo asignado. Pide a un administrador que te asigne uno.</div>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/../sections/footer.php'; ?>
    <script src="../assets/themes/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
</body>
</html>