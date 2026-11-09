<?php
require_once __DIR__ . '/../../classes/Api.php';
require_once __DIR__ . '/../../classes/PredictorEstacionamiento.php';
require_once __DIR__ . '/../../handlers/CuadraHandler.php';
require_once __DIR__ . '/../../handlers/Estaciona_enHandler.php';
require_once __DIR__ . '/../../handlers/SesionHandler.php';
require_once __DIR__ . '/../../models/Estacionamiento.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../api/api.php';

$estacionarApi = new Api(
	name: "estacionar",
	handlers: [
		'conexion' => $conexion,
		'cuadra' => new CuadraHandler($conexion),
		'sesion' => new SesionHandler($conexion),
		'estacionamiento' => new Estaciona_enHandler($conexion)
	],
	prerun: function (array $data, array $handlers, Closure $endLog): void {
		/*
		if(checkSession($handlers['sesion']) === false)
			throw new GenericRuntimeException (
				httpCode: 401,
				message: 'Unauthorized',
				getResponse: null
			);
		 */
	}
);

$estacionarApi->addEndpoint(new Endpoint(
	name: "estacionar",
	method: "POST",
	parameters: ['latitud', 'longitud', 'id_vehiculo'],
	run: function (array $data, array $handlers, Closure $endLog): void {
		$cuahnd = $handlers['cuadra'];
		$esthnd = $handlers['estacionamiento'];
		$ch = curl_init();
		$latitud = $data['latitud'];
		$longitud = $data['longitud'];
		curl_setopt($ch, CURLOPT_URL, "https://nominatim.openstreetmap.org/reverse?lat=${latitud}&lon=${longitud}&format=json&zoom=18");
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($ch), true);
		if($response === null){
			$response = curl_exec($ch);
			$errno    = curl_errno($ch);
			$error    = curl_error($ch);
			$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			($endLog)('error', "Curl failed. | response: ${response} | errno: ${errno} | error: ${error} | status: ${status} |");
			curl_close($ch);
			throw new GenericRuntimeException(
				httpCode: 500,
				message: 'Curl failed',
				getResponse: null
			);
		}
		
		try {
			$cuadra = $cuahnd->obtenerPorPuerta(
				$response['address']['road'],
				$response['address']['house_number']
			);
		} catch (Throwable $e) {
			($endLog)('error', "Unexpected error.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage(),
				getResponse: null
			);
		}
		if($cuadra === false)
			throw new GenericRuntimeException(
				httpCode: 404,
				message: 'That block is not in the area',
				getResponse: null
			);
		try {
			$now = new DateTime('now', new DateTimeZone('-03:00'));
			$id_usuario = checkSession($handlers['sesion'])->id_usuario;
			$id_est = $esthnd->crearEstacionamiento(
				new Estacionamiento(
					null,
					$id_usuario,
					$data['id_vehiculo'],
					$cuadra->id,
					$now->format('Y-m-d'),
					$now->format('H:i:s'),
					null
				));
			echo json_encode([
				'success' => true,
				'id_est' => $id_est
			]);
		} catch (Throwable $e) {
			($endLog)('error', "Unexpected error.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage(),
				getResponse: null
			);
		}
		curl_close($ch);
	}
));

$estacionarApi->addEndpoint(new Endpoint(
	name: "probabilidad",
	method: "GET",
	parameters: ['cuadras_id', 'dia_semana', 'hora'],
	run: function (array $data, array $handlers, Closure $endLog): void {
		try {
			$conexion = $handlers['conexion'];
			$predictor = new PredictorEstacionamiento($conexion);
			foreach($data['cuadras_id'] as $cuadra_id){
				$p = $predictor->calcularPuntaje($cuadra_id, $data['hora'], $data['dia_semana']);
				$output[] = [ 
					"cuadra_id" => $cuadra_id,
					"puntaje" => $p['puntaje'],
					"confianza" => $p['confianza']
			       	];
			}
			echo json_encode($output);
		} catch (Throwable $e) {
			$message = $e->getMessage();
			($endLog)('error', "Unexpected error.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage(),
				getResponse: null
			);
		}
	}
));
$main->addapi($estacionarApi);
