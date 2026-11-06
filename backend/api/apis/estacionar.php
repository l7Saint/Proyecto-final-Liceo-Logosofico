<?php
require_once __DIR__ . '/../../classes/PredictorEstacionamiento.php';
require_once __DIR__ . '/../../classes/Api.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../api/api.php';

$estacionarApi = new Api(
	name: "estacionar",
	handlers: [
		'conexion' => $conexion
	],
	prerun: null
);

$estacionarApi->addEndpoint(new Endpoint(
	name: "estacionar",
	method: "POST",
	parameters: ['ubicacion', 'fecha', 'hora'],
	run: function (array $data, array $handlers, Closure $endLog): void {
		
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
