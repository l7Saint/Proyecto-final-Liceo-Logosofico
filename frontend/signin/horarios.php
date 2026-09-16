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

    <link
        rel="stylesheet"
        href="/assets/css/style.css">
    <link
        rel="stylesheet"
        href="/assets/css/horarios.css">
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
