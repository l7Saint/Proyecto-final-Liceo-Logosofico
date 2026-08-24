<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Urbanaut | Datos del Auto</title>

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
            overflow-x: hidden;
            overflow-y: auto;
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
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;

            background:
                linear-gradient(
                    135deg,
                    rgba(0,180,255,.15),
                    rgba(0,255,170,.08)
                );

            backdrop-filter: blur(2px);
            z-index: -1;
        }

        .contenedor {
            width: min(95%, 900px);
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 15px;
        }

        .card-urbanaut {
            width: 560px;
            max-width: 100%;

            padding: 20px 50px 25px;

            border-radius: 30px;

            background:
                linear-gradient(
                    135deg,
                    rgba(255,255,255,.72),
                    rgba(230,250,255,.45)
                );

            border:
                1px solid
                rgba(255,255,255,.8);

            box-shadow:
                0 25px 70px
                rgba(0,40,70,.30),

                inset 0 1px 0
                rgba(255,255,255,.8);

            backdrop-filter:
                blur(22px);

            -webkit-backdrop-filter:
                blur(22px);

            animation:
                aparecer .8s ease;
        }

        @keyframes aparecer {

            from {
                opacity: 0;

                transform:
                    translateY(25px)
                    scale(.97);
            }

            to {
                opacity: 1;

                transform:
                    translateY(0)
                    scale(1);
            }

        }

        .logo {

            display: flex;
            justify-content: center;
            align-items: center;

            margin-bottom: 5px;
        }

        .logo img {

            width: 150px;
            max-height: 65px;

            object-fit: contain;
        }

        .titulo {

            display: flex;
            align-items: center;
            justify-content: center;

            gap: 8px;
        }

        .titulo i {

            color: #238bff;
            font-size: 30px;
        }

        h1 {

            text-align: center;

            color: #123d69;

            font-size: 36px;
            font-weight: 750;

            margin-bottom: 3px;
        }

        .subtitulo {

            text-align: center;

            color: #58738e;

            font-size: 15px;

            margin-bottom: 16px;
        }

        /* SELECTOR DE CANTIDAD */

        .selector-autos {

            display: flex;

            gap: 12px;

            margin-bottom: 18px;
        }

        .opcion-auto {

            flex: 1;

            height: 105px;

            border:
                1px solid
                rgba(60,120,170,.15);

            border-radius: 18px;

            background:
                rgba(255,255,255,.55);

            color: #246294;

            font-family: inherit;

            cursor: pointer;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            gap: 5px;

            transition:
                all .25s ease;

            box-shadow:
                0 5px 15px
                rgba(30,90,120,.05);
        }

        .opcion-auto i {

            font-size: 32px;

            color: #238bff;
        }

        .opcion-auto strong {

            font-size: 16px;
        }

        .opcion-auto span {

            font-size: 12px;

            color: #607991;
        }

        .opcion-auto:hover {

            transform:
                translateY(-3px);

            background:
                rgba(255,255,255,.85);

            border-color:
                #3b9cff;

            box-shadow:
                0 10px 22px
                rgba(30,145,220,.15);
        }

        .opcion-auto.activa {

            background:
                linear-gradient(
                    135deg,
                    rgba(35,139,255,.14),
                    rgba(99,216,95,.14)
                );

            border-color:
                #238bff;

            box-shadow:
                0 8px 22px
                rgba(30,145,220,.15);
        }

        /* FORMULARIOS */

        #datosAutos {

            display: none;

            animation:
                aparecer .5s ease;
        }

        .auto {

            margin-bottom: 18px;

            padding-bottom: 8px;

            border-bottom:
                1px solid
                rgba(70,120,150,.12);
        }

        .auto-titulo {

            display: flex;

            align-items: center;

            gap: 8px;

            color: #246294;

            font-size: 16px;

            font-weight: 700;

            margin-bottom: 10px;
        }

        .auto-titulo i {

            color: #238bff;

            font-size: 19px;
        }

        /* CAMPOS */

        .campo {

            position: relative;

            margin-bottom: 10px;
        }

        .campo > i {

            position: absolute;

            left: 18px;

            top: 50%;

            transform:
                translateY(-50%);

            color: #2772b8;

            font-size: 19px;

            z-index: 2;

            pointer-events: none;
        }

        .campo input,
        .campo select {

            width: 100%;

            height: 52px;

            border-radius: 15px;

            border:
                1px solid
                rgba(60,120,170,.15);

            background:
                rgba(255,255,255,.72);

            padding:
                0 18px 0 52px;

            font-size: 16px;

            color: #163f62;

            outline: none;

            transition:
                all .25s ease;
        }

        .campo input::placeholder {

            color: #7790a8;
        }

        .campo input:focus,
        .campo select:focus {

            background:
                white;

            border-color:
                #3b9cff;

            box-shadow:
                0 0 0 4px
                rgba(59,156,255,.12);

            transform:
                translateY(-1px);
        }

        .campo select {

            cursor: pointer;
        }

        /* BOTÓN */

        .btn-continuar {

            width: 100%;

            height: 54px;

            border: none;

            border-radius: 16px;

            margin-top: 5px;

            background:
                linear-gradient(
                    100deg,
                    #238bff,
                    #19b7c9,
                    #63d85f
                );

            background-size:
                200% auto;

            color: white;

            font-size: 17px;

            font-weight: 700;

            cursor: pointer;

            transition:
                .25s ease;

            box-shadow:
                0 10px 25px
                rgba(30,145,220,.25);
        }

        .btn-continuar:hover {

            transform:
                translateY(-2px);

            background-position:
                right center;

            box-shadow:
                0 15px 30px
                rgba(30,145,220,.35);
        }

        .flecha {

            margin-left: 8px;

            font-size: 21px;
        }

        .info {

            text-align: center;

            color: #607991;

            font-size: 12px;

            margin-top: 9px;
        }

        .info i {

            color: #238bff;

            margin-right: 4px;
        }

        /* CELULAR */

        @media (max-width: 650px) {

            body {

                overflow-y:
                    auto;
            }

            .card-urbanaut {

                width: 94%;

                padding:
                    25px;

                border-radius:
                    24px;
            }

            h1 {

                font-size:
                    32px;
            }
        }

        @media (max-width: 430px) {

            .card-urbanaut {

                padding:
                    25px 18px 22px;
            }

            h1 {

                font-size:
                    29px;
            }

            .selector-autos {

                gap:
                    8px;
            }

            .opcion-auto {

                height:
                    95px;
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

    <div class="titulo">

        <i class="bi bi-car-front-fill"></i>

        <h1>
            Tus Autos
        </h1>

    </div>


    <p class="subtitulo">

        ¿Cuántos vehículos querés ingresar?

    </p>


    <!-- ELEGIR CANTIDAD -->

    <div class="selector-autos">

        <!-- 1 AUTO -->

        <button
            type="button"
            class="opcion-auto"
            id="unAuto"
            onclick="seleccionarAutos(1)">

            <i class="bi bi-car-front-fill"></i>

            <strong>
                1 auto
            </strong>

            <span>
                Ingresar un vehículo
            </span>

        </button>


        <!-- 2 AUTOS -->

        <button
            type="button"
            class="opcion-auto"
            id="dosAutos"
            onclick="seleccionarAutos(2)">

            <i class="bi bi-car-front-fill"></i>

            <strong>
                2 autos
            </strong>

            <span>
                Ingresar dos vehículos
            </span>

        </button>

    </div>


    <!-- FORMULARIO -->

    <form id="formularioAuto">

        <div id="datosAutos">

            <!-- AUTO 1 -->

            <div class="auto">

                <div class="auto-titulo">

                    <i class="bi bi-1-circle-fill"></i>

                    Auto 1

                </div>


                <div class="campo">

                    <i class="bi bi-car-front"></i>

                    <input
                        type="text"
                        id="marca1"
                        name="marca1"
                        placeholder="Marca"
                        required>

                </div>


                <div class="campo">

                    <i class="bi bi-car-front"></i>

                    <input
                        type="text"
                        id="modelo1"
                        name="modelo1"
                        placeholder="Modelo"
                        required>

                </div>


                <div class="campo">

                    <i class="bi bi-palette"></i>

                    <select
                        id="color1"
                        name="color1"
                        required>

                        <option
                            value=""
                            selected
                            disabled>

                            Seleccioná el color

                        </option>

                        <option value="Negro">Negro</option>
                        <option value="Blanco">Blanco</option>
                        <option value="Gris">Gris</option>
                        <option value="Azul">Azul</option>
                        <option value="Rojo">Rojo</option>
                        <option value="Verde">Verde</option>
                        <option value="Amarillo">Amarillo</option>
                        <option value="Naranja">Naranja</option>
                        <option value="Otro">Otro</option>

                    </select>

                </div>

            </div>


            <!-- AUTO 2 -->

            <div
                class="auto"
                id="auto2"
                style="display: none;">

                <div class="auto-titulo">

                    <i class="bi bi-2-circle-fill"></i>

                    Auto 2

                </div>


                <div class="campo">

                    <i class="bi bi-car-front"></i>

                    <input
                        type="text"
                        id="marca2"
                        name="marca2"
                        placeholder="Marca">

                </div>


                <div class="campo">

                    <i class="bi bi-car-front"></i>

                    <input
                        type="text"
                        id="modelo2"
                        name="modelo2"
                        placeholder="Modelo">

                </div>


                <div class="campo">

                    <i class="bi bi-palette"></i>

                    <select
                        id="color2"
                        name="color2">

                        <option
                            value=""
                            selected
                            disabled>

                            Seleccioná el color

                        </option>

                        <option value="Negro">Negro</option>
                        <option value="Blanco">Blanco</option>
                        <option value="Gris">Gris</option>
                        <option value="Azul">Azul</option>
                        <option value="Rojo">Rojo</option>
                        <option value="Verde">Verde</option>
                        <option value="Amarillo">Amarillo</option>
                        <option value="Naranja">Naranja</option>
                        <option value="Otro">Otro</option>

                    </select>

                </div>

            </div>


            <!-- BOTÓN -->

            <button
                type="submit"
                class="btn-continuar">

                Crear Cuenta

                <span class="flecha">
                    ✓
                </span>

            </button>

        </div>

    </form>


    <div class="info">

        <i class="bi bi-shield-check"></i>

        Podés registrar hasta dos vehículos.

    </div>

</section>

</main>


<script>

let cantidadAutos = 0;


function seleccionarAutos(cantidad) {

    cantidadAutos = cantidad;

    const datosAutos =
        document.getElementById("datosAutos");

    const auto2 =
        document.getElementById("auto2");

    const unAuto =
        document.getElementById("unAuto");

    const dosAutos =
        document.getElementById("dosAutos");


    /* MOSTRAR FORMULARIO */

    datosAutos.style.display =
        "block";


    /* QUITAR SELECCIÓN ANTERIOR */

    unAuto.classList.remove(
        "activa"
    );

    dosAutos.classList.remove(
        "activa"
    );


    /* SI ELIGE 1 AUTO */

    if (cantidad === 1) {

        unAuto.classList.add(
            "activa"
        );

        auto2.style.display =
            "none";

        document
            .getElementById("marca2")
            .required = false;

        document
            .getElementById("modelo2")
            .required = false;

        document
            .getElementById("color2")
            .required = false;

    }


    /* SI ELIGE 2 AUTOS */

    else {

        dosAutos.classList.add(
            "activa"
        );

        auto2.style.display =
            "block";

        document
            .getElementById("marca2")
            .required = true;

        document
            .getElementById("modelo2")
            .required = true;

        document
            .getElementById("color2")
            .required = true;

    }


    /* BAJAR SUAVEMENTE AL FORMULARIO */

    setTimeout(function () {

        datosAutos.scrollIntoView({
            behavior: "smooth",
            block: "nearest"
        });

    }, 100);

}


/* CUANDO TOCA CREAR CUENTA */

document
    .getElementById("formularioAuto")
    .addEventListener(
        "submit",
        function(event) {

            event.preventDefault();


            if (cantidadAutos === 0) {

                alert(
                    "Seleccioná primero cuántos autos querés ingresar."
                );

                return;
            }


            /* =====================================
               IR A CARGA.HTML

               carga.html está AFUERA de la carpeta
               donde está este archivo.
            ===================================== */

            window.location.href =
                "../carga.html";

        }
    );

</script>

</body>

</html>