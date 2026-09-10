<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Urbanaut | Recuperar cuenta</title>

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
            font-family: "Segoe UI", Arial, sans-serif;

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

            padding: 32px 35px;

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

            backdrop-filter: blur(22px);
        }

        /* LOGO */

        .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo img {
            width: 135px;
            max-height: 60px;
            object-fit: contain;
        }

        /* ICONO */

        .icono-recuperar {
            width: 60px;
            height: 60px;

            margin: 5px auto 12px;

            border-radius: 18px;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                rgba(35,139,255,0.12);

            color: #238bff;

            font-size: 28px;
        }

        /* TÍTULO */

        h1 {
            text-align: center;

            color: #123d69;

            font-size: 28px;
            font-weight: 750;

            margin-bottom: 7px;
        }

        .subtitulo {
            text-align: center;

            color: #58738e;

            font-size: 13px;
            line-height: 1.5;

            margin-bottom: 22px;
        }

        /* CAMPO */

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

            transform: translateY(-50%);

            color: #238bff;

            font-size: 17px;
        }

        .input-contenedor input {
            width: 100%;
            height: 45px;

            padding: 0 13px 0 40px;

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

        /* BOTÓN RECUPERAR */

        .btn-recuperar {
            width: 100%;
            height: 45px;

            border: none;
            border-radius: 13px;

            background: #238bff;

            color: white;

            font-size: 14px;
            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 7px 18px
                rgba(35,139,255,0.25);

            transition: 0.25s;
        }

        .btn-recuperar:hover {
            background: #126dcc;

            transform: translateY(-2px);

            box-shadow:
                0 10px 23px
                rgba(35,139,255,0.30);
        }

        /* EQUIPO */

        .admin-seccion {
            margin-top: 18px;

            padding-top: 16px;

            border-top:
                1px solid
                rgba(18,61,105,0.10);

            text-align: center;
        }

        .admin-texto {
            color: #58738e;

            font-size: 11px;

            margin-bottom: 8px;
        }

        .btn-admin {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 6px;

            border: none;

            background: transparent;

            color: #238bff;

            font-size: 11px;
            font-weight: 700;

            cursor: pointer;

            text-decoration: underline;

            transition: 0.2s;
        }

        .btn-admin:hover {
            color: #126dcc;
        }

        /* VOLVER */

        .volver {
            display: block;

            text-align: center;

            margin-top: 17px;

            color: #58738e;

            font-size: 11px;

            text-decoration: none;

            transition: 0.2s;
        }

        .volver:hover {
            color: #238bff;
        }

        /* POP UP */

        .modal-contacto {
            display: none;

            position: fixed;
            inset: 0;

            background:
                rgba(0, 30, 50, 0.45);

            backdrop-filter: blur(5px);

            align-items: center;
            justify-content: center;

            z-index: 9999;

            padding: 20px;
        }

        .modal-contacto.activo {
            display: flex;
        }

        .modal-contenido {
            width: 380px;
            max-width: 100%;

            padding: 30px;

            border-radius: 25px;

            background:
                linear-gradient(
                    135deg,
                    rgba(255,255,255,0.95),
                    rgba(230,250,255,0.90)
                );

            border:
                1px solid
                rgba(255,255,255,0.9);

            box-shadow:
                0 25px 70px
                rgba(0,40,70,0.35);

            text-align: center;

            animation:
                aparecer 0.25s ease;
        }

        @keyframes aparecer {

            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }

        }

        .modal-icono {
            width: 60px;
            height: 60px;

            margin: 0 auto 15px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 18px;

            background:
                rgba(35,139,255,0.12);

            color: #238bff;

            font-size: 28px;
        }

        .modal-contenido h2 {
            color: #123d69;

            font-size: 22px;

            margin-bottom: 10px;
        }

        .modal-contenido p {
            color: #58738e;

            font-size: 13px;

            line-height: 1.5;

            margin-bottom: 15px;
        }

        .correo {
            display: inline-block;

            padding: 12px 16px;

            border-radius: 12px;

            background:
                rgba(35,139,255,0.10);

            color: #123d69;

            font-weight: 700;

            font-size: 14px;

            margin-bottom: 20px;
        }

        .btn-cerrar {
            border: none;

            padding: 10px 20px;

            border-radius: 12px;

            background: #238bff;

            color: white;

            font-weight: 700;

            cursor: pointer;

            transition: 0.2s;
        }

        .btn-cerrar:hover {
            background: #126dcc;
        }

        /* CELULAR */

        @media (max-width: 500px) {

            .contenedor {
                padding: 10px;
            }

            .card-urbanaut {
                width: 100%;

                padding: 25px 22px;

                border-radius: 24px;
            }

            h1 {
                font-size: 25px;
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


            <!-- ICONO -->

            <div class="icono-recuperar">

                <i class="bi bi-key-fill"></i>

            </div>


            <!-- TÍTULO -->

            <h1>
                Recuperar cuenta
            </h1>


            <p class="subtitulo">

                Ingresá el mail con el que
                creaste tu cuenta y te ayudaremos
                a recuperar el acceso.

            </p>


            <!-- FORMULARIO -->

            <form onsubmit="recuperarCuenta(event)">

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


                <!-- BOTÓN -->

                <button
                    type="submit"
                    class="btn-recuperar">

                    Recuperar contraseña

                </button>

            </form>


            <!-- EQUIPO -->

            <div class="admin-seccion">

                <p class="admin-texto">

                    ¿No recordás el correo con el que
                    creaste la cuenta?

                </p>


                <button
                    type="button"
                    class="btn-admin"
                    onclick="abrirContacto()">

                    <i class="bi bi-people-fill"></i>

                    Si no se acuerda, contáctese con el equipo

                </button>

            </div>


            <!-- VOLVER -->

            <a
                href="index.html"
                class="volver">

                <i class="bi bi-arrow-left"></i>

                Volver al inicio de sesión

            </a>

        </section>

    </main>


    <!-- POP UP CONTACTO -->

    <div
        id="modalContacto"
        class="modal-contacto">

        <div class="modal-contenido">

            <div class="modal-icono">

                <i class="bi bi-envelope-heart-fill"></i>

            </div>


            <h2>
                Contactate con el equipo
            </h2>


            <p>

                Si no recordás el correo con el que
                creaste tu cuenta, podés comunicarte
                con el equipo de Craft Solutions.

            </p>


            <div class="correo">

                craft.solutions@gmail.com

            </div>


            <br>


            <button
                class="btn-cerrar"
                onclick="cerrarContacto()">

                Cerrar

            </button>

        </div>

    </div>


    <script>

        /* RECUPERAR CUENTA */

        function recuperarCuenta(event) {

            event.preventDefault();

            const email =
                document
                .getElementById("email")
                .value
                .trim();


            if (email === "") {

                alert(
                    "Por favor, ingresá el correo electrónico con el que creaste tu cuenta."
                );

                return;
            }


            const formatoEmail =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


            if (!formatoEmail.test(email)) {

                alert(
                    "Ingresá un correo electrónico válido."
                );

                return;
            }


            alert(
                "Si el correo está registrado, recibirás las instrucciones para recuperar tu cuenta."
            );

        }


        /* ABRIR POP UP */

        function abrirContacto() {

            document
                .getElementById("modalContacto")
                .classList.add("activo");

        }


        /* CERRAR POP UP */

        function cerrarContacto() {

            document
                .getElementById("modalContacto")
                .classList.remove("activo");

        }


        /* CERRAR AL HACER CLICK AFUERA */

        document
            .getElementById("modalContacto")
            .addEventListener(
                "click",
                function(event) {

                    if (
                        event.target === this
                    ) {

                        cerrarContacto();

                    }

                }
            );

    </script>

</body>

</html>