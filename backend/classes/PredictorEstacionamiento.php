<?php
require_once __DIR__ . '/Normalizar.php';
require_once __DIR__ . '/../models/Cuadra.php';

class PredictorEstacionamiento {
	private PDOStatement $stmt_obtenerConteoCuadra;
	private PDOStatement $stmt_obtenerReporteCuadraDia;
	private PDOStatement $stmt_obtenerReporteCuadraHora;
	private PDOStatement $stmt_obtenerReporteCuadraAhora;
	private PDOStatement $stmt_obtenerEstacionarCuadra;
	private PDOStatement $stmt_obtenerEstacionarCuadraId;
	private PDOStatement $stmt_obtenerEstacionarCuadraCantidad;

	public function calcularPuntaje(int $id_cuadra, int $hora, string $dia_semana) : array {
		$PESO_CONTEO = 1/3;
		$PESO_REPORTE = 1/3;
		$PESO_CUADRA = 1/3;

		$conteo = $this->normalizar_conteoCuadra($id_cuadra, $hora, $dia_semana);
		$reporte = $this->normalizar_reporteCuadra($id_cuadra, $hora, $dia_semana);
		$cuadra = $this->normalizar_estacionarCuadra($id_cuadra);

		if((float)$cuadra[1] === 0.0) { //si la confianza de la cuadra es 0
			$PESO_CONTEO = 1/2;
			$PESO_REPORTE = 1/2;
			$PESO_CUADRA = 0;
		}

		return [
			'puntaje' => [
				'conteo' => $conteo[0]*$PESO_CONTEO,
				'reporte' => $reporte[0]*$PESO_REPORTE,
				'cuadra' => $cuadra[0]*$PESO_CUADRA
			],

			'confianza' => [
				'conteo' => $conteo[1]*$PESO_CONTEO,
				'reporte' => $reporte[1]*$PESO_REPORTE,
				'cuadra' => $cuadra[1]*$PESO_CUADRA
			]
		];
	}

	private function normalizar_conteoCuadra(int $id_cuadra, int $hora, string $dia_semana) : array {
		$PESO_DISTANCIA = 1/2;
		$PESO_MUESTRAS = 1/2;
		$K_MAX_DISTANCIA = 450; // Metros
		$K_MAX_MUESTRAS = 200;
		$K_MAX_VEHICULOS_CARRIL = 35;
		$U_DISTANCIA = 2.5;
		$U_MUESTRAS = 5;
		$U_VEHICULOS = 2;

		$this->stmt_obtenerConteoCuadra->execute([
			$hora,$dia_semana,$id_cuadra,$K_MAX_DISTANCIA]);
		$datos = [];
		while($fetch = $this->stmt_obtenerConteoCuadra->fetch(PDO::FETCH_ASSOC))
			$datos[] = $fetch;

		$normalizado = ['distancia' => [],'muestras' => [],'trafico' => []];
		for($i = 0; $i < sizeof($datos); $i++){
			$normalizado['distancia'][$i] = Normalizar::exponente_dNegativo(
				x: $datos[$i]['distancia'],
				k: $K_MAX_DISTANCIA,
				u: $U_DISTANCIA
			);
			$normalizado['muestras'][$i] = Normalizar::exponente_dPositivo(
				x: $datos[$i]['cantidad_muestras'],
				k: $K_MAX_MUESTRAS,
				u: $U_MUESTRAS
			);
			$normalizado['trafico'][$i] = Normalizar::exponente_dNegativo(
				x: $datos[$i]['promedio_vehiculos_carril'],
				k: $K_MAX_VEHICULOS_CARRIL,
				u: $U_VEHICULOS
			);
		}
		$pntj_distancia = Normalizar::mediana($normalizado['distancia']);
		$pntj_muestras = Normalizar::mediana($normalizado['muestras']);
		$pntj_trafico = Normalizar::mediana($normalizado['trafico']);

		return [
			(float) $pntj_trafico, //puntaje
			(float) ($PESO_DISTANCIA*$pntj_distancia + $PESO_MUESTRAS*$pntj_muestras) //confianza
		];
	}

	private function normalizar_reporteCuadra(int $id_cuadra, int $hora, string $dia_semana) : array {
		$PESO_DIA = 25/100;
		$PESO_HORA = 35/100;
		$PESO_AHORA = 40/100;
		$K_MAX_VALOR = 10;
		$K_CANTIDAD_REPORTES = 20;
		$U_VALOR = 3;
		$U_CANTIDAD_REPORTES = 3;

		$this->stmt_obtenerReporteCuadraDia->execute([
			$dia_semana,$id_cuadra]);
		$this->stmt_obtenerReporteCuadraHora->execute([
			$hora,$id_cuadra]);
		$this->stmt_obtenerReporteCuadraAhora->execute([
			$dia_semana,$hora,$id_cuadra]);
		$datos = [];
		$datos['dia'] = $this->stmt_obtenerReporteCuadraDia->fetch(PDO::FETCH_ASSOC);
		$datos['hora'] = $this->stmt_obtenerReporteCuadraHora->fetch(PDO::FETCH_ASSOC);
		$datos['ahora'] = $this->stmt_obtenerReporteCuadraAhora->fetch(PDO::FETCH_ASSOC);
		$datos['dia'] = ($datos['dia']['mediana_valor'] === null) ? ['mediana_valor' => 0, 'cantidad_reportes' => 0] : $datos['dia'];
		$datos['hora'] = ($datos['hora']['mediana_valor'] === null) ? ['mediana_valor' => 0, 'cantidad_reportes' => 0] : $datos['hora'];
		$datos['ahora'] = ($datos['ahora']['mediana_valor'] === null) ? ['mediana_valor' => 0, 'cantidad_reportes' => 0] : $datos['ahora'];

		$normalizado = ['dia' => [], 'hora' => [], 'ahora' => []];
		foreach (['dia', 'hora', 'ahora'] as $v) {
			$normalizado[$v]['valor'] = Normalizar::exponente_dNegativo(
				x: $datos[$v]['mediana_valor'],
				k: $K_MAX_VALOR,
				u: $U_VALOR
			);
			$normalizado_cantidad_reportes = Normalizar::exponente_dPositivo(
				x: $datos[$v]['cantidad_reportes'],
				k: $K_CANTIDAD_REPORTES,
				u: $U_CANTIDAD_REPORTES
			);
			$normalizado[$v]['cantidad'] = ($normalizado_cantidad_reportes > 1) ? 1 : $normalizado_cantidad_reportes;
		}
		return [
			$PESO_DIA*$normalizado['dia']['valor'] +
			$PESO_HORA*$normalizado['hora']['valor'] +
			$PESO_AHORA*$normalizado['ahora']['valor'],

			$PESO_DIA*$normalizado['dia']['cantidad'] +
			$PESO_HORA*$normalizado['hora']['cantidad'] +
			$PESO_AHORA*$normalizado['ahora']['cantidad']
		];
	}

	private function normalizar_estacionarCuadra(int $id_cuadra) : array {
		$K_PERCENTIL = 1;
		$K_CANT_ESTACIONAMIENTOS = 30;
		$U_PERCENTIL = 3;
		$U_CANT_ESTACIONAMIENTOS = 1;

		$this->stmt_obtenerEstacionarCuadra->execute();
		$this->stmt_obtenerEstacionarCuadraId->execute([$id_cuadra]);
		$this->stmt_obtenerEstacionarCuadraCantidad->execute();
		$ratio_cuadra = $this->stmt_obtenerEstacionarCuadraId->fetch(PDO::FETCH_ASSOC)['ratio'];
		$cant_estacionamientos = $this->stmt_obtenerEstacionarCuadraCantidad->fetch(PDO::FETCH_ASSOC)['cantidad'] ?? 0;

		if((int)$cant_estacionamientos === 0)
			return [0, 0];

		$datos = [];
		while($fetch = $this->stmt_obtenerEstacionarCuadra->fetch(PDO::FETCH_ASSOC))
			$datos[] = $fetch;
		
		$ratios = [];
		foreach ($datos as $v)
			$ratios[] = $v['ratio'];
		$mediana_ratio = Normalizar::mediana($ratios);
		$percentil_cuadra = Normalizar::obtenerPercentil($ratios, $ratio_cuadra);

		$pntj_cuadra = Normalizar::exponente_dPositivo(
			x: $percentil_cuadra,
			k: $K_PERCENTIL,
			u: $U_PERCENTIL
		);
		$confianza_cuadra = Normalizar::exponente_dPositivo(
			x: $cant_estacionamientos,
			k: $K_CANT_ESTACIONAMIENTOS,
			u: $U_CANT_ESTACIONAMIENTOS
		);
		return [$pntj_cuadra, $confianza_cuadra];
	}

	public function __construct (
		private PDO $db,
	){
		if (!$db instanceof PDO) {
			throw new InvalidArgumentException("Instancia invalida de PDO.");
		}
		try {
			$this->stmt_obtenerConteoCuadra = $this->db->prepare('
				SELECT 
					co.promedio_vehiculos_carril, 
					co.cantidad_muestras,
					ST_Distance_Sphere(
						POINT(co.longitud,co.latitud),
						POINT(cu.longitud,cu.latitud)) as distancia
				FROM 
					Conteo as co, 
					Cuadra as cu 
				WHERE 
					co.hora = ? AND
					co.dia_semana = ? AND 
					cu.id = ? 
				HAVING
					distancia <= ?
				ORDER BY distancia ASC;
			');
			$this->stmt_obtenerReporteCuadraDia = $this->db->prepare('
				SELECT
					MAX(mediana_valor) as mediana_valor,	
					COUNT(*) as cantidad_reportes
				FROM
					(
					SELECT MEDIAN(valor) OVER () as mediana_valor
					FROM Reporte
					WHERE 
						WEEKDAY(fecha) = ? AND 
						id_cuadra = ?
					) as t;
			');
			$this->stmt_obtenerReporteCuadraHora = $this->db->prepare('
				SELECT
					MAX(mediana_valor) as mediana_valor,	
					COUNT(*) as cantidad_reportes
				FROM
					(
					SELECT MEDIAN(valor) OVER () as mediana_valor
					FROM Reporte
					WHERE 
						HOUR(hora) = ? AND 
						id_cuadra = ?
					) as t;
			');
			$this->stmt_obtenerReporteCuadraAhora = $this->db->prepare('
				SELECT
					MAX(mediana_valor) as mediana_valor,	
					COUNT(*) as cantidad_reportes
				FROM
					(
					SELECT MEDIAN(valor) OVER () as mediana_valor
					FROM Reporte
					WHERE 
						WEEKDAY(fecha) = ? AND 
						HOUR(hora) = ? AND 
						id_cuadra = ?
					) as t;
			');
			// Cuantos autos estacionaron por 100 metros de cuadra
			$this->stmt_obtenerEstacionarCuadra = $this->db->prepare('
				SELECT 
					e.id_cuadra,
					(COUNT(e.id_cuadra)*100)/c.largo_metros as ratio
				FROM 
					Estaciona_en as e
				INNER JOIN
					Cuadra AS c ON c.id = e.id_cuadra
				GROUP BY e.id_cuadra;
			');
			$this->stmt_obtenerEstacionarCuadraId = $this->db->prepare('
				SELECT 
					(COUNT(e.id_cuadra)*100)/c.largo_metros as ratio
				FROM 
					Estaciona_en as e
				INNER JOIN
					Cuadra AS c ON c.id = e.id_cuadra
				WHERE
					e.id_cuadra = ?;
			');
			$this->stmt_obtenerEstacionarCuadraCantidad = $this->db->prepare('
				SELECT 
					COUNT(*) as cantidad
				FROM 
					Estaciona_en;
			');
		} catch (PDOException $e) {
			throw new Exception("Error en PredictorEstacionamiento.prepare");
		}
	}
}
