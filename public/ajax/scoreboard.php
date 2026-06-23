<?php
include __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json');

$partidos = [];
$sql = 'SELECT p.codMat, p.fecMat, p.estMat, p.punlocMat, p.punvisMat, el.nomEqui AS local, ev.nomEqui AS visitante
    FROM Partido p
    LEFT JOIN Equipo el ON el.codEqui = p.codequilocMat
    LEFT JOIN Equipo ev ON ev.codEqui = p.codequivisMat
    WHERE p.estMat = "jugado"
    ORDER BY p.fecMat DESC';
$result = $conector->query($sql);
if ($result) {
    $partidos = $result->fetch_all(MYSQLI_ASSOC);
}

echo json_encode($partidos);
?>