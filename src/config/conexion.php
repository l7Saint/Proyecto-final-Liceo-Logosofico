<?php
$host = "mariadb"
$bd = "urbanaut";
$usuario = "urbanaut_user";
$password = "urbanaut123";

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
