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
				<!-- DÍAS -->
				<div class="dias-container">
					<button
						type="button"
						class="btn-dia"
						data-dia="Lunes"
						onclick="despliegaHorarios('ullunes')">
						<i class="bi bi-calendar2"></i>
						Lunes
					</button>
					<button
						type="button"
						class="btn-dia"
						data-dia="Martes"
						onclick="despliegaHorarios('ulmartes')">
						<i class="bi bi-calendar2"></i>
						Martes
					</button>
					<button
						type="button"
						class="btn-dia"
						data-dia="Miercoles"
						onclick="despliegaHorarios('ulmiercoles')">
						<i class="bi bi-calendar2"></i>
						Miércoles
					</button>
					<button
						type="button"
						class="btn-dia"
						data-dia="Jueves"
						onclick="despliegaHorarios('uljueves')">
						<i class="bi bi-calendar2"></i>
						Jueves
					</button>
					<button
						type="button"
						class="btn-dia"
						data-dia="Viernes"
						onclick="despliegaHorarios('ulviernes')">
						<i class="bi bi-calendar2"></i>
						Viernes
					</button>
				</div>
				<!-- CONTENEDORES DE DÍAS -->
				<div id="contenedorlunes" class="contenedor-dia"></div>
				<div id="contenedormartes" class="contenedor-dia"></div>
				<div id="contenedormiercoles" class="contenedor-dia"></div>
				<div id="contenedorjueves" class="contenedor-dia"></div>
				<div id="contenedorviernes" class="contenedor-dia"></div>
				<!-- EVENTO ESPECIAL -->
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
					<div id="infoEvento" class="evento-info">
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
				<!-- MENSAJE -->
				<p class="mensaje-horarios">
					<i class="bi bi-info-circle"></i>
					Podés seleccionar más de un horario por día.
				</p>
				<!-- BOTÓN -->
				<button type="submit" class="btn-continuar">
					Guardar horarios
					<span class="flecha"> → </span>
				</button>
			</form>
			<!-- INFO -->
			<div class="info">
				<i class="bi bi-clock"></i>
				Los horarios seleccionados quedarán guardados en Urbanaut.
			</div>
		</section>
	</main>
	<script type="module">
		import UrbanautClient, {
			HttpError,
			NetworkError
		} from '/assets/js/urbanaut-js-client/src/urbanaut.js';

		const api = new UrbanautClient({
			baseUrl: <?= json_encode($url . '/api') ?>
		});

		const DIAS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];

		/** @type {Record<string, Array<{ numero_hora: number, horario: string }>> | null} */
		let horariosPorDia = null;
		let horariosCreados = false;
		let cargaHorarios = null;

		/** Groups the flat `horario[]` response into per-day, ordered buckets. */
		function agruparPorDia(horarios, dias) {
			const resultado = {};
			for (const d of dias) resultado[d] = [];

			for (const h of horarios) {
				const dia = String(h?.dia_semana ?? '').toLowerCase();
				if (!resultado[dia]) continue; // skip dias we do not render
				resultado[dia].push({
					numero_hora: Number(h.numero_hora),
					horario: String(h.horario ?? '')
				});
			}

			for (const d of dias) {
				resultado[d].sort((a, b) => a.numero_hora - b.numero_hora);
			}
			return resultado;
		}

		/** Loads the catalogue once and caches the promise. */
		function cargarHorarios() {
			if (!cargaHorarios) {
				cargaHorarios = api.tiene
					.obtenertodos()
					.then((lista) => {
						horariosPorDia = agruparPorDia(lista, DIAS);
						return horariosPorDia;
					})
					.catch((error) => {
						if (error instanceof HttpError) {
							console.error('tiene.obtenertodos', error.status, error.code, error);
							if (error.status === 401) {
								window.location.href = '../login/login.php';
								cargaHorarios = null;
								throw error;
							}
							alert(`No se pudieron cargar los horarios (HTTP ${error.status}).`);
						} else if (error instanceof NetworkError) {
							console.error('tiene.obtenertodos', error);
							alert('No se pudo conectar con el servidor.');
						} else {
							console.error('tiene.obtenertodos', error);
							alert('Ocurrió un error inesperado al cargar los horarios.');
						}
						cargaHorarios = null; // allow a retry on the next click
						throw error;
					});
			}
			return cargaHorarios;
		}

		function mostrarInfoEvento() {
			const info = document.getElementById('infoEvento');
			info.classList.toggle('mostrar');
		}
		window.mostrarInfoEvento = mostrarInfoEvento;

		function generarLiHorarioHora(dia, registro) {
			const inputId = `${dia}${registro.numero_hora}`;
			const liHora = document.createElement('li');
			liHora.classList.add('li-hora');
			liHora.id = `li${inputId}`;
			liHora.innerHTML = `
				<label for="${inputId}">${registro.horario}</label>
				<input
					type="checkbox"
					id="${inputId}"
					name="horarios${dia}[]"
					value="${registro.numero_hora}"
				/>
			`;
			liHora.querySelector('input').addEventListener('change', () => {
				actualizarColorDia(dia);
			});
			return liHora;
		}

		function generarUlHorarioDia(dia) {
			const ulDia = document.createElement('ul');
			ulDia.classList.add('lista-horarios');
			ulDia.id = `ul${dia}`;
			for (const registro of horariosPorDia[dia]) {
				ulDia.appendChild(generarLiHorarioHora(dia, registro));
			}
			return ulDia;
		}

		function generarHorarios() {
			for (const dia of DIAS) {
				const contenedorDia = document.getElementById(`contenedor${dia}`);
				const ulDia = generarUlHorarioDia(dia);
				ulDia.hidden = true;
				contenedorDia.appendChild(ulDia);
			}
		}

		function actualizarColorDia(dia) {
			const ul = document.getElementById(`ul${dia}`);
			if (!ul) return;
			const tieneHorario = [...ul.querySelectorAll('input[type="checkbox"]')]
				.some((check) => check.checked);
			const boton = [...document.querySelectorAll('.btn-dia')]
				.find((b) => (b.dataset.dia || '').toLowerCase() === dia);
			if (!boton) return;
			boton.classList.toggle('dia-seleccionado', tieneHorario);
		}

		async function despliegaHorarios(idUl) {
		console.log(idUl);
			try {
				await cargarHorarios();
			} catch {
		console.log('pepito');
				return; // the loader already alerted the user
			}

			if (!horariosCreados) {
				generarHorarios();
				horariosCreados = true;
			}

			const ul = document.getElementById(idUl);
			if (!ul) return;

			DIAS.forEach((dia) => {
				const otroUl = document.getElementById(`ul${dia}`);
				if (otroUl && otroUl !== ul) otroUl.hidden = true;
			});

			ul.hidden = !ul.hidden;
		}
		window.despliegaHorarios = despliegaHorarios;

		function obtenerHorariosSeleccionados() {
			const resultado = {};
			for (const dia of DIAS) {
				const checkboxes = document.querySelectorAll(
					`#ul${dia} input[type="checkbox"]:checked`
				);
				resultado[dia] = Array.from(checkboxes).map((cb) => Number(cb.value));
			}
			return resultado;
		}

		/** Claims one slot for the authenticated user (id comes from the token). */
		function asignarHorario(numero_hora, dia_semana) {
			return api.tiene.asignar({ numero_hora, dia_semana });
		}

		document
			.getElementById('formularioHorarios')
			.addEventListener('submit', async (event) => {
				event.preventDefault();

				if (!api.isAuthenticated) {
					alert('Necesitás iniciar sesión para guardar tus horarios.');
					window.location.href = '../login/login.php';
					return;
				}

				const seleccion = obtenerHorariosSeleccionados();
				const total = DIAS.reduce((acc, dia) => acc + seleccion[dia].length, 0);

				if (total === 0) {
					alert('Seleccioná al menos un horario antes de guardar.');
					return;
				}

				const boton = event.submitter ?? document.querySelector('#formularioHorarios .btn-continuar');
				if (boton) boton.disabled = true;

				const errores = [];
				try {
					for (const dia of DIAS) {
						for (const numero_hora of seleccion[dia]) {
							try {
								await asignarHorario(numero_hora, dia);
							} catch (error) {
								errores.push({ dia, numero_hora, error });
								console.error(`tiene.asignar (${dia} #${numero_hora})`, error);
							}
						}
					}
				} finally {
					if (boton) boton.disabled = false;
				}

				if (errores.length === 0) {
					alert('Horarios guardados correctamente.');
					window.location.href = './auto.php';
					return;
				}

				const sesionExpirada = errores.some(
					({ error }) => error instanceof HttpError && error.status === 401
				);
				if (sesionExpirada) {
					alert('Tu sesión expiró. Iniciá sesión de nuevo.');
					window.location.href = '../login/login.php';
					return;
				}

				alert(
					`No se pudieron guardar ${errores.length} de ${total} horarios. ` +
					'Puede que ya los tuvieras asignados. Intentá de nuevo.'
				);
			});
	</script>
</body>
</html>
