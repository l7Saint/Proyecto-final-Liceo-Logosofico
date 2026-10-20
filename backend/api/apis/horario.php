<?php
require_once __DIR__ . '/../../classes/Api.php';
require_once __DIR__ . '/../../handlers/UsuarioHandler.php';
require_once __DIR__ . '/../../handlers/HorarioHandler.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../api/api.php';

$horarioApi = new Api(
	name: "horario",
	handlers: [
		'usuario' => new UsuarioHandler($conexion),
		'horario' => new HorarioHandler($conexion)
	],
	prerun: function (array $data, array $handlers, Closure $endLog): void {
		if(verificarAdministrador($handlers['usuario'], $handlers['sesion']) !== 1){
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized",
				getResponse: null
			);
		}
	}
);

$horarioApi->addEndpoint(new Endpoint(
	name: "crear",
	method: "POST",
	parameters: ['numero_hora', 'dia_semana', 'horario'],
	run: function (array $data, array $handlers, Closure $endLog): array {
		$horhnd = handlers['horario'];
		$horario = new Horario(
			$data["numero_hora"],
			$data["dia_semana"],
			$data["horario"]
		);
		try {
			$horhnd->crearHorario($horario);
			return [
			    'success' => true
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

$horarioApi->addEndpoint(new Endpoint(
	name: "obtener",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers, Closure $endLog): array {
		$horhnd = handlers['horario'];
		try {
			$horarios = $horhnd->obtenerTodos();
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

$horarioApi->addEndpoint(new Endpoint(
	name: "eliminar",
	method: "DELETE",
	parameters: ['numero_hora', 'dia_semana'],
	run: function (array $data, array $handlers, Closure $endLog): array {
		$horhnd = handlers['horario'];
		try {
			$horhnd->elminarHorario($numero_hora, $dia_semana);
			return [
			    'success' => true
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

$main->addApi($horarioApi);
