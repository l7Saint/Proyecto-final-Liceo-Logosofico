<?php
$backend = rtrim(getenv('BACKEND_HOST') ?: '', '/');
$port    = getenv('BACKEND_PORT') ?: '';
$url     = 'http://' . $backend . ':' . $port;
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Urbanaut | Iniciar Sesión</title>


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


    <link
        rel="stylesheet"
        href="/assets/css/style.css">

    <link
        rel="stylesheet"
        href="/assets/css/login.css">
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

            <h1>
                Iniciar Sesión
            </h1>


            <p class="subtitulo">
                Ingresá a tu cuenta de Urbanaut.
            </p>


            <!-- FORMULARIO -->

            <form
                id="formLogin"
                >


                <!-- MAIL -->

                <div class="campo">

                    <label for="email">
                        Correo electrónico
                    </label>


                    <div class="input-contenedor">

                        <i class="bi bi-envelope-fill"></i>


                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Ingresá tu correo"
                            required>

                    </div>

                </div>


                <!-- CONTRASEÑA -->

                <div class="campo">

                    <label for="password">
                        Contraseña
                    </label>


                    <div class="input-contenedor">

                        <i class="bi bi-lock-fill"></i>


                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Ingresá tu contraseña"
                            required>

                    </div>

                </div>


                <!-- LOGIN -->

                <button
                    type="submit"
                    class="btn-login">
                    Iniciar sesión
                </button>


            </form>


            <!-- RECUPERAR CUENTA -->

            <a
                href="recuperar.php"
                class="recuperar">

                ¿Olvidaste tu contraseña?

            </a>


            <!-- ADMINISTRADOR -->

            <div class="admin-seccion">

                <button
                    type="button"
                    class="admin-btn"
                    onclick="window.location.href='../administrador/ingreso.html'">

                    Soy administrador

                </button>

            </div>


            <!-- VOLVER -->

            <a
                href="../introduccion/index.html"
                class="volver">

                <i class="bi bi-arrow-left"></i>

                Volver

            </a>


        </section>


    </main>


    <script>

	function obtenerDatos(){
            const email =
                document
                .getElementById("email")
                .value
                .trim();

            const contrasena =
                document
                .getElementById("password")
                .value
                .trim();

	    return {
		email,
		contrasena
	    };
	}

async function enviarDatos(datos){
	try {
		const response = await fetch(
			<?= "\"".$url.'/api/usuario.php/login'."\"" ?>,
			{
				method: "POST",
				credentials: "include",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify(datos)
			}
		);
		let data = await response.json();
		if (response.status === 200) {
			alert("Inicio de sesion exitoso.");
			document.getElementById("formLogin").reset();
		} else if (response.status === 400) {
			alert("Faltan datos o hay datos incorrectos.");
			console.error(data);
		} else if (response.status === 500) {
			alert("Error interno del servidor.");
			console.error(data);
		} else {
			alert("Ocurrió un error inesperado.");
			console.error(data);
		}
	} catch (error) {
              console.error(
                  "Error al conectar con la API:",
                  error
              );
              alert("No se pudo conectar con el servidor.");
          }

}

        function iniciarSesion() {
	    const datos = obtenerDatos();
            if (
                datos.email === "" ||
                datos.contrasena === ""
            ) {
                alert(
                    "Por favor, completá el correo electrónico y la contraseña."
                );
                return;
            }
            /* COMPROBAR FORMATO DEL EMAIL */
            const formatoEmail =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (
                !formatoEmail.test(datos.email)
            ) {
                alert(
                    "Ingresá un correo electrónico válido."
                );
                return;
            }
	    enviarDatos(datos);
        }


        document
          .getElementById("formLogin")
          .addEventListener("submit", function (event) {
            event.preventDefault();
	    iniciarSesion();
	  });
    </script>
</body>
</html>
