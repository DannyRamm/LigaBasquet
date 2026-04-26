<?php
/*	function encrypt($string,$key){
		$result = '';
		for($i=0;$i<strlen($string);$i++){
			$char = substr($string,$i,1);
			$keychar = substr($key, ($i % strlen($key))-1,1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;
		}
		return base64_encode($result);
	}
	function decrypt($string,$key){
		$result = '';
		$string = base64_decode($string);
		for($i=0;$i<strlen($string);$i++){
			$char = substr($string,$i,1);
			$keychar = substr($key, ($i % strlen($key))-1,1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
		}
		return $result;
	}
	//Prueba del encriptador
	/*$pass = "admin456";
	$pass_enc = encrypt($pass,"LigB");
	echo $pass_enc.'<br>';
	$pass_dec = decrypt($pass_enc,"LigB");
	echo $pass_dec.'<br>';*/
	
	/*function redireccionar($url,$tiempo=0){
		if(!headers_sent()){
			if($tiempo ==0){
				header("Location: $url");
			}else{
				header("Refresh:$tiempo; URL=$url");
			}
		}else{
			echo "
			<script>
				setTimeout(function(){
					window.location.href = '$url';
				},".($tiempo*1000).");
			</script>
			";
		}
	}

	function limpiarDato($dato){
		return htmlspecialchars(strip_tags(trim($dato)),ENT_QUOTES, 'UTF-8');
	}
	// FUNCIÓN MEJORADA PARA VALIDAR DIFERENTES TIPOS DE DOCUMENTOS
	function validarDocumento($documento, $tipoDocumento){
		$documento = trim($documento);
		
		switch($tipoDocumento){
			case '1': // DNI (Perú)
				return preg_match('/^\d{8}$/', $documento);
				
			case '2': // Carnet de Extranjería
				// Formato: Letra + 8-9 dígitos (ej: A12345678, B123456789)
				return preg_match('/^[A-Za-z]\d{8,9}$/', $documento);
				
			case '3': // Pasaporte
				// Formato: Letra + 6-8 dígitos (ej: A123456, AB1234567)
				return preg_match('/^[A-Za-z]{1,2}\d{6,8}$/', $documento);
				
			default:
				return false;
		}
	}
	function validarDatos($datos){
		return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',$datos);
	}
	function validarCel($cel){
		$cel = trim($cel);
		return preg_match('/^9\d{8}$/',$cel);
	}
	function validarCorreo($mail){
		return filter_var($mail, FILTER_VALIDATE_EMAIL) !== false;
	}
	// FUNCIÓN PARA OBTENER EL NOMBRE DEL TIPO DE DOCUMENTO
	function obtenerTipoDocumento($codigo){
		$tipos = [
			'1' => 'DNI',
			'2' => 'Carnet de Extranjería',
			'3' => 'Pasaporte'
		];
		return $tipos[$codigo] ?? 'Desconocido';
	}*/
?>