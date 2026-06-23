<?php
session_start();

include __DIR__ . '/config/funciones.php';
include __DIR__ . '/config/conexion.php';

prepararTablaUsuario($conector);

$errores = [];
$form = [
    'correo' => '',
    'nombre' => '',
    'apellido' => '',
    'mes' => '',
    'anio' => '',
    'pais' => 'Perú',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['correo'] = limpiarDato($_POST['correo'] ?? '');
    $form['nombre'] = limpiarDato($_POST['nombre'] ?? '');
    $form['apellido'] = limpiarDato($_POST['apellido'] ?? '');
    $form['mes'] = limpiarDato($_POST['mes'] ?? '');
    $form['anio'] = limpiarDato($_POST['anio'] ?? '');
    $form['pais'] = limpiarDato($_POST['pais'] ?? 'Perú');
    $password = $_POST['password'] ?? '';
    $aceptaTerminos = isset($_POST['acepta_terminos']) ? 1 : 0;
    $aceptaMarketing = isset($_POST['acepta_marketing']) ? 1 : 0;

    if (!validarCorreo($form['correo'])) {
        $errores[] = 'Ingresa un correo válido.';
    }

    if (!validarPasswordNBAID($password)) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres, 1 letra mayúscula y 1 símbolo.';
    }

    if (!$aceptaTerminos) {
        $errores[] = 'Debes aceptar las condiciones de uso y la política de privacidad.';
    }

    $stmt = $conector->prepare('SELECT codUsu FROM Usuario WHERE corUsu = ? LIMIT 1');
    $stmt->bind_param('s', $form['correo']);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        $errores[] = 'Este correo ya está registrado. Inicia sesión con tu cuenta.';
    }

    if (empty($errores)) {
        $rolUsuario = obtenerRolUsuario($conector);
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $mes = $form['mes'] !== '' ? (int) $form['mes'] : null;
        $anio = $form['anio'] !== '' ? (int) $form['anio'] : null;

        $sql = "INSERT INTO Usuario
                    (nomUsu, apeUsu, corUsu, pasUsu, mesNacUsu, anioNacUsu, paisUsu, aceptaTerminosUsu, aceptaMarketingUsu, codRol)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conector->prepare($sql);
        $stmt->bind_param(
            'ssssiisiii',
            $form['nombre'],
            $form['apellido'],
            $form['correo'],
            $passwordHash,
            $mes,
            $anio,
            $form['pais'],
            $aceptaTerminos,
            $aceptaMarketing,
            $rolUsuario
        );
        $stmt->execute();

        header('Location: login.php?registro=ok');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear LeagueDan ID</title>
    <link rel="stylesheet" href="assets/themes/bootstrap-5.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/themes/Font-Awesome-6.7.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <main class="register-page">
        <section class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="register-card">
                        <a class="d-inline-flex align-items-center gap-2 text-decoration-none text-reset mb-4" href="login.php">
                            <span class="brand-mark">LD</span>
                            <span class="fw-bold">LeagueDan ID</span>
                        </a>

                        <h1 class="h3 fw-bold">Crea tu NBA ID gratis</h1>
                        <p class="text-secondary">Inicia sesión y sácale el máximo partido con sorteos de entradas, ventajas adicionales para eventos y mucho más.</p>

                        <?php if (!empty($errores)) : ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errores as $error) : ?>
                                    <div><?php echo htmlspecialchars($error); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" autocomplete="on">
                            <div class="mb-3">
                                <label class="form-label" for="correo">E-mail</label>
                                <input class="form-control" id="correo" name="correo" type="email" value="<?php echo htmlspecialchars($form['correo']); ?>" placeholder="srdannyramirez5@gmail.com" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">Contraseña</label>
                                <div class="input-group">
                                    <input class="form-control" id="password" name="password" type="password" required>
                                    <button class="btn btn-outline-secondary" type="button" data-toggle-password="#password">Mostrar</button>
                                </div>
                                <div class="form-text">Debe tener al menos 8 caracteres e incluir 1 letra mayúscula y 1 símbolo.</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="nombre">Nombre (opcional)</label>
                                    <input class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($form['nombre']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="apellido">Apellido (opcional)</label>
                                    <input class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($form['apellido']); ?>">
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label class="form-label" for="mes">Fecha de nacimiento (MM)</label>
                                    <select class="form-select" id="mes" name="mes">
                                        <option value="">Mes</option>
                                        <?php for ($i = 1; $i <= 12; $i++) : ?>
                                            <option value="<?php echo $i; ?>" <?php echo (string) $form['mes'] === (string) $i ? 'selected' : ''; ?>><?php echo str_pad((string) $i, 2, '0', STR_PAD_LEFT); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="anio">Fecha de nacimiento (AAAA)</label>
                                    <select class="form-select" id="anio" name="anio">
                                        <option value="">Año</option>
                                        <?php for ($i = (int) date('Y') - 10; $i >= 1940; $i--) : ?>
                                            <option value="<?php echo $i; ?>" <?php echo (string) $form['anio'] === (string) $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label" for="pais">País/Región/Territorio</label>
                                <select class="form-select" id="pais" name="pais">
                                    <?php foreach (['Perú', 'Argentina', 'Bolivia', 'Chile', 'Colombia', 'Ecuador', 'México'] as $pais) : ?>
                                        <option value="<?php echo htmlspecialchars($pais); ?>" <?php echo $form['pais'] === $pais ? 'selected' : ''; ?>><?php echo htmlspecialchars($pais); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-check mt-3">
                                <input class="form-check-input" id="acepta_terminos" name="acepta_terminos" type="checkbox" required>
                                <label class="form-check-label" for="acepta_terminos">Acepto las condiciones de uso y la política de privacidad.</label>
                            </div>

                            <div class="form-check mt-2">
                                <input class="form-check-input" id="acepta_marketing" name="acepta_marketing" type="checkbox">
                                <label class="form-check-label" for="acepta_marketing">Consiento que LeagueDan use mis datos personales para enviarme mensajes y anuncios sobre productos e iniciativas de LeagueDan y sus socios.</label>
                            </div>

                            <button class="btn btn-danger w-100 fw-bold mt-4" type="submit">Crear cuenta</button>
                            <a class="btn btn-link w-100 mt-2" href="login.php">Ya tengo cuenta, iniciar sesión</a>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="assets/js/auth.js"></script>
</body>
</html>
