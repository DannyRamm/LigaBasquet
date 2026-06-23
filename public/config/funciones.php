<?php
function limpiarDato($dato)
{
    return htmlspecialchars(strip_tags(trim((string) $dato)), ENT_QUOTES, 'UTF-8');
}

function validarCorreo($correo)
{
    return filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
}

function validarPasswordNBAID($password)
{
    return strlen($password) >= 8
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[^A-Za-z0-9]/', $password);
}

function asegurarColumna($conector, $tabla, $columna, $definicion)
{
    $tabla = $conector->real_escape_string($tabla);
    $columna = $conector->real_escape_string($columna);
    $sql = "SHOW COLUMNS FROM `$tabla` LIKE '$columna'";
    $resultado = $conector->query($sql);

    if ($resultado && $resultado->num_rows === 0) {
        $conector->query("ALTER TABLE `$tabla` ADD COLUMN $definicion");
    }
}

function prepararTablaUsuario($conector)
{
    asegurarColumna($conector, 'Usuario', 'apeUsu', 'apeUsu VARCHAR(100) NULL AFTER nomUsu');
    asegurarColumna($conector, 'Usuario', 'mesNacUsu', 'mesNacUsu TINYINT NULL AFTER pasUsu');
    asegurarColumna($conector, 'Usuario', 'anioNacUsu', 'anioNacUsu SMALLINT NULL AFTER mesNacUsu');
    asegurarColumna($conector, 'Usuario', 'paisUsu', "paisUsu VARCHAR(80) NULL DEFAULT 'Perú' AFTER anioNacUsu");
    asegurarColumna($conector, 'Usuario', 'aceptaTerminosUsu', 'aceptaTerminosUsu TINYINT(1) NOT NULL DEFAULT 0 AFTER paisUsu');
    asegurarColumna($conector, 'Usuario', 'aceptaMarketingUsu', 'aceptaMarketingUsu TINYINT(1) NOT NULL DEFAULT 0 AFTER aceptaTerminosUsu');
    asegurarColumna($conector, 'Usuario', 'fecRegUsu', 'fecRegUsu DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER aceptaMarketingUsu');
}

function obtenerRolUsuario($conector)
{
    $nombreRol = 'Usuario';
    $stmt = $conector->prepare('SELECT codRol FROM Rol WHERE nomRol = ? LIMIT 1');
    $stmt->bind_param('s', $nombreRol);
    $stmt->execute();
    $rol = $stmt->get_result()->fetch_assoc();

    if ($rol) {
        return (int) $rol['codRol'];
    }

    $stmt = $conector->prepare('INSERT INTO Rol (nomRol) VALUES (?)');
    $stmt->bind_param('s', $nombreRol);
    $stmt->execute();

    return (int) $conector->insert_id;
}

function obtenerTipoRol($nomRol, $codRol)
{
    $rol = mb_strtolower((string) $nomRol, 'UTF-8');

    if (strpos($rol, 'administrador') !== false || strpos($rol, 'admin') !== false) {
        return 'admin';
    }

    if (strpos($rol, 'staff') !== false || strpos($rol, 'entrenador') !== false) {
        return 'staff';
    }

    if (strpos($rol, 'jugador') !== false) {
        return 'player';
    }

    return 'user';
}

function esAdmin()
{
    return isset($_SESSION['usuario']['rolTipo']) && $_SESSION['usuario']['rolTipo'] === 'admin';
}

function esStaff()
{
    return isset($_SESSION['usuario']['rolTipo']) && $_SESSION['usuario']['rolTipo'] === 'staff';
}

function esPlayer()
{
    return isset($_SESSION['usuario']['rolTipo']) && $_SESSION['usuario']['rolTipo'] === 'player';
}

function obtenerEquipoUsuario($conector, $codUsu)
{
    $stmt = $conector->prepare('SELECT e.codEqui, e.nomEqui, e.ciuEqui, ue.rolequiUsu FROM Usuario_Equipo ue JOIN Equipo e ON ue.codEqui = e.codEqui WHERE ue.codUsu = ? LIMIT 1');
    $stmt->bind_param('i', $codUsu);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function obtenerEquiposUsuario($conector, $codUsu)
{
    $stmt = $conector->prepare('SELECT e.codEqui, e.nomEqui, e.ciuEqui, ue.rolequiUsu FROM Usuario_Equipo ue JOIN Equipo e ON ue.codEqui = e.codEqui WHERE ue.codUsu = ?');
    $stmt->bind_param('i', $codUsu);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function iniciarSesionUsuario($usuario)
{
    $rolTipo = obtenerTipoRol($usuario['nomRol'] ?? '', $usuario['codRol']);

    $_SESSION['usuario'] = [
        'id' => $usuario['codUsu'],
        'nombre' => $usuario['nomUsu'] ?: $usuario['corUsu'],
        'correo' => $usuario['corUsu'],
        'rolNombre' => $usuario['nomRol'] ?? 'Usuario',
        'rolTipo' => $rolTipo,
        'codRol' => $usuario['codRol']
    ];
}
