<?php
require_once __DIR__ . '/../../classes/Api.php';
require_once __DIR__ . '/../../handlers/TieneHandler.php';
require_once __DIR__ . '/../../handlers/SesionHandler.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../api/api.php';

$tieneApi = new Api(
	name: "tiene",
	handlers: [
		'tiene' => new TieneHandler($conexion),
		'sesion' => new SesionHandler($conexion)
	],
	prerun: function (array $data, array $handlers, Closure $endLog): void {
		if(checkSession($handlers['sesion']) === false)
			throw new GenericRuntimeException (
				httpCode: 401,
				message: 'Unauthorized',
				getResponse: null
			);
	}
);

$tieneApi->addEndpoint(new Endpoint(
	name: "asignar",
	method: "POST",
	parameters: ['numero_hora', 'dia_semana'],
	run: function (array $data, array $handlers, Closure $endLog): array {
		$tiehnd = $handlers['tiene'];
		$id_usuario = checkSession($handlers['sesion'])->id_usuario;
		try {
			if($tiehnd->asignarHorario(
				$id_usuario,
				$data['numero_hora'],
				$data['dia_semana']
			)){
				return [
					'success' => true
				];
			} else {
				($endLog)('error', "Unexpected error, asignarHorario returned 'false'.");
				throw new GenericRuntimeException(
					httpCode: 500,
					message: '',
					getResponse: null
				);
			}
		} catch(Throwable $e) {
			$message = $e->getMessage();
			($endLog)('error', "Unexpected error, message: $message.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage(),
				getResponse: null
			);
		}
	}
));

$tieneApi->addEndpoint(new Endpoint(
	name: "obtener",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers, Closure $endLog): array {
		$tiehnd = $handlers['tiene'];
		$id_usuario = checkSession($handlers['sesion'])->id_usuario;
		try {
			$horarios = $tiehnd->obtenerPorIDUsuario($id_usuario);
			return [
				'success' => true,
				'horarios' => $horarios
			];
		} catch(Throwable $e) {
			$message = $e->getMessage();
			($endLog)('error', "Unexpected error, message: $message.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage(),
				getResponse: null
			);
		}
	}
));

$main->addApi($tieneApi);
