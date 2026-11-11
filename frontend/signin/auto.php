<?php
$backend = rtrim(getenv('BACKEND_HOST') ?: '', '/');
$port    = getenv('BACKEND_PORT') ?: '';
$url     = 'http://' . $backend . ':' . $port;

$apiKey = '';
$apiSecret = '';
if (isset($_GET['marca_id'])) {
	$marcaId = filter_input(INPUT_GET, 'marca_id', FILTER_VALIDATE_INT);
}
if (isset($marcaId) && $marcaId !== false && $marcaId !== null) {
	$url = 'https://carapi.app/api/models/v2?make_id=' . $marcaId;
} else {
	$url = 'https://carapi.app/api/makes/v2';
}
$ch = curl_init();
curl_setopt_array($ch, [
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_HTTPHEADER => [
		'Accept: application/json',
		'api-key: ' . $apiKey,
		'api-secret: ' . $apiSecret
	]
]);
$respuesta = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($httpCode !== 200) {
	die("Error en la petición: Código " . $httpCode);
}
$datos = json_decode($respuesta, true);
if (isset($_GET['marca_id'])) {
	header('Content-Type: application/json');
	echo json_encode($datos['data'] ?? []);
	exit;
}
$marcas = $datos['data'] ?? [];

// NOTE: the block above hijacks $url for the carapi proxy, so the Urbanaut
// backend URL is rebuilt here for the client instance.
$backendHost = rtrim(getenv('BACKEND_HOST') ?: '', '/');
$backendPort = getenv('BACKEND_PORT') ?: '';
$urbanautUrl = 'http://' . $backendHost . ':' . $backendPort . '/api';
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Urbanaut | Datos del Auto</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/css/auto.css">
</head>
<body>
<main class="contenedor">
<section class="card-urbanaut">
	<div class="logo">
		<img src="../imagenes/logoNuevo2.png" alt="Logo Urbanaut">
	</div>
	<div class="titulo">
		<i class="bi bi-car-front-fill"></i>
		<h1>
			Tus Autos
		</h1>
	</div>
	<p class="subtitulo">
		¿Cuántos vehículos querés ingresar?
	</p>
	<div class="selector-autos">
		<button type="button" class="opcion-auto" id="unAuto" onclick="seleccionarAutos(1)">
			<i class="bi bi-car-front-fill"></i>
			<strong>
				1 auto
			</strong>
			<span>
				Ingresar un vehículo
			</span>
		</button>
		<button type="button" class="opcion-auto" id="dosAutos" onclick="seleccionarAutos(2)">
			<i class="bi bi-car-front-fill"></i>
			<strong>
				2 autos
			</strong>
			<span>
				Ingresar dos vehículos
			</span>
		</button>
	</div>
	<form id="formularioAuto">
		<div id="datosAutos">
			<div class="auto">
				<div class="auto-titulo">
					<i class="bi bi-1-circle-fill"></i>
					Auto 1
				</div>
				<div class="campo">
					<i class="bi bi-car-front"></i>
					<select id="marca1" name="marca1" required>
					<option value="" selected disabled>Seleccione la marca del vehículo</option>
						<?php foreach ($marcas as $marca): ?>
							<option value="<?= htmlspecialchars($marca['id']) ?>">
								<?= htmlspecialchars($marca['name']) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="campo">
					<i class="bi bi-car-front"></i>
					<select id="modelo1" name="modelo1" required>
						<option value="" selected disabled> Seleccione primero una marca </option>
					</select>
				</div>
				<div class="campo">
					<i class="bi bi-palette"></i>
					<select id="color1" name="color1" required>
						<option value="" selected disabled>
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
				<div class="campo">
					<i class="bi bi-arrows-angle-expand"></i>
					<select id="tamano1" name="tamano1" required>
						<option value="" selected disabled>
							Seleccioná el tamaño
						</option>
						<option value="chico">Chico</option>
						<option value="mediano">Mediano</option>
						<option value="grande">Grande</option>
					</select>
				</div>
			</div>
			<div class="auto" id="auto2" style="display: none;">
				<div class="auto-titulo">
					<i class="bi bi-2-circle-fill"></i>
					Auto 2
				</div>
				<div class="campo">
					<i class="bi bi-car-front"></i>
					<select id="marca2" name="marca2" required>
						<option value="" selected disabled>Seleccione la marca del vehículo</option>
						<?php foreach ($marcas as $marca): ?>
							<option value="<?= htmlspecialchars($marca['id']) ?>">
								<?= htmlspecialchars($marca['name']) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="campo">
					<i class="bi bi-car-front"></i>
					<select id="modelo2" name="modelo2" required>
						<option value="" selected disabled> Seleccione primero una marca </option>
					</select>
				</div>
				<div class="campo">
					<i class="bi bi-palette"></i>
					<select id="color2" name="color2">
						<option value="" selected disabled>
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
				<div class="campo">
					<i class="bi bi-arrows-angle-expand"></i>
					<select id="tamano2" name="tamano2">
						<option value="" selected disabled>
							Seleccioná el tamaño
						</option>
						<option value="chico">Chico</option>
						<option value="mediano">Mediano</option>
						<option value="grande">Grande</option>
					</select>
				</div>
			</div>
			<button type="submit" class="btn-continuar">
				Continuar
				<span class="flecha">✓</span>
			</button>
		</div>
	</form>
	<div class="info">
		<i class="bi bi-shield-check"></i>
		Podés registrar hasta dos vehículos.
	</div>
</section>
</main>
<script type="module">
import UrbanautClient, {
	HttpError,
	NetworkError
} from '/assets/js/urbanaut-js-client/src/urbanaut.js';

const api = new UrbanautClient({
	baseUrl: <?= json_encode($urbanautUrl) ?>
});

let cantidadAutos = 0;

function seleccionarAutos(cantidad) {
	cantidadAutos = cantidad;
	const datosAutos = document.getElementById('datosAutos');
	const auto2 = document.getElementById('auto2');
	const unAuto = document.getElementById('unAuto');
	const dosAutos = document.getElementById('dosAutos');

	datosAutos.style.display = 'block';
	unAuto.classList.remove('activa');
	dosAutos.classList.remove('activa');

	const marcar = (id, requerido) => {
		const el = document.getElementById(id);
		if (el) el.required = requerido;
	};

	if (cantidad === 1) {
		unAuto.classList.add('activa');
		auto2.style.display = 'none';
		marcar('marca2', false);
		marcar('modelo2', false);
		marcar('color2', false);
		marcar('tamano2', false);
	} else {
		dosAutos.classList.add('activa');
		auto2.style.display = 'block';
		marcar('marca2', true);
		marcar('modelo2', true);
		marcar('color2', true);
		marcar('tamano2', true);
	}

	setTimeout(() => {
		datosAutos.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
	}, 100);
}
window.seleccionarAutos = seleccionarAutos;

document.getElementById('formularioAuto').addEventListener('submit', async (event) => {
	event.preventDefault();

	if (cantidadAutos === 0) {
		alert('Seleccioná primero cuántos autos querés ingresar.');
		return;
	}

	if (!api.isAuthenticated) {
		alert('Necesitás iniciar sesión para registrar tus vehículos.');
		window.location.href = '../login/login.php';
		return;
	}

	const autos = leerAutos(cantidadAutos);
	const incompletos = autos.filter((auto) => !auto.marca || !auto.modelo || !auto.tamano);
	if (incompletos.length > 0) {
		alert('Completá marca, modelo y tamaño de cada vehículo.');
		return;
	}

	const boton = event.submitter ?? document.querySelector('#formularioAuto .btn-continuar');
	if (boton) boton.disabled = true;

	try {
		for (const auto of autos) {
			await api.vehiculo.crear(auto);
		}
		window.location.href = '../inicio/index.html';
	} catch (error) {
		if (error instanceof HttpError) {
			console.error('vehiculo.crear', error.status, error.code, error);
			if (error.status === 401) {
				alert('Tu sesión expiró. Iniciá sesión de nuevo.');
				window.location.href = '../login/login.php';
			} else {
				alert(`No se pudo guardar el vehículo (HTTP ${error.status}).`);
			}
		} else if (error instanceof NetworkError) {
			console.error('vehiculo.crear', error);
			alert('No se pudo conectar con el servidor.');
		} else {
			console.error('vehiculo.crear', error);
			alert('Ocurrió un error inesperado al guardar los vehículos.');
		}
	} finally {
		if (boton) boton.disabled = false;
	}
});

/** Reads the visible car blocks into `vehiculo.crear` payloads. */
function leerAutos(cantidad) {
	const indices = cantidad === 2 ? [1, 2] : [1];
	return indices.map((i) => ({
		// The backend stores the vehicle make/model as human-readable text,
		// so the option label is sent instead of the carapi numeric id.
		marca: textoDe(document.getElementById(`marca${i}`)),
		modelo: textoDe(document.getElementById(`modelo${i}`)),
		color: document.getElementById(`color${i}`).value,
		tamano: document.getElementById(`tamano${i}`).value
	}));
}

function textoDe(select) {
	const option = select?.options?.[select.selectedIndex];
	if (!option || option.disabled) return '';
	return option.textContent.trim();
}

document.getElementById('marca1').addEventListener('change', function () {
	cargarModelos(this.value, 'modelo1');
});
document.getElementById('marca2').addEventListener('change', function () {
	cargarModelos(this.value, 'modelo2');
});

function cargarModelos(marcaId, modeloId) {
	const selectModelo = document.getElementById(modeloId);
	selectModelo.innerHTML = '<option value="" selected disabled>Cargando modelos...</option>';
	fetch('?marca_id=' + marcaId)
		.then((response) => response.json())
		.then((modelos) => {
			selectModelo.innerHTML = '<option value="" selected disabled>Seleccione el modelo</option>';
			modelos.forEach((modelo) => {
				const option = document.createElement('option');
				option.value = modelo.id;
				option.textContent = modelo.name;
				selectModelo.appendChild(option);
			});
		})
		.catch((error) => {
			console.error(error);
			selectModelo.innerHTML = '<option value="" selected disabled>Error al cargar modelos</option>';
		});
}
</script>
</body>
</html>
