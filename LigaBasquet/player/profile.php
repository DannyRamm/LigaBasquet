<?php
session_start();
include __DIR__ . '/../config/conexion.php';
include __DIR__ . '/../config/funciones.php';

if (!esPlayer()) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $pais = trim($_POST['pais'] ?? 'Perú');
    $password = $_POST['password'] ?? '';

    if ($nombre === '') {
        $error = 'El nombre no puede estar vacío.';
    } else {
        if ($password !== '') {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conector->prepare('UPDATE Usuario SET nomUsu = ?, apeUsu = ?, paisUsu = ?, pasUsu = ? WHERE codUsu = ?');
            $stmt->bind_param('ssssi', $nombre, $apellido, $pais, $passwordHash, $_SESSION['usuario']['id']);
        } else {
            $stmt = $conector->prepare('UPDATE Usuario SET nomUsu = ?, apeUsu = ?, paisUsu = ? WHERE codUsu = ?');
            $stmt->bind_param('sssi', $nombre, $apellido, $pais, $_SESSION['usuario']['id']);
        }

        if ($stmt->execute()) {
            $mensaje = 'Perfil actualizado correctamente.';
            $_SESSION['usuario']['nombre'] = $nombre;
        } else {
            $error = 'No se pudo actualizar el perfil.';
        }
    }
}

$stmt = $conector->prepare('SELECT nomUsu, apeUsu, corUsu, paisUsu FROM Usuario WHERE codUsu = ? LIMIT 1');
$stmt->bind_param('i', $_SESSION['usuario']['id']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Jugador | LeagueDan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body data-theme="light">
    <?php include __DIR__ . '/sections/header.php'; ?>
    <main class="container-fluid px-3 py-4">
        <h1 class="h3 mb-3">Perfil de Jugador</h1>

        <?php if ($mensaje !== '') : ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if ($error !== '') : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <form method="post" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre</label>
                        <input class="form-control" name="nombre" type="text" value="<?php echo htmlspecialchars($usuario['nomUsu']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Apellido</label>
                        <input class="form-control" name="apellido" type="text" value="<?php echo htmlspecialchars($usuario['apeUsu']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Correo</label>
                        <input class="form-control" type="email" value="<?php echo htmlspecialchars($usuario['corUsu']); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">País</label>
                        <input class="form-control" name="pais" type="text" value="<?php echo htmlspecialchars($usuario['paisUsu'] ?: 'Perú'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nueva contraseña</label>
                        <input class="form-control" name="password" type="password" placeholder="Dejar vacío para no cambiar">
                    </div>
                    <div class="col-md-6 align-self-end">
                        <button class="btn btn-primary w-100">Guardar perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/../sections/footer.php'; ?>
    <script src="../assets/themes/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
</body>
</html>