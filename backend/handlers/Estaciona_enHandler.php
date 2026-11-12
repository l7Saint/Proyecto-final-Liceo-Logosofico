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

	/**
	 * Obtiene un estacionamiento por su ID
	 *
	 * @param int $id El ID del estacionamiento a buscar
	 * @return Estacionamiento|false
	 * @throws Exception Si ocurre un error en la base de datos
	 * @throws InvalidArgumentException Si $id no es int
	 */
	public function obtenerPorId($id): Estacionamiento|false{
		if (!is_int($id)) {
			throw new InvalidArgumentException("El parámetro \$id debe ser de tipo int.");
		}
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
	 * @throws InvalidArgumentException Si $estacionamiento no es instancia de Estacionamiento
	 */
	public function crearEstacionamiento($estacionamiento): int|false{
		if (!$estacionamiento instanceof Estacionamiento) {
			throw new InvalidArgumentException("El parámetro \$estacionamiento debe ser una instancia de Estacionamiento.");
		}
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
	 * @throws InvalidArgumentException Si $id no es int o $estacionamiento no es instancia de Estacionamiento
	 */
	public function actualizarEstacionamiento($id, $estacionamiento): bool{
		if (!is_int($id)) {
			throw new InvalidArgumentException("El parámetro \$id debe ser de tipo int.");
		}
		if (!$estacionamiento instanceof Estacionamiento) {
			throw new InvalidArgumentException("El parámetro \$estacionamiento debe ser una instancia de Estacionamiento.");
		}
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
	 * @throws InvalidArgumentException Si $id no es int
	 */
	public function eliminarEstacionamiento($id): bool{
		if (!is_int($id)) {
			throw new InvalidArgumentException("El parámetro \$id debe ser de tipo int.");
		}
		try {
			$success = $this->stmt_eliminarEstacionamiento->execute([$id]);
			return $success ? true : false;
		} catch (PDOException $e) {
			error_log("Error en Estaciona_enHandler.eliminarEstacionamiento: " . $e);
			throw new Exception("Error en Estaciona_enHandler.eliminarEstacionamiento: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Convierte un arreglo de consulta de base de datos a un objeto Estacionamiento
	 *
	 * @param array $fetch
	 * @return Estacionamiento
	 * @private
	 */
	private function fetchToEstacionamiento($fetch): Estacionamiento{
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
	 * @throws InvalidArgumentException Si $db no es PDO
	 * @throws Exception Si falla la preparación
	 */
	public function __construct($db){
		if (!$db instanceof PDO) {
			throw new InvalidArgumentException("Instancia invalida de PDO.");
		}
		$this->db = $db;
		try {
			$this->stmt_obtenerPorId             = $this->db->prepare('SELECT * FROM Estaciona_en WHERE id = ?;');
			$this->stmt_obtenerTodos             = $this->db->prepare('SELECT * FROM Estaciona_en;');
			$this->stmt_crearEstacionamiento     = $this->db->prepare('INSERT INTO Estaciona_en (id_usuario, id_vehiculo, id_cuadra, fecha, hora_inicio, hora_fin) VALUES (?,?,?,?,?,?);');
			$this->stmt_actualizarEstacionamiento= $this->db->prepare('UPDATE Estaciona_en SET id_usuario = ?, id_vehiculo = ?, id_cuadra = ?, fecha = ?, hora_inicio = ?, hora_fin = ? WHERE id = ?;');
			$this->stmt_eliminarEstacionamiento  = $this->db->prepare('DELETE FROM Estaciona_en WHERE id = ?;');
		} catch (PDOException $e) {
			throw new Exception("Error en Estaciona_enHandler.prepare");
		}
	}
}

