<?php
require_once '../../classes/Api.php'
require_once '../../handlers/TieneHandler.php'
require_once '../../config/conexion.php'
require_once '../../api/api.php'
/*
 * Hay que actualizar esta api al nuevo sistema de sesion con SesionHandler
 */
$tieneApi = new Api(
	name: "tiene",
	handlers: [
		'tiene' => new TieneHandler($conexion)
	],
	prerun: function (array $data, array $handlers): void {
		if(!checkSession())
			throw new GenericRuntimeException (
				httpCode: 401,
				message: 'Unauthorized'
			);
	}
);

$tieneApi->addEndpoint(new Endpoint(
	name: "asignar",
	method: "POST",
	parameters: ['numero_hora', 'dia_semana'],
	run: function (array $data, array $handlers): array {
		$tiehnd = $handlers['tiene'];
		session_start();
		$id_usuario = $_SESSION['user_id'];
		try {
			if($tiehnd->asignarHorario(
				$id_usuario,
				$data['numero_hora'],
				$data['dia_semana']
			)){
				return [
					'success' => true
				];
				exit;
			} else {
				error_log("Error inesperado en api.tiene.asignar");
				sendServerError();
			}
		} catch(Exception $e) {
			throw new GenericRuntimeException(
				httpCode: 500
				message: $e->getMessage()
			);
		}
	}
));

$tieneApi->addEndpoint(new Endpoint(
	name: "obtener",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers): array {
		$tiehnd = $handlers['tiene'];
		session_start();
		$id_usuario = $_SESSION['user_id'];
		try {
			$horarios = $tiehnd->obtenerPorIDUsuario($id_usuario);
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

$main->addApi($tieneApi);
