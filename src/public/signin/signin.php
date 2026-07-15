<?php
$DIAS = array(
	"Lunes",
	"Martes",
	"Miercoles",
	"Jueves",
	"Viernes"
);
$HORAS = array(
	"8:00 - 8:40",  // 0
	"8:40 - 9:20",  // 1
	"9:25 - 10:05", // 2
	"10:15 - 10:55",// 3
	"11:00 - 11:40",// 4
	"11:00 - 11:40",// 5
	"11:45 - 12:25",// 6
	"12:25 - 13:05",// 7
	"13:05 - 13:45",// 8
	"13:50 - 14:30",// 9
	"14:30 - 15:10",// 10
	"15:15 - 15:55",// 11
	"15:55 - 16:35" // 12
);

$nombrePersona = $_POST['nombre'];
$apellidoPersona = $_POST['apellido'];
$email = $_POST['email'];
$contrasenaUsuario = $_POST['contraseña'];
$marca_modeloAuto = $_POST['marca_modelo'];

$horarios = [];
/*count() cuenta los elementos del array*/
for($ndia = 0; $ndia < count($DIAS); $ndia++){
	/*array_push() es una funcion que le añade al final del array el elemento que le des*/
	$horariosDiaPOST = "horarios" . $DIAS[$ndia];
	array_push($horarios, $_POST[$horariosDiaPOST] ?? []); /*Los signos de preguntan funcionan como un if, si el usuario puso una opción recibe el dato, si lo dejó vacio, para que no de un error, lo deja vacío.*/
}

require_once("/config/conexion.php");
$sql = "INSERT INTO Usuario(nombre, apellido, email, contrasena)
	VALUES (?, ?, ?)"

$stmt = conexion->prepare($sql);
$stmt->execute([$nombrePersona, $apellidoPersona, $email, $contrasenaUsuario]};
$header("Location: /public/login/index.html");
?>


