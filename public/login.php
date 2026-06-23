<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header('Location: index.php?page=home');
    exit;
}

include __DIR__ . '/config/funciones.php';

$error = '';
$correo = '';
$mensaje = isset($_GET['registro']) && $_GET['registro'] === 'ok'
    ? 'Cuenta creada correctamente. Ahora inicia sesión con tu LeagueDan ID.'
    : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = limpiarDato($_POST['correo'] ?? '');
    $passwordIngresado = $_POST['password'] ?? '';

    if ($correo === '' || $passwordIngresado === '') {
        $error = 'Ingresa tu correo y contraseña.';
    } else {
        include __DIR__ . '/config/conexion.php';
        prepararTablaUsuario($conector);

        $sql = "SELECT u.codUsu, u.nomUsu, u.corUsu, u.pasUsu, u.codRol, r.nomRol
                FROM Usuario u
                LEFT JOIN Rol r ON r.codRol = u.codRol
                WHERE u.corUsu = ?
                LIMIT 1";
        $stmt = $conector->prepare($sql);
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        $password_ok = false;
        if ($usuario) {
            $password_ok = password_verify($passwordIngresado, $usuario['pasUsu']) || hash_equals($usuario['pasUsu'], $passwordIngresado);
        }

        if ($password_ok) {
            iniciarSesionUsuario($usuario);
            $rolTipo = obtenerTipoRol($usuario['nomRol'] ?? '', $usuario['codRol']);
            switch ($rolTipo) {
                case 'admin':
                    header('Location: admin/dashboard.php');
                    break;
                case 'staff':
                    header('Location: staff/dashboard.php');
                    break;
                case 'player':
                    header('Location: player/dashboard.php');
                    break;
                default:
                    header('Location: index.php?page=home');
                    break;
            }
            exit;
        }

        $error = 'Correo o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - LeagueDan ID</title>
    <link rel="stylesheet" href="assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <main class="login-page">
        <section class="auth-shell">
            <div class="row g-0">
                <div class="col-lg-6">
                    <div class="login-card h-100">
                        <a class="d-inline-flex align-items-center gap-2 text-decoration-none text-reset mb-4" href="index.php?page=home">
                            <span class="brand-mark">LD</span>
                            <span class="fw-bold">LeagueDan ID</span>
                        </a>

                        <h1 class="h3 fw-bold">Iniciar sesión</h1>
                        <p class="text-secondary">Accede para guardar favoritos, equipos, historial y watchlist.</p>

                        <?php if ($mensaje !== '') : ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
                        <?php endif; ?>

                        <?php if ($error !== '') : ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form method="post" autocomplete="on">
                            <div class="mb-3">
                                <label class="form-label" for="correo">E-mail</label>
                                <input class="form-control" id="correo" name="correo" type="email" value="<?php echo htmlspecialchars($correo); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password">Contraseña</label>
                                <div class="input-group">
                                    <input class="form-control" id="password" name="password" type="password" required>
                                    <button class="btn btn-outline-secondary" type="button" data-toggle-password="#password">Mostrar</button>
                                </div>
                            </div>
                            <button class="btn btn-danger w-100 fw-bold" type="submit">Entrar</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="nba-id-panel h-100">
                        <span class="badge text-bg-danger mb-3">Nuevo usuario</span>
                        <h2 class="display-6 fw-bold">Crea tu NBA ID gratis</h2>
                        <p>Inicia sesión y sácale el máximo partido con los sorteos de entradas, ventajas adicionales para los eventos de LeagueDan y mucho más con LeagueDan ID.</p>
                        <a class="btn btn-light fw-bold" href="registro.php">Crear cuenta</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="assets/js/auth.js"></script>
</body>
</html>
