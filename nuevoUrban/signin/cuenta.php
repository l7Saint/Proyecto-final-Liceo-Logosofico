<?php

$nombrePersona = $_POST['nombre'];
$apellidoPersona = $_POST['apellido'];
$email = $_POST['email'];
$contrasenaUsuario = $_POST['contraseña'];
$marca_modeloAuto = $_POST['marca_modelo'];


require_once("/config/conexion.php");
$sql = "INSERT INTO Usuario(nombre, apellido, email, contrasena)
	VALUES (?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->execute([$nombrePersona, $apellidoPersona, $email, $contrasenaUsuario]);
header("Location: /public/login/index.html");
?>


