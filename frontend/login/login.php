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


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {
            min-height: 100vh;

            font-family:
                "Segoe UI",
                Arial,
                sans-serif;

            background:
                url("../imagenes/imagen2.jpg")
                center center / cover
                no-repeat fixed;

            display: flex;
            align-items: center;
            justify-content: center;
        }


        body::before {
            content: "";

            position: fixed;
            inset: 0;

            background:
                linear-gradient(
                    135deg,
                    rgba(0,180,255,0.15),
                    rgba(0,255,170,0.08)
                );

            backdrop-filter: blur(2px);

            z-index: -1;
        }


        /* CONTENEDOR */

        .contenedor {
            width: 100%;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 15px;
        }


        /* TARJETA */

        .card-urbanaut {

            width: 430px;
            max-width: 100%;

            padding: 30px 35px;

            border-radius: 30px;

            background:
                linear-gradient(
                    135deg,
                    rgba(255,255,255,0.74),
                    rgba(230,250,255,0.48)
                );

            border:
                1px solid
                rgba(255,255,255,0.85);

            box-shadow:
                0 25px 70px
                rgba(0,40,70,0.30),

                inset 0 1px 0
                rgba(255,255,255,0.8);

            backdrop-filter:
                blur(22px);
        }


        /* LOGO */

        .logo {
            text-align: center;

            margin-bottom: 8px;
        }


        .logo img {

            width: 135px;
            max-height: 60px;

            object-fit: contain;
        }


        /* TÍTULO */

        h1 {

            text-align: center;

            color: #123d69;

            font-size: 30px;

            font-weight: 750;

            margin-bottom: 5px;
        }


        .subtitulo {

            text-align: center;

            color: #58738e;

            font-size: 13px;

            margin-bottom: 22px;
        }


        /* FORMULARIO */

        .campo {
            margin-bottom: 15px;
        }


        .campo label {

            display: block;

            color: #123d69;

            font-size: 13px;

            font-weight: 700;

            margin-bottom: 6px;
        }


        .input-contenedor {

            position: relative;
        }


        .input-contenedor i {

            position: absolute;

            left: 13px;
            top: 50%;

            transform:
                translateY(-50%);

            color: #238bff;

            font-size: 17px;
        }


        .input-contenedor input {

            width: 100%;

            height: 45px;

            padding:
                0 13px 0 40px;

            border:
                1px solid
                rgba(255,255,255,0.9);

            border-radius: 13px;

            background:
                rgba(255,255,255,0.68);

            color: #123d69;

            font-size: 13px;

            outline: none;

            box-shadow:
                0 4px 12px
                rgba(30,90,120,0.06);

            transition: 0.2s;
        }


        .input-contenedor input:focus {

            background: white;

            border-color: #238bff;

            box-shadow:
                0 0 0 3px
                rgba(35,139,255,0.12);
        }


        .input-contenedor input::placeholder {
            color: #8ba1b2;
        }


        /* BOTÓN INICIAR SESIÓN */

        .btn-login {

            width: 100%;

            height: 45px;

            border: none;

            border-radius: 13px;

            background: #238bff;

            color: white;

            font-size: 15px;

            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 7px 18px
                rgba(35,139,255,0.25);

            transition: 0.25s;
        }


        .btn-login:hover {

            background: #126dcc;

            transform:
                translateY(-2px);

            box-shadow:
                0 10px 23px
                rgba(35,139,255,0.30);
        }


        /* RECUPERAR CUENTA */

        .recuperar {

            display: block;

            text-align: center;

            margin-top: 12px;

            color: #238bff;

            font-size: 11px;

            text-decoration: none;

            font-weight: 600;

            transition: 0.2s;
        }


        .recuperar:hover {

            color: #126dcc;

            text-decoration: underline;
        }


        /* ADMINISTRADOR */

        .admin-seccion {

            text-align: center;

            margin-top: 17px;

            padding-top: 12px;

            border-top:
                1px solid
                rgba(18,61,105,0.10);
        }


        .admin-btn {

            border: none;

            background: transparent;

            color: #58738e;

            font-size: 10px;

            font-weight: 600;

            cursor: pointer;

            text-decoration: underline;

            transition: 0.2s;
        }


        .admin-btn:hover {

            color: #238bff;
        }


        /* VOLVER */

        .volver {

            display: block;

            text-align: center;

            margin-top: 15px;

            color: #58738e;

            font-size: 11px;

            text-decoration: none;

            transition: 0.2s;
        }


        .volver:hover {
            color: #238bff;
        }


        /* CELULAR */

        @media (max-width: 500px) {

            .contenedor {
                padding: 10px;
            }


            .card-urbanaut {

                width: 100%;

                padding:
                    25px 22px;

                border-radius:
                    24px;
            }


            h1 {
                font-size: 27px;
            }


            .logo img {
                width: 120px;
            }

        }

    </style>

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
                    onclick="window.location.href='../adminitrador/ingreso.html'">

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
