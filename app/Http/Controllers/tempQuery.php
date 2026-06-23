<?php
$mysqli = new mysqli('127.0.0.1', 'root', '1234', 'basquet');
if ($mysqli->connect_error) {
    echo 'CONNERR: ' . $mysqli->connect_error . "\n";
    exit(1);
}
$res = $mysqli->query("SELECT codEqui, nomEqui, ciuEqui, COUNT(*) AS c FROM Equipo GROUP BY codEqui, nomEqui, ciuEqui HAVING c > 1");
if (!$res) {
    echo 'ERR: ' . $mysqli->error . "\n";
    exit(1);
}
while ($row = $res->fetch_assoc()) {
    echo $row['codEqui'] . ' | ' . $row['nomEqui'] . ' | ' . $row['ciuEqui'] . ' | ' . $row['c'] . "\n";
}
$res2 = $mysqli->query('SELECT COUNT(*) AS total FROM Equipo');
$row2 = $res2->fetch_assoc();
echo 'TOTAL: ' . $row2['total'] . "\n";
?>
