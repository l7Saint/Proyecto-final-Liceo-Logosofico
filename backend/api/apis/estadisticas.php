<?php
require_once __DIR__ . '/../../classes/Api.php';
require_once __DIR__ . '/../../handlers/Estaciona_enHandler.php';
require_once __DIR__ . '/../../handlers/SesionHandler.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../api/api.php';

/*
 * API de estadisticas del usuario.
 *
 * Expone un unico endpoint que devuelve los agregados de los registros de
 * estacionamientos del usuario autenticado: total de veces que estaciono,
 * cuadras distintas, veces en el mes en curso y el ranking de cuadras donde
 * mas estaciono.
 *
 * El usuario sale siempre de la sesion (Bearer token), nunca del body, asi
 * que un usuario solo puede leer sus propias estadisticas.
 */
$estadisticasApi = new Api(
	name: "estadisticas",
	handlers: [
		'estacionamiento' => new Estaciona_enHandler($conexion),
		'sesion' => new SesionHandler($conexion)
	],
	prerun: function (array $data, array $handlers, Closure $endLog): void {
		if(checkSession($handlers['sesion']) === false)
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized",
				getResponse: null
			);
	}
);

$estadisticasApi->addEndpoint(new Endpoint(
	name: "resumen",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers, Closure $endLog): void {
		$esthnd = $handlers['estacionamiento'];

		// El prerun ya valido la sesion; se resuelve de nuevo para obtener el id_usuario.
		$sesion = checkSession($handlers['sesion']);
		if($sesion === false)
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized",
				getResponse: null
			);

		// `limite` es opcional: acota cuantas cuadras devuelve el ranking.
		// Se mantiene fijo en 10: el cliente pide este endpoint con GET sin body
		// (fetch no permite body en GET), y el router solo lee php://input, asi
		// que un parametro por query string no llegaria hasta aca.
		$limite = 10;

		try {
			$conteo  = $esthnd->contarPorUsuario((int) $sesion->id_usuario);
			$cuadras = $esthnd->obtenerCuadrasMasUsadas((int) $sesion->id_usuario, $limite);
		} catch(Throwable $e) {
			($endLog)('error', "Unexpected error, message: " . $e->getMessage());
			throw new GenericRuntimeException(
				httpCode: 500,
				message: "Could not read estadisticas.",
				getResponse: null
			);
		}

		// Porcentaje de cada cuadra sobre el total. El total se protege contra 0
		// (usuario sin registros) para no dividir por cero.
		$total = $conteo['total'];
		foreach ($cuadras as $i => $cuadra) {
			$cuadras[$i]['porcentaje'] = ($total > 0)
				? round(($cuadra['total'] * 100) / $total, 1)
				: 0.0;
		}

		($endLog)('debug', "Estadisticas de usuario id={$sesion->id_usuario}: {$total} registros.");

		echo(json_encode([
			'success' => true,
			'resumen' => [
				'total'             => $total,
				'cuadras_distintas' => $conteo['cuadras_distintas'],
				'este_mes'          => $conteo['este_mes'],
				'primera_fecha'     => $conteo['primera_fecha'],
				'ultima_fecha'      => $conteo['ultima_fecha'],
				'cuadras'           => $cuadras
			]
		]));
	}
));

$main->addApi($estadisticasApi);
