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

	<title>Urbanaut | Estadísticas</title>

	<!-- Bootstrap -->
	<link
		href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
		rel="stylesheet">

	<!-- Bootstrap Icons -->
	<link
		rel="stylesheet"
		href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

	<!-- Chart.js -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/estadisticas.css">
</head>

<body>
	<main class="contenedor">
		<section class="card-urbanaut">

			<!-- TÍTULO -->
			<div class="titulo">
				<i class="bi bi-bar-chart-line-fill"></i>
				<h1>Mis Estadísticas</h1>
			</div>

			<p class="subtitulo">
				Dónde estacionaste y cuántas veces lo hiciste.
			</p>

			<!-- ESTADÍSTICAS -->
			<div class="estadisticas">
				<div class="estadistica-card">
					<i class="bi bi-car-front-fill"></i>
					<span class="numero" id="statTotal">—</span>
					<span class="texto">Veces que estacionaste</span>
				</div>

				<div class="estadistica-card">
					<i class="bi bi-geo-alt-fill"></i>
					<span class="numero" id="statCuadras">—</span>
					<span class="texto">Cuadras distintas</span>
				</div>

				<div class="estadistica-card">
					<i class="bi bi-calendar-check-fill"></i>
					<span class="numero" id="statMes">—</span>
					<span class="texto">Veces este mes</span>
				</div>
			</div>

			<!-- ERROR -->
			<p class="mensaje-error" id="mensajeError" hidden></p>

			<!-- GRÁFICO -->
			<div class="grafico-contenedor" id="graficoContenedor">
				<h2>Dónde estacionaste más</h2>
				<div class="grafico">
					<canvas id="graficoCuadras" aria-label="Estacionamientos por cuadra"></canvas>
				</div>
			</div>

			<!-- RANKING -->
			<div class="ranking-contenedor">
				<h2>Tus cuadras más usadas</h2>

				<ol class="ranking" id="rankingCuadras"></ol>

				<p class="empty-state" id="rankingVacio" hidden>
					<i class="bi bi-pin-map"></i>
					Todavía no registraste estacionamientos.
					<a href="../mapa/index.html">Estacioná desde el mapa</a> y volvé para ver tus estadísticas.
				</p>
			</div>

			<!-- BOTÓN -->
			<div class="botones">
				<a href="../inicio/index.html" class="volver">
					<i class="bi bi-house"></i>
					Volver a Inicio
				</a>
			</div>

		</section>
	</main>

	<!-- El controller necesita la URL del backend, que depende del entorno:
	     se inyecta acá como global para no hardcodearla en el módulo. -->
	<script>
		window.URBANAUT_API_BASE = <?= json_encode($url . '/api') ?>;
	</script>
	<script type="module" src="/assets/js/estadisticas.js"></script>
</body>

</html>
