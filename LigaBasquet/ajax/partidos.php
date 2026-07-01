<?php
include __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json');

$partidos = [];
$sql = 'SELECT p.codMat, p.fecMat, p.estMat, p.punlocMat, p.punvisMat, t.nomTem, cl.nomCan AS cancha, el.nomEqui AS local, ev.nomEqui AS visitante
    FROM Partido p
    LEFT JOIN Temporada t ON t.codTem = p.codTem
    LEFT JOIN Cancha cl ON cl.codCan = p.codCan
    LEFT JOIN Equipo el ON el.codEqui = p.codequilocMat
    LEFT JOIN Equipo ev ON ev.codEqui = p.codequivisMat
    ORDER BY p.fecMat DESC';
$result = $conector->query($sql);
if ($result) {
    $partidos = $result->fetch_all(MYSQLI_ASSOC);
}

echo json_encode($partidos);
?>