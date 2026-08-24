<?php
require_once("/config/conexion.php");

$nombrePersona = $_POST['nombre'];
$apellidoPersona = $_POST['apellido'];
$correoElectronico = $_POST['email'];
$contrasenaUsuario = $_POST['contrasena'];

$sql = "INSERT INTO Usuario(nombre, apellido, email, contrasena)
VALUES (?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->execute([$nombrePersona, $apellidoPersona, $correoElectronico, $contrasenaUsuario]);

header("Location: /signin/horarios.php");




