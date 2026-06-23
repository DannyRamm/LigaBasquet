<?php
include __DIR__ . '/config/conexion.php';

$res = $conector->query('SELECT codUsu, nomUsu, corUsu, pasUsu, codRol FROM Usuario');
while ($row = $res->fetch_assoc()) {
    echo implode('|', $row) . "\n";
}

$res2 = $conector->query('SELECT codRol, nomRol FROM Rol');
while ($row = $res2->fetch_assoc()) {
    echo 'ROL|' . implode('|', $row) . "\n";
}
?>