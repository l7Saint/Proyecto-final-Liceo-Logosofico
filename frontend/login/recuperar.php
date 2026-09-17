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
