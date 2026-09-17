<?php
$backend = rtrim(getenv('BACKEND_HOST') ?: '', '/');
$port    = getenv('BACKEND_PORT') ?: '';
$url     = 'http://' . $backend . ':' . $port;
?>
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
                        onclick="despliegaHorarios('ullunes')">
                        <i class="bi bi-calendar2"></i>
                        Lunes
                    </button>
                    <!-- MARTES -->
                    <button
                        type="button"
                        class="btn-dia"
                        data-dia="Martes"
                        onclick="despliegaHorarios('ulmartes')">
                        <i class="bi bi-calendar2"></i>
                        Martes
                    </button>
                    <!-- MIÉRCOLES -->
                    <button
                        type="button"
                        class="btn-dia"
                        data-dia="Miercoles"
                        onclick="despliegaHorarios('ulmiercoles')">
                        <i class="bi bi-calendar2"></i>
                        Miércoles
                    </button>
                    <!-- JUEVES -->
                    <button
                        type="button"
                        class="btn-dia"
                        data-dia="Jueves"
                        onclick="despliegaHorarios('uljueves')">
                        <i class="bi bi-calendar2"></i>
                        Jueves
                    </button>
                    <!-- VIERNES -->
                    <button
                        type="button"
                        class="btn-dia"
                        data-dia="Viernes"
                        onclick="despliegaHorarios('ulviernes')">
                        <i class="bi bi-calendar2"></i>
                        Viernes
                    </button>
                </div>
                <!-- =========================
                     CONTENEDORES DE DÍAS
                     ========================= -->
                <div
                    id="contenedorlunes"
                    class="contenedor-dia">
                </div>
                <div
                    id="contenedormartes"
                    class="contenedor-dia">
                </div>
                <div
                    id="contenedormiercoles"
                    class="contenedor-dia">
                </div>
                <div
                    id="contenedorjueves"
                    class="contenedor-dia">
                </div>
                <div
                    id="contenedorviernes"
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
    <script>
function agruparPorDia(horarios, dias) {
  const resultado = {};
  for (const d of dias){
	  resultado[d] = [];
	}
  for (const h of horarios) {
    const dia = h.dia_semana;
    resultado[dia].push(h.horario);
  }

  return resultado;
}

async function obtenerHorarios(){
	var horarios = null;
	try{
		const response = await fetch(
			<?= "\"".$url.'/api/horario.php/obtener'."\"" ?>,
			{
				method: "GET",
				headers: {
					"Content-Type": "application/json"
				},
			}
		);
		horarios = await response.json();
	}  catch (error) {
              console.error(
                  "Error al conectar con la API:",
                  error
              );
              alert(
                  "No se pudo conectar con el servidor."
              );
          }
	return horarios;
}
        const DIAS = [
            "lunes",
            "martes",
            "miercoles",
            "jueves",
            "viernes"
        ];
	var horarios = null;
	var horariosCreados = false;
	var horariosPorDia = null;
	obtenerHorarios().then(result =>{
		horarios = result;
		console.log(horarios);
		horariosPorDia = agruparPorDia(horarios["horarios"], DIAS);
	});

        function mostrarInfoEvento() {
            const info =
                document.getElementById(
                    "infoEvento"
                );
            info.classList.toggle("mostrar");
        }

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
                    ${horariosPorDia[dia][nhora]}
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
                nhora < horariosPorDia[dia].length;
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
function obtenerHorariosSeleccionados() {
    const resultado = {};
    
    for (const dia of DIAS) {
        const checkboxes = document.querySelectorAll(
            `#ul${dia} input[type="checkbox"]:checked`
        );
        resultado[dia] = Array.from(checkboxes).map(cb => {
            return parseInt(cb.id.slice(dia.length), 10);
        });
    }
    
    return resultado;
}

async function asignarHorario(nhora, dia) {
	const payload = {
		numero_hora: nhora,
		dia_semana: dia
	};
	try {
		const response = await fetch(
		<?= "\"".$url.'/api/tiene.php/asignar'."\"" ?>, 
		{
			method: "POST",
			credentials: "include",
			headers: {
				"Content-Type": "application/json"
			},
			body: JSON.stringify(payload)
		}
		);

		if (!response.ok) {
			throw new Error(`Error HTTP: ${response.status} ${response.statusText}`);
		}

		const data = await response.json();
		console.log("Respuesta del servidor:", data);
		return data;

	} catch (error) {
		console.error("Error al enviar los horarios:", error);
		alert("No se pudieron guardar los horarios. Intentá de nuevo.");
		throw error;
	}
}

        document
            .getElementById(
                "formularioHorarios"
            )
            .addEventListener(
                "submit",
                function(event) {
                    event.preventDefault();
			const horSelc = obtenerHorariosSeleccionados();
		    for (const d of DIAS){
			    for (const n of horSelc[d]){
				asignarHorario(n,d);
		            }
			}
                    //window.location.href =   "auto.php";
                }
            );
    </script>
</body>
</html>
