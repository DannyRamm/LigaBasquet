<?php
// Script temporal para crear usuarios de prueba
// Ejecutar una vez y luego borrar

include __DIR__ . '/config/conexion.php';
include __DIR__ . '/config/funciones.php';

prepararTablaUsuario($conector);

$roles = [
    'Administrador',
    'Staff Técnico',
    'Jugador',
    'Usuario'
];

$roleIds = [];
foreach ($roles as $nombreRol) {
    $stmt = $conector->prepare('SELECT codRol FROM Rol WHERE nomRol = ? LIMIT 1');
    $stmt->bind_param('s', $nombreRol);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();

    if ($resultado) {
        $roleIds[$nombreRol] = (int) $resultado['codRol'];
    } else {
        $stmt = $conector->prepare('INSERT INTO Rol (nomRol) VALUES (?)');
        $stmt->bind_param('s', $nombreRol);
        $stmt->execute();
        $roleIds[$nombreRol] = (int) $conector->insert_id;
    }
}

$usuarios = [
    [
        'nomUsu' => 'Admin User',
        'corUsu' => 'admin@leaguedan.com',
        'pasUsu' => password_hash('admin123', PASSWORD_DEFAULT),
        'rol' => 'Administrador'
    ],
    [
        'nomUsu' => 'Staff User',
        'corUsu' => 'staff@leaguedan.com',
        'pasUsu' => password_hash('staff123', PASSWORD_DEFAULT),
        'rol' => 'Staff Técnico'
    ],
    [
        'nomUsu' => 'Player User',
        'corUsu' => 'player@leaguedan.com',
        'pasUsu' => password_hash('player123', PASSWORD_DEFAULT),
        'rol' => 'Jugador'
    ],
    [
        'nomUsu' => 'Normal User',
        'corUsu' => 'user@leaguedan.com',
        'pasUsu' => password_hash('user123', PASSWORD_DEFAULT),
        'rol' => 'Usuario'
    ]
];

foreach ($usuarios as $usuario) {
    $codRol = $roleIds[$usuario['rol']] ?? $roleIds['Usuario'];
    $stmt = $conector->prepare('INSERT IGNORE INTO Usuario (nomUsu, corUsu, pasUsu, codRol, fecRegUsu) VALUES (?, ?, ?, ?, NOW())');
    $stmt->bind_param('sssi', $usuario['nomUsu'], $usuario['corUsu'], $usuario['pasUsu'], $codRol);
    $stmt->execute();
}

echo "Usuarios de prueba creados exitosamente.<br>";
echo "Admin: admin@leaguedan.com / admin123 → Dashboard Admin<br>";
echo "Staff: staff@leaguedan.com / staff123 → Dashboard Staff<br>";
echo "Player: player@leaguedan.com / player123 → Dashboard Player<br>";
echo "User: user@leaguedan.com / user123 → Página principal con dropdown<br>";
?>