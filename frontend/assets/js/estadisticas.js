/**
 * Página de estadísticas del usuario.
 *
 * Consume `GET /api/estadisticas/resumen` a través del cliente JS y pinta:
 *   - tres tarjetas: total de estacionamientos, cuadras distintas y veces este mes;
 *   - un gráfico de barras con las cuadras donde más estacionó;
 *   - el ranking completo (hasta 10 cuadras) con cantidad y porcentaje del total.
 *
 * La identidad la resuelve el backend con el Bearer token: acá solo se verifica
 * que haya sesión antes de pedir los datos.
 */

import UrbanautClient, { HttpError, NetworkError } from '/assets/js/urbanaut-js-client/src/urbanaut.js';

const api = new UrbanautClient({
	baseUrl: window.URBANAUT_API_BASE || '/api'
});

/** Cuántas cuadras muestra el gráfico (el ranking muestra todas las recibidas). */
const CUADRAS_EN_GRAFICO = 5;

const el = {
	total: document.getElementById('statTotal'),
	cuadras: document.getElementById('statCuadras'),
	mes: document.getElementById('statMes'),
	grafico: document.getElementById('graficoContenedor'),
	lienzo: document.getElementById('graficoCuadras'),
	ranking: document.getElementById('rankingCuadras'),
	vacio: document.getElementById('rankingVacio'),
	error: document.getElementById('mensajeError')
};

let grafico = null;

/**
 * Etiqueta legible de una cuadra: "Calle 1200–1350" o "Calle s/n".
 * @param {{ calle: string, puerta_inicial: number|null, puerta_final: number|null }} cuadra
 * @returns {string}
 */
function etiquetaCuadra(cuadra) {
	const calle = cuadra.calle || 'Cuadra sin nombre';
	if (cuadra.puerta_inicial === null && cuadra.puerta_final === null) {
		return `${calle} (s/n)`;
	}
	if (cuadra.puerta_inicial === null) return `${calle} (hasta ${cuadra.puerta_final})`;
	if (cuadra.puerta_final === null) return `${calle} (desde ${cuadra.puerta_inicial})`;
	return `${calle} ${cuadra.puerta_inicial}–${cuadra.puerta_final}`;
}

/**
 * Formatea `YYYY-MM-DD` como `DD/MM/YYYY` sin pasar por Date, para no
 * desplazar el día por zona horaria.
 * @param {string|null} fecha
 * @returns {string}
 */
function formatearFecha(fecha) {
	if (typeof fecha !== 'string' || !/^\d{4}-\d{2}-\d{2}$/.test(fecha)) return '';
	const [anio, mes, dia] = fecha.split('-');
	return `${dia}/${mes}/${anio}`;
}

/** @param {number} valor */
function textoVeces(valor) {
	return valor === 1 ? 'vez' : 'veces';
}

/**
 * Pinta las tarjetas superiores.
 * @param {{ total: number, cuadras_distintas: number, este_mes: number }} resumen
 */
function pintarResumen(resumen) {
	el.total.textContent = String(resumen.total);
	el.cuadras.textContent = String(resumen.cuadras_distintas);
	el.mes.textContent = String(resumen.este_mes);
}

/**
 * Pinta el ranking. Cada ítem muestra posición, cuadra, barra proporcional,
 * cantidad de veces y la fecha del último registro.
 * @param {Array<object>} cuadras
 */
function pintarRanking(cuadras) {
	el.ranking.textContent = '';

	if (cuadras.length === 0) {
		el.ranking.hidden = true;
		el.vacio.hidden = false;
		return;
	}

	el.ranking.hidden = false;
	el.vacio.hidden = true;

	cuadras.forEach((cuadra, indice) => {
		const item = document.createElement('li');

		const posicion = document.createElement('span');
		posicion.className = 'posicion';
		posicion.textContent = String(indice + 1);

		const datos = document.createElement('div');
		datos.className = 'datos';

		const nombre = document.createElement('p');
		nombre.className = 'calle';
		nombre.textContent = etiquetaCuadra(cuadra);

		const ultima = formatearFecha(cuadra.ultima_vez);
		if (ultima) {
			const detalle = document.createElement('span');
			detalle.className = 'ultima';
			detalle.textContent = ` · última vez ${ultima}`;
			nombre.append(detalle);
		}

		const barra = document.createElement('div');
		barra.className = 'barra';

		const relleno = document.createElement('span');
		relleno.className = 'barra-fill';
		// El ancho se acota a 100%: el porcentaje es sobre el total de
		// estacionamientos y una sola cuadra puede concentrarlos casi todos.
		relleno.style.width = `${Math.max(4, Math.min(100, cuadra.porcentaje))}%`;
		barra.append(relleno);

		datos.append(nombre, barra);

		const veces = document.createElement('span');
		veces.className = 'veces';
		veces.innerHTML = `${cuadra.total}<small>${textoVeces(cuadra.total)}</small>`;

		item.append(posicion, datos, veces);
		el.ranking.append(item);
	});
}

/**
 * Pinta el gráfico de barras con las cuadras más usadas.
 * @param {Array<object>} cuadras
 */
function pintarGrafico(cuadras) {
	if (typeof Chart === 'undefined') {
		// Sin Chart.js (CDN caído) las tarjetas y el ranking siguen siendo útiles.
		el.grafico.hidden = true;
		return;
	}

	const top = cuadras.slice(0, CUADRAS_EN_GRAFICO);

	if (grafico) {
		grafico.destroy();
		grafico = null;
	}

	el.grafico.hidden = cuadras.length === 0;
	if (cuadras.length === 0) return;

	grafico = new Chart(el.lienzo, {
		type: 'bar',
		data: {
			labels: top.map((cuadra) => etiquetaCuadra(cuadra)),
			datasets: [{
				label: 'Veces estacionado',
				data: top.map((cuadra) => cuadra.total),
				backgroundColor: 'rgba(35, 139, 255, 0.65)',
				borderColor: '#238bff',
				borderWidth: 2,
				borderRadius: 10
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: { display: false },
				tooltip: {
					callbacks: {
						label: (contexto) => `${contexto.parsed.y} ${textoVeces(contexto.parsed.y)}`
					}
				}
			},
			scales: {
				y: {
					beginAtZero: true,
					ticks: { stepSize: 1, precision: 0 }
				},
				x: {
					ticks: {
						autoSkip: false,
						callback: function (valor) {
							// Las etiquetas largas ("Calle 1200–1350") se cortan a
							// dos líneas para que entren en el ancho del card.
							const texto = this.getLabelForValue(valor);
							return texto.length > 22 ? `${texto.slice(0, 21)}…` : texto;
						}
					}
				}
			}
		}
	});
}

/** @param {string} mensaje */
function mostrarError(mensaje) {
	el.error.textContent = mensaje;
	el.error.hidden = false;
}

/** Vuelve a las tarjetas al estado de carga. */
function marcarCargando() {
	el.total.textContent = '—';
	el.cuadras.textContent = '—';
	el.mes.textContent = '—';
}

async function cargar() {
	if (!api.isAuthenticated) {
		window.location.href = '../login/login.php';
		return;
	}

	marcarCargando();
	el.error.hidden = true;

	try {
		const resumen = await api.estadisticas.resumen();
		pintarResumen(resumen);
		pintarGrafico(resumen.cuadras);
		pintarRanking(resumen.cuadras);
	} catch (error) {
		if (error instanceof HttpError && error.status === 401) {
			api.clearSession();
			window.location.href = '../login/login.php';
			return;
		}

		if (error instanceof NetworkError) {
			mostrarError('No se pudieron cargar tus estadísticas: no hay conexión con el servidor.');
		} else {
			mostrarError('No se pudieron cargar tus estadísticas. Probá de nuevo en unos minutos.');
		}

		el.grafico.hidden = true;
		el.ranking.hidden = true;
		el.vacio.hidden = true;
		console.error('estadisticas.resumen', error);
	}
}

// Si el token expira en otra pestaña, el cliente limpia la sesión y avisa.
api.on('unauthorized', () => {
	window.location.href = '../login/login.php';
});

cargar();
