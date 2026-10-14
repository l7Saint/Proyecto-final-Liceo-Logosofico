<?php
require_once '../../classes/Api.php'
require_once '../../handlers/UsuarioHandler.php'
require_once '../../handlers/HorarioHandler.php'
require_once '../../config/conexion.php'
require_once '../../api/api.php'

$horarioApi = new Api(
	name: "horario",
	handlers: [
		'usuario' => new UsuarioHandler($conexion),
		'horario' => new HorarioHandler($conexion)
	],
	prerun: function (array $data, array $handlers): void {
		if(verificarAdministrador($handlers['usuario'], $handlers['sesion']) !== 1){
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized"
			);
		}
	}
);

$horarioApi->addEndpoint(new Endpoint(
	name: "crear",
	method: "POST",
	parameters: ['numero_hora', 'dia_semana', 'horario'],
	run: function (array $data, array $handlers): array {
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
		} catch(Exception $e) {
			throw new GenericRuntimeException(
				httpCode: 500
				message: $e->getMessage()
			);
		}
	}
));

$horarioApi->addEndpoint(new Endpoint(
	name: "obtener",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers): array {
		$horhnd = handlers['horario'];
		try {
			$horarios = $horhnd->obtenerTodos();
			return [
				'success' => true,
				'horarios' => $horarios
			];
			exit;
		} catch(Exception $e) {
			throw new GenericRuntimeException(
				httpCode: 500
				message: $e->getMessage()
			);
		}
	}
));

$horarioApi->addEndpoint(new Endpoint(
	name: "eliminar",
	method: "DELETE",
	parameters: ['numero_hora', 'dia_semana'],
	run: function (array $data, array $handlers): array {
		$horhnd = handlers['horario'];
		try {
			$horhnd->elminarHorario($numero_hora, $dia_semana);
			return [
			    'success' => true
			];
		} catch(Exception $e) {
			throw new GenericRuntimeException(
				httpCode: 500
				message: $e->getMessage()
			);
		}
	}
));

$main->addApi($horarioApi);
