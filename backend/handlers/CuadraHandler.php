<?php
require_once __DIR__ . "/../models/Cuadra.php";

/**
 * Clase CuadraHandler
 * Maneja las operaciones de base de datos para entidades Cuadra
 */
class CuadraHandler {
	private $db;
	private $stmt_obtenerPorId;
	private $stmt_obtenerPorPuerta;
	private $stmt_obtenerTodos;
	private $stmt_crearCuadra;
	private $stmt_actualizarCuadra;
	private $stmt_eliminarCuadra;

	/**
	 * Obtiene una Cuadra por su ID
	 *
	 * @param int $id El ID de la Cuadra a buscar
	 * @return Cuadra|false
	 * @throws Exception
	 */
	public function obtenerPorId(int $id): Cuadra|false{
		try {
			$this->stmt_obtenerPorId->execute([$id]);
			$fetch = $this->stmt_obtenerPorId->fetch(PDO::FETCH_ASSOC);
			return ($fetch === false) ? false : $this->fetchToCuadra($fetch);
		} catch (PDOException $e) {
			error_log("Error en CuadraHandler.obtenerPorId: " . $e);
			throw new Exception("Error en CuadraHandler.obtenerPorId: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Obtiene una Cuadra en base a un numero de puerta y una calle
	 *
	 * @param int $num_puerta El numero de puerta
	 * @param string $calle El nombre de la calle
	 * @return Cuadra|false
	 * @throws Exception
	 */
	public function obtenerPorPuerta(string $calle, int $num_puerta): Cuadra|false{
		try {
			$this->stmt_obtenerPorPuerta->execute([$calle, $num_puerta]);
			$fetch = $this->stmt_obtenerPorPuerta->fetch(PDO::FETCH_ASSOC);
			return ($fetch === false) ? false : $this->fetchToCuadra($fetch);
		} catch (PDOException $e) {
			error_log("Error en CuadraHandler.obtenerPorPuerta: " . $e);
			throw new Exception("Error en CuadraHandler.obtenerPorPuerta: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Obtiene todas las Cuadras
	 *
	 * @return Cuadra[] (vacío si no hay filas)
	 * @throws Exception
	 */
	public function obtenerTodos(): array{
		try {
			$this->stmt_obtenerTodos->execute();
			$out = [];
			while ($fetch = $this->stmt_obtenerTodos->fetch(PDO::FETCH_ASSOC)) {
				$out[] = $this->fetchToCuadra($fetch);
			}
			return $out;
		} catch (PDOException $e) {
			error_log("Error en CuadraHandler.obtenerTodos: " . $e);
			throw new Exception("Error en CuadraHandler.obtenerTodos: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Crea una nueva Cuadra
	 *
	 * @param Cuadra $cuadra
	 * @return int|false
	 * @throws Exception
	 */
	public function crearCuadra(Cuadra $cuadra): int|false{
		try {
			$success = $this->stmt_crearCuadra->execute([
				$cuadra->numero_puerta_inicial,
				$cuadra->numero_puerta_final,
				$cuadra->calle,
				$cuadra->latitud,
				$cuadra->longitud,
			]);
			return $success ? (int) $this->db->lastInsertId() : false;
		} catch (PDOException $e) {
			error_log("Error en CuadraHandler.crearCuadra: " . $e);
			throw new Exception("Error en CuadraHandler.crearCuadra: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Actualiza una Cuadra existente
	 *
	 * @param int $id El ID de la Cuadra a actualizar
	 * @param Cuadra $cuadra
	 * @return true|false
	 * @throws Exception
	 */
	public function actualizarCuadra(int $id, Cuadra $cuadra): bool{
		try {
			$success = $this->stmt_actualizarCuadra->execute([
				$cuadra->numero_puerta_inicial,
				$cuadra->numero_puerta_final,
				$cuadra->calle,
				$cuadra->latitud,
				$cuadra->longitud,
				$id
			]);
			return $success ? true : false;
		} catch (PDOException $e) {
			error_log("Error en CuadraHandler.actualizarCuadra: " . $e);
			throw new Exception("Error en CuadraHandler.actualizarCuadra: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Elimina una Cuadra por su ID
	 *
	 * @param int $id El ID de la Cuadra a eliminar
	 * @return true|false
	 * @throws Exception
	 */
	public function eliminarCuadra(int $id): bool{
		try {
			$success = $this->stmt_eliminarCuadra->execute([$id]);
			return $success ? true : false;
		} catch (PDOException $e) {
			error_log("Error en CuadraHandler.eliminarCuadra: " . $e);
			throw new Exception("Error en CuadraHandler.eliminarCuadra: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Convierte un arreglo de consulta de base de datos a un objeto Cuadra
	 *
	 * @param array $fetch
	 * @return Cuadra
	 * @private
	 */
	private function fetchToCuadra(array $fetch): Cuadra{
		return new Cuadra(
			(int) $fetch['id'],
			$fetch['numero_puerta_inicial'] !== null ? (int) $fetch['numero_puerta_inicial'] : null,
			$fetch['numero_puerta_final'] !== null ? (int) $fetch['numero_puerta_final'] : null,
			$fetch['calle'],
			$fetch['latitud'] !== null ? (float) $fetch['latitud'] : null,
			$fetch['longitud'] !== null ? (float) $fetch['longitud'] : null
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
			$this->stmt_obtenerPorPuerta  = $this->db->prepare('SELECT * FROM Cuadra WHERE calle = ? AND ? BETWEEN numero_puerta_inicial AND numero_puerta_final LIMIT 1;');
			$this->stmt_obtenerPorId      = $this->db->prepare('SELECT * FROM Cuadra WHERE id = ?;');
			$this->stmt_obtenerTodos      = $this->db->prepare('SELECT * FROM Cuadra;');
			$this->stmt_crearCuadra       = $this->db->prepare('INSERT INTO Cuadra (numero_puerta_inicial, numero_puerta_final, calle, latitud, longitud) VALUES (?,?,?,?,?);');
			$this->stmt_actualizarCuadra  = $this->db->prepare('UPDATE Cuadra SET numero_puerta_inicial = ?, numero_puerta_final = ?, calle = ?, latitud = ?, longitud = ? WHERE id = ?;');
			$this->stmt_eliminarCuadra    = $this->db->prepare('DELETE FROM Cuadra WHERE id = ?;');
		} catch (PDOException $e) {
			throw new Exception("Error en CuadraHandler.prepare");
		}
	}
}
