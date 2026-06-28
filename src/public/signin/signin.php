<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Datos</title>
	<link rel="stylesheet" href="../../assets/css/main_style.css"/>
	<style>
#cajaDatos{
  background-color: whitesmoke;
      border-radius: 15px;
      padding-top: 1rem;
      padding-bottom: 1rem;
      width: 30rem;
}

.etiqueta{
       color: #67878a;
      font-family: "Open Sans", sans-serif;
}

.btn{
      color: #285357;
      font-weight: bolder;
      cursor: pointer;
      background-color: #67878a;
      border-radius: 10px;
      border: 2px 2px solid;
      border-color: #67878a;
      padding: 0.5rem;
}

.btn:hover{
    background-color: #455c5e;
      color: whitesmoke;
}

.textoPregunta{
    color:black;
    font-family: "Open Sans", sans-serif;
    font-weight: bold;
    text-decoration: underline;

}
</style>
</head>
<body>
    <center>
    <div id="cajaDatos">

    <h1 class = "titulos">Datos de registro:</h1>
    <p class="textoPregunta">¿Estos datos son correctos?</p>

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
$contraseñaUsuario = $_POST['contraseña'];
$marca_modeloAuto = $_POST['marca_modelo'];

$horarios = [];
/*count() cuenta los elementos del array*/
for($ndia = 0; $ndia < count($DIAS); $ndia++){
	/*array_push() es una funcion que le añade al final del array el elemento que le des*/
	array_push($horarios, $_POST["horarios$DIAS[ndia]" ?? []); /*Los signos de preguntan funcionan como un if, si el usuario puso una opción recibe el dato, si lo dejó vacio, para que no de un error, lo deja vacío.*/
}

echo "<p class='dato'><b class='etiqueta'>Nombre:</b> $nombrePersona</p>";
echo "<p class='dato'><b class='etiqueta'>Apellido:</b> $apellidoPersona</p>";
echo "<p class='dato'><b class='etiqueta'>Correo electrónico:</b> $email</p>";
echo "<p class='dato'><b class='etiqueta'>Contraseña (prueba):</b> $contraseñaUsuario</p>";
echo "<p class='dato'><b class='etiqueta'>Marca y modelo del auto:</b> $marca_modeloAuto</p>";

for($ndia = 0; $ndia < count($horarios); $ndia++){
	$horariosDia = $horarios[$ndia];
	echo $DIAS[$ndia] . "</br>";
	for($nhora = 0; $nhora < count($horariosDia); $nhora++){
		echo $HORAS[$nhora] . "</br>";
	}
	echo "</br>";
}

?>
<a href="index.html" class="btn">Volver</a>
<a href="../inicio/index.html" class="btn">Completar registro</a>


</div>
</center>
</body>
</html>

