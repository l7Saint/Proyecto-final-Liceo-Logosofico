<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos</title>
    <style>
        :root { 
	/*colores*/
	--color-fondo: #88B6C2;
	--color-navbar: #32545C;
	--color-boton-navbar: #1B3237;
	--color-titulos: #32545c;
	--color-subtitulos: #497C88;
	--color-texto: #21434D;

	--padding-navbar: 1rem;
}

body{
	background-color:var(--color-fondo);
	padding-bottom: 100px;
	font-size: 19px;
	font-family: 'Open Sans', sans-serif;
}

#barraNavbar {
	width: 100%;
	background-color: var(--color-navbar);
	position: fixed;
	bottom: 0;
	left: 0;
	/*fuerza a que la navbar se superponga sobre todos los elementos*/
	z-index: 1050;
}

#marcaNavbar{
	color: white;
	font-size: 30px;
	font-family:Verdana, Geneva, Tahoma, sans-serif;
	font-weight: bold;
	/*aca uso la variable, si despues le pongo "--padding-navbar: 3rem;"
	  se va a ver afectado en todos los "var(--padding-navbar);"*/
	margin-left: var(--padding-navbar);
	margin-right: var(--padding-navbar);
}

#botonesNavbar{
	margin-left: var(--padding-navbar);
	margin-right: var(--padding-navbar);
	gap: 0.4rem;
}

/*aca sobreescribo 2 atributos de una de las clases de bootstrap*/
.btn {
	background-color: var(--color-boton-navbar); 
	color: white;
}

#botonHamburguesa {
	margin-left: var(--padding-navbar);
	margin-right: var(--padding-navbar);
}

.titulos{
	/*font-family:Verdana, Geneva, Tahoma, sans-serif;*/
	font-weight: bold;
	color: var(--color-titulos);
}

.subTitulos{
	/*font-family:Verdana, Geneva, Tahoma, sans-serif;*/
	color : var(--color-subtitulos);
}

.texto{
	/*font-family:Verdana, Geneva, Tahoma, sans-serif;*/
	color: var(--color-texto);
}

#contenido {
	text-align: center;
	margin-top: 0.5rem;	
}

@media (min-width: 992px) { 
	#contenido {
		text-align: left;
	} 

	/*aca cambio el padding del navbar
	  para las pantallas grandes ( > o = 992px ) */
	:root { 
		--padding-navbar: 2.4rem;
	}
}

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
$nombrePersona = $_POST['nombre'];
$apellidoPersona = $_POST['apellido'];
$email = $_POST['email'];
$contraseñaUsuario = $_POST['contraseña'];
$marca_modeloAuto = $_POST['marca_modelo'];
$marco_lunes = $_POST['lunes'] ?? "" ; /*Los signos de preguntan funcionan como un if, si el usuario puso una opción recibe el dato, 
si lo dejó vacio, para que no de un error, lo deja vacío.*/
$horario_lunes = $_POST["horariosLunes"] ?? []; 
$marco_martes = $_POST['martes']?? "";
$horario_martes = $_POST["horariosMartes"] ?? [];
$marco_miercoles = $_POST['miercoles'] ?? "";
$horario_miercoles = $_POST["horariosMiercoles"] ?? [];
$marco_jueves = $_POST['jueves']?? "";
$horario_jueves = $_POST["horariosJueves"] ?? [];
$marco_viernes = $_POST['viernes']?? "";
$horario_viernes = $_POST["horariosViernes"] ?? [];

echo "<p class='dato'><b class='etiqueta'>Nombre:</b> $nombrePersona</p>";
echo "<p class='dato'><b class='etiqueta'>Apellido:</b> $apellidoPersona</p>";
echo "<p class='dato'><b class='etiqueta'>Correo electrónico:</b> $email</p>";
echo "<p class='dato'><b class='etiqueta'>Contraseña (prueba):</b> $contraseñaUsuario</p>";
echo "<p class='dato'><b class='etiqueta'>Marca y modelo del auto:</b> $marca_modeloAuto</p>";
/*Recorro el array para imprimirlo*/
echo "<b class='etiqueta'>Horarios seleccionados: </b>" . "<br>";
echo $marco_lunes . "</br>";
for ($i=0; $i < count($horario_lunes); $i++) { /*La función count() devuelve la cantidad de elementos que hay en el array, por lo que lo va recorriendo 
dependiendo de cuántas funciones elegió el usuario, e imprime el contenido en la posición que esté ($i)*/
    echo $horario_lunes[$i] . "<br>";
}
echo $marco_martes . "</br>";
for ($i=0; $i < count($horario_martes); $i++) {
    echo $horario_martes[$i] . "<br>";
}

echo $marco_miercoles . "</br>";
for ($i=0; $i < count($horario_miercoles); $i++) {
    echo $horario_miercoles[$i] . "<br>";
}

echo $marco_jueves . "</br>";
for ($i=0; $i < count($horario_jueves); $i++) {
    echo $horario_jueves[$i] . "<br>";
}

echo $marco_viernes . "</br>";
for ($i=0; $i < count($horario_viernes); $i++) {
    echo $horario_viernes[$i] . "<br>";
}

?>
<a href="index.html" class="btn">Volver</a>
<a href="../inicio/index.html" class="btn">Completar registro</a>


</div>
</center>
</body>
</html>

