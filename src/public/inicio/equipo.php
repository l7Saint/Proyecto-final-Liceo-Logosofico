<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Document</title>
		<!-- bootstrap cdn -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
		<!-- cdn de google para la fuente de Open Sans -->
		<link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
	<style>
/*Esto es para crear variables de css,
  basicamente, desde aca puedo cambiar el padding
  general de los elementos adentro del navbar, o los
  colores*/
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
	padding-bottom: 20px;
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
    margin-top: 6rem;
    padding: 10px 17px;
}

#botonHamburguesa {
	margin-left: var(--padding-navbar);
	margin-right: var(--padding-navbar);
}

.titulos{
	/*font-family:Verdana, Geneva, Tahoma, sans-serif;*/
	font-weight: bold;
	color: var(--color-titulos);
  padding-top: 13rem;
  font-size: 5rem;
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

.cajaContacta{
	
    background: white;
    border-radius: 2rem;
    padding: 2rem;
    margin: 11rem auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.cerrar{
    color:  #1B3237;
    background: none;
    border: none;
    font-size: 3rem; 
    cursor: pointer;
}

	</style>
	<body>
		<div id="contenedorPrincipal" class="  d-flex container-fluid align-items-center">
		    <div class="cajaContacta" >
              <div id="contenido" class="container-lg">
				<a href="index.html">
                <button class="cerrar">&times;</button>
				</a>
                <br>
                <br>
                <h1 class="subTitulos">Quienes Somos?</h1>
                <p class="texto">Craft Solutions comenzo en 2026 con el objetivo de ayudar a los docentes a integrar la tecnolgia en su dia a dia. Su mas reciente creacion fue Urbanaut. Una aplicacion capaz de monitorear los alrededores de una zona con datos internos creados por su comunidad.</p>
               
			  </div>
		    </div>
        </div>
		<!--Aca esta la barrita, el navbar-->
		<div id="barraNavbar" class="justify-content-between navbar navbar-expand-lg">
			<div class="d-flex ">
				<a id="marcaNavbar" class="navbar-brand" href="#">Urbanaut</a>
			</div>
			
			<button id="botonHamburguesa" class="navbar-toggler my-auto" type="button" data-bs-toggle="collapse" data-bs-target="#botonesNavbar">
				<span class="navbar-toggler-icon"></span>
			</button>
			
		</div>


	</body>

<script>
  
</script>
    
</html>

