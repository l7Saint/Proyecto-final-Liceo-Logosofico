<?php
$host = "urbanaut_mariadb";
$bd = "urbanaut";
$usuario = "root";
$password = "root";

try {
	$conexion = new PDO(
		"mysql:host=$host;dbname=$bd",
		$usuario,
		$password
	);

	$conexion->setAttribute(
		PDO::ATTR_ERRMODE,
		PDO::ERRMODE_EXCEPTION
	);
} catch(PDOException $e){
	error_log("Error en config.conexion: ".$e->getMessage());
	http_response_code(500);
	die();
}
