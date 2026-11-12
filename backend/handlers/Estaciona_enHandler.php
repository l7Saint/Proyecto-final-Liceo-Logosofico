<?php
require_once __DIR__ . "/../models/Estacionamiento.php";

/**
 * Clase Estaciona_enHandler
 * Maneja las operaciones de base de datos para entidades Estaciona_en
 */
class Estaciona_enHandler {
	private $db;
	private $stmt_obtenerPorId;
	private $stmt_obtenerTodos;
	private $stmt_crearEstacionamiento;
	private $stmt_actualizarEstacionamiento;
	private $stmt_eliminarEstacionamiento;
	private $stmt_contarPorUsuario;
	private $stmt_obtenerCuadrasMasUsadas;

	/**
	 * Obtiene un estacionamiento por su ID
	 *
	 * @param int $id El ID del estacionamiento a buscar
	 * @return Estacionamiento|false
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerPorId(int $id): Estacionamiento|false{
		try {
			$this->stmt_obtenerPorId->execute([$id]);
			$fetch = $this->stmt_obtenerPorId->fetch(PDO::FETCH_ASSOC);
			return ($fetch === false) ? false : $this->fetchToEstacionamiento($fetch);
		} catch (PDOException $e) {
			error_log("Error en Estaciona_enHandler.obtenerPorId: " . $e);
			throw new Exception("Error en Estaciona_enHandler.obtenerPorId: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Obtiene todos los estacionamientos
	 *
	 * @return Estacionamiento[] (vacío si no hay filas)
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerTodos(): array{
		try {
			$this->stmt_obtenerTodos->execute();
			$out = [];
			while ($fetch = $this->stmt_obtenerTodos->fetch(PDO::FETCH_ASSOC)) {
				$out[] = $this->fetchToEstacionamiento($fetch);
			}
			return $out;
		} catch (PDOException $e) {
			error_log("Error en Estaciona_enHandler.obtenerTodos: " . $e);
			throw new Exception("Error en Estaciona_enHandler.obtenerTodos: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Crea un nuevo estacionamiento
	 *
	 * @param Estacionamiento $estacionamiento
	 * @return int|false
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function crearEstacionamiento(Estacionamiento $estacionamiento): int|false{
		try {
			$success = $this->stmt_crearEstacionamiento->execute([
				$estacionamiento->id_usuario,
				$estacionamiento->id_vehiculo,
				$estacionamiento->id_cuadra,
				$estacionamiento->fecha,
				$estacionamiento->hora_inicio,
				$estacionamiento->hora_fin
			]);
			return $success ? (int) $this->db->lastInsertId() : false;
		} catch (PDOException $e) {
			error_log("Error en Estaciona_enHandler.crearEstacionamiento: " . $e);
			throw new Exception("Error en Estaciona_enHandler.crearEstacionamiento: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Actualiza un estacionamiento existente
	 *
	 * @param int $id El ID del estacionamiento a modificar
	 * @param Estacionamiento $estacionamiento
	 * @return true|false
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function actualizarEstacionamiento(int $id, Estacionamiento $estacionamiento): bool{
		try {
			$success = $this->stmt_actualizarEstacionamiento->execute([
				$estacionamiento->id_usuario,
				$estacionamiento->id_vehiculo,
				$estacionamiento->id_cuadra,
				$estacionamiento->fecha,
				$estacionamiento->hora_inicio,
				$estacionamiento->hora_fin,
				$id
			]);
			return $success ? true : false;
		} catch (PDOException $e) {
			error_log("Error en Estaciona_enHandler.actualizarEstacionamiento: " . $e);
			throw new Exception("Error en Estaciona_enHandler.actualizarEstacionamiento: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Elimina un estacionamiento por su ID
	 *
	 * @param int $id El ID del estacionamiento a eliminar
	 * @return true|false
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function eliminarEstacionamiento(int $id): bool{
		try {
			$success = $this->stmt_eliminarEstacionamiento->execute([$id]);
			return $success ? true : false;
		} catch (PDOException $e) {
			error_log("Error en Estaciona_enHandler.eliminarEstacionamiento: " . $e);
			throw new Exception("Error en Estaciona_enHandler.eliminarEstacionamiento: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Cuenta los estacionamientos registrados por un usuario
	 *
	 * Devuelve los agregados de todo el historial del usuario: cantidad total,
	 * cantidad de cuadras distintas y cantidad de estacionamientos del mes en
	 * curso, mas las fechas del primero y del ultimo registro.
	 *
	 * @param int $id_usuario El ID del usuario a analizar
	 * @return array{total:int, cuadras_distintas:int, este_mes:int, primera_fecha:?string, ultima_fecha:?string}
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function contarPorUsuario(int $id_usuario): array{
		try {
			$this->stmt_contarPorUsuario->execute([$id_usuario]);
			$fetch = $this->stmt_contarPorUsuario->fetch(PDO::FETCH_ASSOC);
			if ($fetch === false) {
				return [
					'total'             => 0,
					'cuadras_distintas' => 0,
					'este_mes'          => 0,
					'primera_fecha'    => null,
					'ultima_fecha'     => null
				];
			}
			return [
				'total'             => (int) $fetch['total'],
				'cuadras_distintas' => (int) $fetch['cuadras_distintas'],
				'este_mes'          => (int) $fetch['este_mes'],
				'primera_fecha'     => $fetch['primera_fecha'],
				'ultima_fecha'      => $fetch['ultima_fecha']
			];
		} catch (PDOException $e) {
			error_log("Error en Estaciona_enHandler.contarPorUsuario: " . $e);
			throw new Exception("Error en Estaciona_enHandler.contarPorUsuario: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Obtiene las cuadras donde un usuario estaciono mas veces
	 *
	 * Agrupa por cuadra y ordena de mayor a menor cantidad de estacionamientos,
	 * desempatando por el registro mas reciente. El LIMIT se bindea como entero:
	 * PDO envia los parametros como string por defecto y `LIMIT '10'` es invalido.
	 *
	 * @param int $id_usuario El ID del usuario a analizar
	 * @param int $limite Cantidad maxima de cuadras a devolver
	 * @return array[] Cada fila: id_cuadra, calle, puerta_inicial, puerta_final, total, ultima_vez
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerCuadrasMasUsadas(int $id_usuario, int $limite = 10): array{
		try {
			$this->stmt_obtenerCuadrasMasUsadas->bindValue(1, $id_usuario, PDO::PARAM_INT);
			$this->stmt_obtenerCuadrasMasUsadas->bindValue(2, $limite, PDO::PARAM_INT);
			$this->stmt_obtenerCuadrasMasUsadas->execute();
			$out = [];
			while ($fetch = $this->stmt_obtenerCuadrasMasUsadas->fetch(PDO::FETCH_ASSOC)) {
				$out[] = [
					'id_cuadra'      => (int) $fetch['id_cuadra'],
					'calle'          => $fetch['calle'],
					'puerta_inicial' => $fetch['numero_puerta_inicial'] !== null ? (int) $fetch['numero_puerta_inicial'] : null,
					'puerta_final'   => $fetch['numero_puerta_final'] !== null ? (int) $fetch['numero_puerta_final'] : null,
					'total'          => (int) $fetch['total'],
					'ultima_vez'     => $fetch['ultima_vez']
				];
			}
			return $out;
		} catch (PDOException $e) {
			error_log("Error en Estaciona_enHandler.obtenerCuadrasMasUsadas: " . $e);
			throw new Exception("Error en Estaciona_enHandler.obtenerCuadrasMasUsadas: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Convierte un arreglo de consulta de base de datos a un objeto Estacionamiento
	 *
	 * @param array $fetch
	 * @return Estacionamiento
	 * @private
	 */
	private function fetchToEstacionamiento(array $fetch): Estacionamiento{
		return new Estacionamiento(
			$fetch['id'],
			$fetch['id_usuario'],
			$fetch['id_vehiculo'],
			$fetch['id_cuadra'],
			$fetch['fecha'],
			$fetch['hora_inicio'],
			$fetch['hora_fin']
		);
	}

	/**
	 * Constructor — prepara todas las sentencias SQL
	 *
	 * @param PDO $db
	 * @throws Exception Si falla la preparación
	 */
	public function __construct(PDO $db){
		$this->db = $db;
		try {
			$this->stmt_obtenerPorId             = $this->db->prepare('SELECT * FROM Estaciona_en WHERE id = ?;');
			$this->stmt_obtenerTodos             = $this->db->prepare('SELECT * FROM Estaciona_en;');
			$this->stmt_crearEstacionamiento     = $this->db->prepare('INSERT INTO Estaciona_en (id_usuario, id_vehiculo, id_cuadra, fecha, hora_inicio, hora_fin) VALUES (?,?,?,?,?,?);');
			$this->stmt_actualizarEstacionamiento= $this->db->prepare('UPDATE Estaciona_en SET id_usuario = ?, id_vehiculo = ?, id_cuadra = ?, fecha = ?, hora_inicio = ?, hora_fin = ? WHERE id = ?;');
			$this->stmt_eliminarEstacionamiento  = $this->db->prepare('DELETE FROM Estaciona_en WHERE id = ?;');
			// Agregados del historial del usuario. Sin GROUP BY, MySQL devuelve
			// siempre una fila (con ceros y NULLs si no hay registros).
			$this->stmt_contarPorUsuario = $this->db->prepare("
				SELECT
					COUNT(*) AS total,
					COUNT(DISTINCT id_cuadra) AS cuadras_distintas,
					COALESCE(SUM(fecha >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')), 0) AS este_mes,
					MIN(fecha) AS primera_fecha,
					MAX(fecha) AS ultima_fecha
				FROM
					Estaciona_en
				WHERE
					id_usuario = ?;
			");
			// Ranking de cuadras del usuario. El INNER JOIN es seguro porque la FK
			// Estaciona_en.id_cuadra -> Cuadra.id impide huérfanos.
			$this->stmt_obtenerCuadrasMasUsadas = $this->db->prepare('
				SELECT
					e.id_cuadra,
					c.calle,
					c.numero_puerta_inicial,
					c.numero_puerta_final,
					COUNT(*) AS total,
					MAX(e.fecha) AS ultima_vez
				FROM
					Estaciona_en AS e
					INNER JOIN Cuadra AS c ON c.id = e.id_cuadra
				WHERE
					e.id_usuario = ?
				GROUP BY
					e.id_cuadra, c.calle, c.numero_puerta_inicial, c.numero_puerta_final
				ORDER BY
					total DESC, ultima_vez DESC
				LIMIT ?;
			');
		} catch (PDOException $e) {
			throw new Exception("Error en Estaciona_enHandler.prepare");
		}
	}
}

