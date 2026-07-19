<?php
$host = "127.0.0.1";
$bd = "urbanaut";
$usuario = "root";
$password = "";

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
	die("Error de conexion:" . $e->getMessage());
}
