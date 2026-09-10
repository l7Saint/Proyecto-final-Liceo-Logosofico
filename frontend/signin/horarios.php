<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Urbanaut | Ingresar Horarios</title>

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

            font-family: "Segoe UI", Arial, sans-serif;

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
                    rgba(0, 180, 255, 0.15),
                    rgba(0, 255, 170, 0.08)
                );

            backdrop-filter: blur(2px);

            z-index: -1;
        }

        /* =========================
           CONTENEDOR
           ========================= */

        .contenedor {
            width: min(95%, 900px);
            height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 15px;
        }

        /* =========================
           TARJETA
           ========================= */

        .card-urbanaut {
            width: 700px;
            max-width: 100%;
            max-height: calc(100vh - 30px);

            padding: 18px 35px 20px;

            border-radius: 30px;

            background:
                linear-gradient(
                    135deg,
                    rgba(255, 255, 255, 0.72),
                    rgba(230, 250, 255, 0.45)
                );

            border:
                1px solid rgba(255, 255, 255, 0.8);

            box-shadow:
                0 25px 70px
                rgba(0, 40, 70, 0.30),

                inset 0 1px 0
                rgba(255, 255, 255, 0.8);

            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);

            position: relative;

            animation: aparecer 0.8s ease;

            overflow: hidden;
        }

        /* =========================
           ANIMACIÓN
           ========================= */

        @keyframes aparecer {

            from {
                opacity: 0;

                transform:
                    translateY(25px)
                    scale(0.97);
            }

            to {
                opacity: 1;

                transform:
                    translateY(0)
                    scale(1);
            }
        }

        /* =========================
           LOGO
           ========================= */

        .logo {
            display: flex;

            justify-content: center;
            align-items: center;

            margin-bottom: 2px;
        }

        .logo img {
            width: 145px;
            height: auto;

            max-height: 60px;

            object-fit: contain;

            display: block;
        }

        /* =========================
           TÍTULO
           ========================= */

        h1 {
            text-align: center;

            color: #123d69;

            font-size: 35px;
            font-weight: 750;

            margin-bottom: 2px;

            letter-spacing: -1.5px;
        }

        .subtitulo {
            text-align: center;

            color: #58738e;

            font-size: 15px;

            margin-bottom: 12px;
        }

        .titulo-horarios {
            display: flex;

            align-items: center;
            justify-content: center;

            gap: 8px;
        }

        .titulo-horarios i {
            color: #238bff;

            font-size: 30px;
        }

        /* =========================
           DÍAS
           ========================= */

        .dias-container {
            display: grid;

            grid-template-columns:
                repeat(5, 1fr);

            gap: 8px;

            margin-top: 8px;
        }

        /* =========================
           BOTÓN DEL DÍA
           ========================= */

        .btn-dia {
            width: 100%;

            min-height: 48px;

            border: none;

            border-radius: 14px;

            background:
                rgba(255, 255, 255, 0.72);

            color: #246294;

            font-size: 14px;
            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 5px 15px
                rgba(30, 90, 120, 0.08);

            transition:
                all 0.25s ease;
        }

        .btn-dia:hover {
            transform:
                translateY(-2px);

            background: white;

            color: #1683e9;

            box-shadow:
                0 8px 20px
                rgba(30, 120, 180, 0.15);
        }

        .btn-dia i {
            margin-right: 4px;

            color: #238bff;
        }

        /* =========================
           DÍA SELECCIONADO
           ========================= */

        .btn-dia.dia-seleccionado {
            background:
                rgba(99, 216, 95, 0.45);

            color: #23753b;

            box-shadow:
                0 5px 15px
                rgba(55, 170, 75, 0.20);
        }

        .btn-dia.dia-seleccionado i {
            color: #2e9b49;
        }

        /* =========================
           CONTENEDOR DE CADA DÍA
           ========================= */

        .contenedor-dia {
            margin-top: 8px;
        }

        /* =========================
           LISTA DE HORARIOS
           ========================= */

        .lista-horarios {
            list-style: none;

            margin: 0;
            padding: 8px;

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 6px;

            max-height: 260px;

            overflow-y: auto;

            border-radius: 15px;

            background:
                rgba(255, 255, 255, 0.35);

            border:
                1px solid
                rgba(255, 255, 255, 0.5);
        }

        /* =========================
           SCROLLBAR
           ========================= */

        .lista-horarios::-webkit-scrollbar {
            width: 6px;
        }

        .lista-horarios::-webkit-scrollbar-track {
            background:
                rgba(255,255,255,0.3);

            border-radius: 10px;
        }

        .lista-horarios::-webkit-scrollbar-thumb {
            background:
                rgba(35,139,255,0.45);

            border-radius: 10px;
        }

        /* =========================
           CADA HORARIO
           ========================= */

        .li-hora {
            display: flex;

            align-items: center;
            justify-content: space-between;

            min-height: 38px;

            padding:
                5px 10px;

            border-radius: 10px;

            background:
                rgba(255,255,255,0.68);

            transition:
                all 0.2s ease;
        }

        .li-hora:hover {
            background: white;

            transform:
                translateX(2px);
        }

        .li-hora label {
            color: #45657f;

            font-size: 13px;
            font-weight: 600;

            cursor: pointer;
        }

        .li-hora input {
            width: 18px;
            height: 18px;

            accent-color: #238bff;

            cursor: pointer;
        }

        .li-hora:has(input:checked) {
            background:
                rgba(99, 216, 95, 0.25);

            border:
                1px solid
                rgba(70, 180, 80, 0.25);
        }

        /* =========================
           EVENTO ESPECIAL
           ========================= */

        .evento-especial {
            margin-top: 12px;

            padding: 12px 15px;

            border-radius: 16px;

            background:
                linear-gradient(
                    135deg,
                    rgba(255,255,255,0.65),
                    rgba(220,245,255,0.55)
                );

            border:
                1px solid
                rgba(35,139,255,0.18);

            box-shadow:
                0 5px 15px
                rgba(30,90,120,0.07);
        }

        .evento-cabecera {
            display: flex;

            align-items: center;

            gap: 10px;

            cursor: pointer;
        }

        .evento-icono {
            width: 38px;
            height: 38px;

            flex-shrink: 0;

            border-radius: 12px;

            display: flex;

            align-items: center;
            justify-content: center;

            background:
                rgba(35,139,255,0.12);

            color: #238bff;

            font-size: 20px;
        }

        .evento-texto {
            flex: 1;
        }

        .evento-titulo {
            display: block;

            color: #123d69;

            font-size: 14px;

            font-weight: 750;

            margin-bottom: 2px;
        }

        .evento-descripcion-corta {
            color: #607991;

            font-size: 11px;
        }

        /* SWITCH */

        .switch {
            position: relative;

            width: 46px;
            height: 24px;

            flex-shrink: 0;
        }

        .switch input {
            opacity: 0;

            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;

            inset: 0;

            cursor: pointer;

            background:
                #b8cbd8;

            border-radius: 30px;

            transition: 0.3s;
        }

        .slider::before {
            content: "";

            position: absolute;

            width: 18px;
            height: 18px;

            left: 3px;
            top: 3px;

            background: white;

            border-radius: 50%;

            transition: 0.3s;

            box-shadow:
                0 2px 5px
                rgba(0,0,0,0.15);
        }

        .switch input:checked + .slider {
            background:
                #238bff;
        }

        .switch input:checked + .slider::before {
            transform:
                translateX(22px);
        }

        /* INFORMACIÓN DEL EVENTO */

        .evento-info {
            display: none;

            margin-top: 10px;

            padding: 10px 12px;

            border-radius: 12px;

            background:
                rgba(35,139,255,0.08);

            border-left:
                3px solid #238bff;

            color: #45657f;

            font-size: 11px;

            line-height: 1.5;
        }

        .evento-info.mostrar {
            display: block;
        }

        .evento-info strong {
            color: #123d69;
        }

        /* =========================
           MENSAJE
           ========================= */

        .mensaje-horarios {
            text-align: center;

            color: #607991;

            font-size: 13px;

            margin-top: 8px;
        }

        /* =========================
           BOTÓN CONTINUAR
           ========================= */

        .btn-continuar {
            width: 100%;

            height: 52px;

            border: none;

            border-radius: 16px;

            margin-top: 10px;

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

            letter-spacing: 0.3px;

            cursor: pointer;

            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                background-position 0.4s ease;

            box-shadow:
                0 10px 25px
                rgba(30, 145, 220, 0.25);
        }

        .btn-continuar:hover {
            transform:
                translateY(-2px);

            background-position:
                right center;

            box-shadow:
                0 15px 30px
                rgba(30, 145, 220, 0.35);
        }

        .flecha {
            margin-left: 8px;

            font-size: 21px;
        }

        /* =========================
           INFORMACIÓN INFERIOR
           ========================= */

        .info {
            display: flex;

            justify-content: center;
            align-items: center;

            gap: 8px;

            margin-top: 7px;

            color: #607991;

            font-size: 12px;
        }

        .info i {
            color: #238bff;

            font-size: 15px;
        }

        /* =========================
           RESPONSIVE
           ========================= */

        @media (max-width: 650px) {

            body {
                overflow-y: auto;
            }

            .contenedor {
                min-height: 100vh;

                height: auto;

                padding: 15px;
            }

            .card-urbanaut {
                width: 94%;

                max-height: none;

                padding:
                    22px 22px 20px;

                border-radius: 24px;

                overflow: visible;
            }

            h1 {
                font-size: 30px;
            }

            .logo img {
                width: 130px;

                max-height: 55px;
            }

            .dias-container {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .lista-horarios {
                grid-template-columns:
                    1fr;
            }
        }

        @media (max-width: 430px) {

            .card-urbanaut {
                padding:
                    20px 16px;
            }

            h1 {
                font-size: 27px;
            }

            .subtitulo {
                font-size: 13px;
            }

            .dias-container {
                grid-template-columns:
                    1fr;
            }

            .btn-dia {
                min-height: 43px;
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

            <div class="titulo-horarios">

                <i class="bi bi-calendar-week"></i>

                <h1>
                    Ingresar Horarios
                </h1>

            </div>


            <p class="subtitulo">

                Seleccioná los días y horarios
                en los que vas a estar disponible.

            </p>


            <!-- FORMULARIO -->

            <form id="formularioHorarios">


                <!-- =========================
                     DÍAS
                     ========================= -->

                <div class="dias-container">


                    <!-- LUNES -->

                    <button
                        type="button"
                        class="btn-dia"
                        data-dia="Lunes"
                        onclick="despliegaHorarios('ulLunes')">

                        <i class="bi bi-calendar2"></i>

                        Lunes

                    </button>


                    <!-- MARTES -->

                    <button
                        type="button"
                        class="btn-dia"
                        data-dia="Martes"
                        onclick="despliegaHorarios('ulMartes')">

                        <i class="bi bi-calendar2"></i>

                        Martes

                    </button>


                    <!-- MIÉRCOLES -->

                    <button
                        type="button"
                        class="btn-dia"
                        data-dia="Miercoles"
                        onclick="despliegaHorarios('ulMiercoles')">

                        <i class="bi bi-calendar2"></i>

                        Miércoles

                    </button>


                    <!-- JUEVES -->

                    <button
                        type="button"
                        class="btn-dia"
                        data-dia="Jueves"
                        onclick="despliegaHorarios('ulJueves')">

                        <i class="bi bi-calendar2"></i>

                        Jueves

                    </button>


                    <!-- VIERNES -->

                    <button
                        type="button"
                        class="btn-dia"
                        data-dia="Viernes"
                        onclick="despliegaHorarios('ulViernes')">

                        <i class="bi bi-calendar2"></i>

                        Viernes

                    </button>

                </div>


                <!-- =========================
                     CONTENEDORES DE DÍAS
                     ========================= -->

                <div
                    id="contenedorLunes"
                    class="contenedor-dia">
                </div>

                <div
                    id="contenedorMartes"
                    class="contenedor-dia">
                </div>

                <div
                    id="contenedorMiercoles"
                    class="contenedor-dia">
                </div>

                <div
                    id="contenedorJueves"
                    class="contenedor-dia">
                </div>

                <div
                    id="contenedorViernes"
                    class="contenedor-dia">
                </div>


                <!-- =========================
                     EVENTO ESPECIAL
                     ========================= -->

                <div class="evento-especial">

                    <div
                        class="evento-cabecera"
                        onclick="mostrarInfoEvento()">

                        <div class="evento-icono">

                            <i class="bi bi-star-fill"></i>

                        </div>


                        <div class="evento-texto">

                            <span class="evento-titulo">
                                Evento especial
                            </span>

                            <span class="evento-descripcion-corta">
                                Eventos extracurriculares del liceo
                            </span>

                        </div>


                        <label
                            class="switch"
                            onclick="event.stopPropagation()">

                            <input
                                type="checkbox"
                                id="eventoEspecial">

                            <span class="slider"></span>

                        </label>

                    </div>


                    <!-- INFORMACIÓN QUE APARECE AL ACTIVAR -->

                    <div
                        id="infoEvento"
                        class="evento-info">

                        <strong>
                            ¿Qué significa?
                        </strong>

                        <br>

                        Esta opción indica que formás parte de
                        los <strong>eventos extracurriculares
                        del liceo</strong> que se realizan
                        los sábados.

                        <br><br>

                        Si seleccionás esta opción, Urbanaut
                        registrará que participás de estos
                        eventos.

                        <br><br>

                        Cuando el administrador lo decida,
                        podrá habilitar la información de los
                        <strong>sábados en las estadísticas</strong>
                        de Urbanaut.

                    </div>

                </div>


                <!-- =========================
                     MENSAJE
                     ========================= -->

                <p class="mensaje-horarios">

                    <i class="bi bi-info-circle"></i>

                    Podés seleccionar más de un horario por día.

                </p>


                <!-- =========================
                     BOTÓN
                     ========================= -->

                <button
                    type="submit"
                    class="btn-continuar">

                    Guardar horarios

                    <span class="flecha">
                        →
                    </span>

                </button>

            </form>


            <!-- =========================
                 INFO
                 ========================= -->

            <div class="info">

                <i class="bi bi-clock"></i>

                Los horarios seleccionados quedarán guardados en Urbanaut.

            </div>

        </section>

    </main>


    <!-- =========================
         JAVASCRIPT
         ========================= -->

    <script>

        /* =========================
           VARIABLES
           ========================= */

        var horariosCreados = false;


        const DIAS = [

            "Lunes",
            "Martes",
            "Miercoles",
            "Jueves",
            "Viernes"

        ];


        const HORAS = [

            "8:00 - 8:40",
            "8:40 - 9:20",
            "9:25 - 10:05",
            "10:15 - 10:55",
            "11:00 - 11:40",
            "11:45 - 12:25",
            "12:25 - 13:05",
            "13:05 - 13:45",
            "13:50 - 14:30",
            "14:30 - 15:10",
            "15:15 - 15:55",
            "15:55 - 16:35"

        ];


        /* =========================
           INFORMACIÓN EVENTO ESPECIAL
           ========================= */

        function mostrarInfoEvento() {

            const info =
                document.getElementById(
                    "infoEvento"
                );

            info.classList.toggle("mostrar");

        }


        /* =========================
           CREAR HORARIO
           ========================= */

        function generarLiHorarioHora(
            dia,
            nhora
        ) {

            var liHora =
                document.createElement("li");

            liHora.classList.add(
                "li-hora"
            );

            liHora.innerHTML = `

                <label
                    for="${dia}${nhora}">

                    ${HORAS[nhora]}

                </label>

                <input
                    type="checkbox"
                    id="${dia}${nhora}"
                    name="horarios${dia}[]"
                    value="${nhora}"
                />

            `;


            var checkbox =
                liHora.querySelector(
                    "input"
                );


            checkbox.addEventListener(
                "change",
                function() {

                    actualizarColorDia(
                        dia
                    );

                }
            );


            liHora.id =
                "li" + dia + nhora;


            return liHora;

        }


        /* =========================
           CREAR LISTA DEL DÍA
           ========================= */

        function generarUlHorarioDia(
            dia
        ) {

            var ulDia =
                document.createElement(
                    "ul"
                );


            ulDia.classList.add(
                "lista-horarios"
            );


            for (
                var nhora = 0;
                nhora < HORAS.length;
                nhora++
            ) {

                var liHora =
                    generarLiHorarioHora(
                        dia,
                        nhora
                    );


                ulDia.appendChild(
                    liHora
                );

            }


            ulDia.id =
                "ul" + dia;


            return ulDia;

        }


        /* =========================
           GENERAR TODOS LOS HORARIOS
           ========================= */

        function generarHorarios() {

            for (
                var ndia = 0;
                ndia < DIAS.length;
                ndia++
            ) {

                var contenedorDia =
                    document.getElementById(
                        "contenedor" +
                        DIAS[ndia]
                    );


                var ulDia =
                    generarUlHorarioDia(
                        DIAS[ndia]
                    );


                ulDia.hidden = true;


                contenedorDia.appendChild(
                    ulDia
                );

            }

        }


        /* =========================
           ACTUALIZAR COLOR DEL DÍA
           ========================= */

        function actualizarColorDia(
            dia
        ) {

            var ul =
                document.getElementById(
                    "ul" + dia
                );


            if (!ul) return;


            var checks =
                ul.querySelectorAll(
                    'input[type="checkbox"]'
                );


            var tieneHorario = false;


            checks.forEach(
                function(check) {

                    if (check.checked) {

                        tieneHorario = true;

                    }

                }
            );


            var boton =
                document.querySelector(
                    '.btn-dia[data-dia="' +
                    dia +
                    '"]'
                );


            if (!boton) return;


            if (tieneHorario) {

                boton.classList.add(
                    "dia-seleccionado"
                );

            } else {

                boton.classList.remove(
                    "dia-seleccionado"
                );

            }

        }


        /* =========================
           MOSTRAR / OCULTAR HORARIOS
           ========================= */

        function despliegaHorarios(
            idUl
        ) {

            if (!horariosCreados) {

                generarHorarios();

                horariosCreados = true;

            }


            var ul =
                document.getElementById(
                    idUl
                );


            DIAS.forEach(
                function(dia) {

                    var otroUl =
                        document.getElementById(
                            "ul" + dia
                        );


                    if (
                        otroUl &&
                        otroUl !== ul
                    ) {

                        otroUl.hidden = true;

                    }

                }
            );


            if (ul.hidden) {

                ul.hidden = false;

            } else {

                ul.hidden = true;

            }

        }


        /* =========================
           GUARDAR HORARIOS
           ========================= */

        document
            .getElementById(
                "formularioHorarios"
            )
            .addEventListener(
                "submit",
                function(event) {

                    event.preventDefault();


                    const seleccionados =
                        document.querySelectorAll(
                            'input[type="checkbox"]:checked'
                        );


                    const eventoEspecial =
                        document.getElementById(
                            "eventoEspecial"
                        ).checked;


                    if (
                        seleccionados.length === 0 &&
                        !eventoEspecial
                    ) {

                        alert(
                            "Seleccioná al menos un horario o indicá si participás de un evento especial."
                        );

                        return;

                    }


                    /*
                     * Guardamos temporalmente
                     * si participa de eventos.
                     */

                    localStorage.setItem(
                        "eventoEspecial",
                        eventoEspecial
                    );


                    /*
                     * Ir a los datos del auto.
                     */

                    window.location.href =
                        "auto.php";

                }
            );

    </script>

</body>

</html>