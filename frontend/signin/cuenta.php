<?php
$backend = rtrim(getenv('BACKEND_HOST') ?: '', '/');
$port    = getenv('BACKEND_PORT') ?: '';
$url     = 'http://' . $backend . ':' . $port;
?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Urbanaut | Crear Cuenta</title>
		<!-- Bootstrap -->
		<link
			href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
			rel="stylesheet"
		/>
		<!-- Bootstrap Icons -->
		<link
			rel="stylesheet"
			href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
		/>
		<link
			rel="stylesheet"
			href="/assets/css/style.css"
		/>
		<link
			rel="stylesheet"
			href="/assets/css/cuenta.css"
		/>
	</head>
	<body>
		<main class="contenedor">
			<section class="card-urbanaut">
				<!-- LOGO -->
				<div class="logo">
					<img src="../imagenes/logoNuevo2.png" alt="Logo Urbanaut" />
				</div>
				<!-- TÍTULO -->
				<h1>Crear Cuenta</h1>
				<p class="subtitulo">
					Poné tus datos y empezá a encontrar tu lugar ideal.
				</p>
				<!-- FORMULARIO -->
				<form id="formulario">
					<!-- NOMBRE -->
					<div class="campo">
						<i class="bi bi-person"></i>
						<input
							type="text"
							id="nombre"
							name="nombre"
							placeholder="Nombre"
							required
						/>
					</div>
					<!-- APELLIDO -->
					<div class="campo">
						<i class="bi bi-person"></i>
						<input
							type="text"
							id="apellido"
							name="apellido"
							placeholder="Apellido"
							required
						/>
					</div>
					<!-- CORREO -->
					<div class="campo">
						<i class="bi bi-envelope"></i>
						<input
							type="email"
							id="email"
							name="email"
							placeholder="Correo electrónico"
							required
						/>
					</div>
					<!-- CONTRASEÑA -->
					<div class="campo">
						<i class="bi bi-lock"></i>
						<input
							type="password"
							id="contrasena"
							name="contrasena"
							placeholder="Contraseña"
							required
						/>
						<button
							type="button"
							class="mostrar-password"
							onclick="mostrarPassword('contrasena', this)"
						>
							<i class="bi bi-eye"></i>
						</button>
					</div>
					<!-- CONFIRMAR CONTRASEÑA -->
					<div class="campo">
						<i class="bi bi-lock"></i>
						<input
							type="password"
							id="confirmar"
							name="confirmar"
							placeholder="Confirmar contraseña"
							required
						/>
						<button
							type="button"
							class="mostrar-password"
							onclick="mostrarPassword('confirmar', this)"
						>
							<i class="bi bi-eye"></i>
						</button>
					</div>
					<!-- CONTINUAR -->
					<button
						type="submit"
						class="btn-continuar"
					>
						Continuar
						<span class="flecha"> → </span>
					</button>
				</form>
				<!-- INICIAR SESIÓN -->
				<div class="login">
					¿Ya tenés una cuenta?
					<a href="../login/login.php"> Iniciá sesión </a>
				</div>
				<!-- BENEFICIOS -->
				<div class="beneficios">
					<div class="beneficio">
						<div class="beneficio-icon">
							<i class="bi bi-shield-check"></i>
						</div>
						<div>
							<strong> Seguro </strong>
							Tus datos protegidos
						</div>
					</div>
					<div class="beneficio">
						<div class="beneficio-icon">
							<i class="bi bi-geo-alt"></i>
						</div>
						<div>
							<strong> Confiable </strong>
							Estacionamientos cerca
						</div>
					</div>
					<div class="beneficio">
						<div class="beneficio-icon">
							<i class="bi bi-clock"></i>
						</div>
						<div>
							<strong> Rápido </strong>
							Encontrá tu lugar ideal
						</div>
					</div>
				</div>
			</section>
		</main>
		<script type="module">
			import UrbanautClient, {
				HttpError,
				NetworkError,
				ValidationError
			} from '/assets/js/urbanaut-js-client/src/urbanaut.js';

			const api = new UrbanautClient({
				baseUrl: <?= json_encode($url . '/api') ?>
	<?php error_log("baseUrl: $url"); ?>
			});

			function mostrarPassword(id, boton) {
				const input = document.getElementById(id);
				const icono = boton.querySelector('i');
				if (input.type === 'password') {
					input.type = 'text';
					icono.classList.remove('bi-eye');
					icono.classList.add('bi-eye-slash');
				} else {
					input.type = 'password';
					icono.classList.remove('bi-eye-slash');
					icono.classList.add('bi-eye');
				}
			}
			window.mostrarPassword = mostrarPassword;

			document.getElementById('formulario').addEventListener('submit', async (event) => {
				event.preventDefault();
				const contrasena = document.getElementById('contrasena').value;
				const confirmar = document.getElementById('confirmar').value;
				if (contrasena !== confirmar) {
					alert('Las contraseñas no coinciden.');
					return;
				}
				await enviarDatos(obtenerDatos());
			});

			function obtenerDatos() {
				const nombre = document.getElementById('nombre').value.trim();
				const apellido = document.getElementById('apellido').value.trim();
				const email = document.getElementById('email').value.trim();
				const contrasena = document.getElementById('contrasena').value;
				return { nombre, apellido, email, contrasena };
			}

			async function enviarDatos(datos) {
				try {
					// `autoLogin` is required: the backend creates the session during signin
					// but discards the token before responding. The wrapper logs in right
					// afterwards so the token + profile are stored in localStorage.
					const { success, session } = await api.usuario.signin(datos, { autoLogin: true });

					if (!success) {
						alert('No se pudo crear la cuenta. Intentá de nuevo.');
						return;
					}

					alert('Usuario registrado correctamente.');
					document.getElementById('formulario').reset();

					if (session?.token) {
						window.location.href = './horarios.php';
					} else {
						// Registration worked but auto-login did not — send the user to login.
						window.location.href = '../login/login.php';
					}
				} catch (error) {
					if (error instanceof ValidationError) {
						alert(`Faltan datos: ${error.details?.missing?.join(', ') ?? ''}`);
						console.error(error);
					} else if (error instanceof HttpError) {
						if (error.status === 409) {
							alert('Ese correo ya está registrado.');
						} else if (error.status === 400) {
							alert('Faltan datos o hay datos incorrectos.');
						} else if (error.status === 500) {
							alert('Error interno del servidor.');
						} else {
							alert(`Ocurrió un error inesperado (HTTP ${error.status}).`);
						}
						console.error(error);
					} else if (error instanceof NetworkError) {
						alert('No se pudo conectar con el servidor.');
						console.error(error);
					} else {
						alert('Ocurrió un error inesperado.');
						console.error(error);
					}
				}
			}
		</script>
	</body>
</html>
