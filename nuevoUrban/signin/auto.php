<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
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

            overflow: hidden;

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


        /* =========================
           CAMPOS
           ========================= */

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

            transition: all .25s ease;
        }


        .campo input::placeholder {

            color: #7790a8;
        }


        .campo input:focus,
        .campo select:focus {

            background: white;

            border-color: #3b9cff;

            box-shadow:
                0 0 0 4px
                rgba(59,156,255,.12);

            transform:
                translateY(-1px);
        }


        .campo select {

            cursor: pointer;
        }


        /* =========================
           BOTÓN
           ========================= */

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

            background-size: 200% auto;

            color: white;

            font-size: 17px;

            font-weight: 700;

            cursor: pointer;

            transition: .25s ease;

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


        @media (max-width: 650px) {

            body {
                overflow-y: auto;
            }

            .card-urbanaut {

                width: 94%;

                padding:
                    25px;

                border-radius: 24px;
            }

            h1 {

                font-size: 32px;
            }

        }


        @media (max-width: 430px) {

            .card-urbanaut {

                padding:
                    25px 18px 22px;
            }

            h1 {

                font-size: 29px;
            }

        }

    </style>

</head>


<body>


<main class="contenedor">


<section class="card-urbanaut">


    <!-- LOGO -->

    <div class="logo">

        <img src="../imagenes/logoNuevo2.png" alt="Logo Urbanaut">

    </div>


    <!-- TÍTULO -->

    <div class="titulo">

        <i class="bi bi-car-front-fill"></i>

        <h1>
            Datos del Auto
        </h1>

    </div>


    <p class="subtitulo">

        Ingresá los datos de tu vehículo.

    </p>


    <!-- FORMULARIO -->

    <form
        id="formularioAuto">


        <!-- MARCA -->

        <div class="campo">

            <i class="bi bi-car-front"></i>

            <input
                type="text"
                id="marca"
                name="marca"
                placeholder="Marca"
                required>

        </div>


        <!-- MODELO -->

        <div class="campo">

            <i class="bi bi-car-front"></i>

            <input
                type="text"
                id="modelo"
                name="modelo"
                placeholder="Modelo"
                required>

        </div>


        <!-- COLOR -->

        <div class="campo">

            <i class="bi bi-palette"></i>

            <select
                id="color"
                name="color"
                required>

                <option
                    value=""
                    selected
                    disabled>

                    Seleccioná el color

                </option>

                <option value="Negro">
                    Negro
                </option>

                <option value="Blanco">
                    Blanco
                </option>

                <option value="Gris">
                    Gris
                </option>

                <option value="Azul">
                    Azul
                </option>

                <option value="Rojo">
                    Rojo
                </option>

                <option value="Verde">
                    Verde
                </option>

                <option value="Amarillo">
                    Amarillo
                </option>

                <option value="Naranja">
                    Naranja
                </option>

                <option value="Otro">
                    Otro
                </option>

            </select>

        </div>


        <!-- BOTÓN -->

        <button
            type="button"
            class="btn-continuar"
            onclick="window.location.href='../inicio/index.html'">
                Crear Cuenta
            <span class="flecha">
                ✓
            </span>
        </button>

    </form>


    <div class="info">

        <i class="bi bi-shield-check"></i>

        Completá los datos de tu vehículo.

    </div>


</section>

</main>


<script>

    document
        .getElementById("formularioAuto")
        .addEventListener(
            "submit",
            function(event) {

                event.preventDefault();

                alert(
                    "¡Datos del auto guardados correctamente!"
                );

            }
        );

</script>


</body>

</html>