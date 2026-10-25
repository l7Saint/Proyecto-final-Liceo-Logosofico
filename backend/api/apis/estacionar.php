<?php
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
	name: "probabilidad",
	method: "GET",
	parameters: ['cuadra_id', 'dia_semana', 'hora'],
	run: function (array $data, array $handlers, Closure $endLog): array {
		try {
			$conexion = $handlers['conexion'];
			$stmt_obtenerConteoCuadra = $conexion->prepare('
			SELECT 
				co.promedio_vehiculos_carril, 
				co.cantidad_muestras, 
				ST_Distance_Sphere(
					POINT(co.longitud,co.latitud),
					POINT(cu.longitud,cu.latitud)) as distancia, 
				co.latitud, 
				co.longitud 
			FROM 
				Conteo as co, 
				Cuadra as cu 
			WHERE 
				cu.id = ? AND 
				co.dia_semana = ? AND 
				co.hora = ? 
			ORDER BY distancia ASC 
			LIMIT 20;
			');
			$stmt_obtenerConteoCuadra->execute([
				$data['cuadra_id'],
				$data['dia_semana'],
				$data['hora']
			]);	
			$conteo = [];
			while($fetch = $stmt_obtenerConteoCuadra->fetch(PDO::FETCH_ASSOC)){
				$conteo[] = $fetch;
			}

########foreach ($conteo as $i => $f) {
########    foreach (['distancia','cantidad_muestras','promedio_vehiculos_carril'] as $k) {
########	if ((float)($f[$k] ?? 0) == 0.0) {
########	    echo "fila $i → $k = " . var_export($f[$k], true) . " (tipo " . gettype($f[$k]) . ")\n";
########	}
########    }
########}
			//normalizar
			$datos = [];
			for($i = 0; $i < sizeof($conteo); $i++){
				$datos[$i]['distancia'] = exp(-1 * $conteo[$i]['distancia'] / 2200);
				$datos[$i]['muestras'] = (-1 * exp(-1 * $conteo[$i]['cantidad_muestras'] / 200)) + 1;
				$datos[$i]['trafico'] = exp(-1 * $conteo[$i]['promedio_vehiculos_carril'] / 25);
			}

			$pntj_distancia = 0;
			for($i = 0; $i < sizeof($datos); $i++){
				$pntj_distancia += $datos[$i]['distancia'];
			}
			$pntj_distancia /= sizeof($datos);

			$pntj_muestras = 0;
			for($i = 0; $i < sizeof($datos); $i++){
				$pntj_muestras += $datos[$i]['muestras'];
			}
			$pntj_muestras /= sizeof($datos);

			$pntj_trafico = 0;
			for($i = 0; $i < sizeof($datos); $i++){
				$pntj_trafico += $datos[$i]['trafico'];
			}
			$pntj_trafico /= sizeof($datos);

			echo json_encode([
				'puntaje' => $pntj_trafico * 10,
				'confianza' => $pntj_muestras * $pntj_distancia * 10
			]);
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
