<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Urbanaut | Recuperar Contraseña</title>

	<!-- Bootstrap -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Bootstrap Icons -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

	<link
		rel="stylesheet"
		href="/assets/css/style.css">


	<link
		rel="stylesheet"
		href="/assets/css/recuperar.css">

</head>

<body>

	<main class="contenedor">

		<section class="card-urbanaut">

			<!-- LOGO -->

			<div class="logo">
				<img
					src="../imagenes/logoNuevo2.png"
					alt="Logo Urbanaut">
			</div>

			<!-- TÍTULO -->

			<h1>Recuperar Contraseña</h1>

			<p class="subtitulo">
				Estamos mejorando esta sección para ofrecerte
				una mejor experiencia.
			</p>

			<!-- AVISO -->

			<div class="aviso">
				<strong>Atención:</strong>
				Estamos trabajando para mejorar tu experiencia.
				Por ahora esta sección no está disponible.
				Podes contactanos para resolver el problema.
			</div>

			<!-- CONTACTO CON EL EQUIPO -->

			<div class="admin-seccion">

				<button
					type="button"
					class="btn-admin"
					onclick="abrirContacto()">

					<i class="bi bi-people-fill"></i>
					Hablar con el equipo

				</button>

			</div>

			<!-- VOLVER AL INICIO DE SESIÓN -->

			<a href="../login/login.php" class="volver">
				<i class="bi bi-arrow-left"></i>
				Volver al inicio de sesión
			</a>

		</section>

	</main>

	<!-- POPUP DE CONTACTO -->

	<div
		id="modalContacto"
		class="modal-contacto"
		role="dialog"
		aria-modal="true"
		aria-labelledby="tituloContacto">

		<div class="modal-contenido">

			<div class="modal-icono">
				<i class="bi bi-envelope-heart-fill"></i>
			</div>

			<h2 id="tituloContacto">
				Hablá con el equipo
			</h2>

			<p>
				Si necesitás ayuda o tenés alguna consulta,
				podés comunicarte con el equipo de
				Craft Solutions. Estaremos encantados de ayudarte.
			</p>

			<div class="correo">
				craft.solutions@gmail.com
			</div>

			<br>

			<button
				type="button"
				class="btn-cerrar"
				onclick="cerrarContacto()">

				Cerrar

			</button>

		</div>

	</div>

	<script>
		/* ABRIR POPUP DE CONTACTO */

		function abrirContacto() {
			document
				.getElementById("modalContacto")
				.classList.add("activo");
		}

		/* CERRAR POPUP */

		function cerrarContacto() {
			document
				.getElementById("modalContacto")
				.classList.remove("activo");
		}

		/* CERRAR AL HACER CLICK AFUERA */

		document
			.getElementById("modalContacto")
			.addEventListener("click", function(event) {

				if (event.target === this) {
					cerrarContacto();
				}

			});

		/* CERRAR CON LA TECLA ESC */

		document.addEventListener("keydown", function(event) {

			if (event.key === "Escape") {
				cerrarContacto();
			}

		});
	</script>

</body>

</html>
