<?php
	include_once 'bd.php';

	$conector = new mysqli($servidor, $usuario, $password, $basededatos, $puerto);

	// Verificar conexión
	if ($conector->connect_error) {
		die("Error de conexión: " . $conector->connect_error);
	}

	// Configurar codificación
	$conector->set_charset("utf8");

	// (Opcional) mensaje de prueba
	// echo "Conectado correctamente";
?>