<?php
require_once __DIR__ . "/../models/Vehiculo.php";

/**
 * Clase VehiculoHandler
 * Maneja las operaciones de base de datos para entidades Vehiculo
 */
class VehiculoHandler {
	private $db;
	private $stmt_obtenerPorIdVehiculo;
	private $stmt_obtenerPorIdUsuario;
	private $stmt_obtenerTodos;
	private $stmt_crearVehiculo;
	private $stmt_actualizarVehiculo;
	private $stmt_eliminarVehiculo;

	/**
	 * Obtiene un Vehiculo por su ID
	 *
	 * @param int $id El ID del Vehiculo a buscar
	 * @return Vehiculo|false
	 * @throws Exception Si ocurre un error en la base de datos
	 * @throws InvalidArgumentException Si $id no es de tipo int
	 */
	public function obtenerPorIdVehiculo($id){
		if (!is_int($id)) {
			throw new InvalidArgumentException("El parámetro \$id debe ser de tipo int.");
		}
		try {
			$this->stmt_obtenerPorIdVehiculo->execute([$id]);
			$fetch = $this->stmt_obtenerPorIdVehiculo->fetch(PDO::FETCH_ASSOC);
			return ($fetch === false) ? false : $this->fetchToVehiculo($fetch);
		} catch (PDOException $e) {
			error_log("Error en VehiculoHandler.obtenerPorIdVehiculo: " . $e);
			throw new Exception("Error en VehiculoHandler.obtenerPorIdVehiculo: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Obtiene todos los Vehiculo que pertenecen a un Usuario
	 *
	 * @param int $id_usuario El ID del Usuario propietario
	 * @return Vehiculo[] (vacío si no hay filas)
	 * @throws Exception Si ocurre un error en la base de datos
	 * @throws InvalidArgumentException Si $id_usuario no es de tipo int
	 */
	public function obtenerPorIdUsuario($id_usuario){
		if (!is_int($id_usuario)) {
			throw new InvalidArgumentException("El parámetro \$id_usuario debe ser de tipo int.");
		}
		try {
			$this->stmt_obtenerPorIdUsuario->execute([$id_usuario]);
			$out = [];
			while ($fetch = $this->stmt_obtenerPorIdUsuario->fetch(PDO::FETCH_ASSOC)) {
				$out[] = $this->fetchToVehiculo($fetch);
			}
			return $out;
		} catch (PDOException $e) {
			error_log("Error en VehiculoHandler.obtenerPorIdUsuario: " . $e);
			throw new Exception("Error en VehiculoHandler.obtenerPorIdUsuario: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Obtiene todos los Vehiculo registrados
	 *
	 * @return Vehiculo[] (vacío si no hay filas)
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerTodos(){
		try {
			$this->stmt_obtenerTodos->execute();
			$out = [];
			while ($fetch = $this->stmt_obtenerTodos->fetch(PDO::FETCH_ASSOC)) {
				$out[] = $this->fetchToVehiculo($fetch);
			}
			return $out;
		} catch (PDOException $e) {
			error_log("Error en VehiculoHandler.obtenerTodos: " . $e);
			throw new Exception("Error en VehiculoHandler.obtenerTodos: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Crea un nuevo Vehiculo
	 *
	 * @param Vehiculo $vehiculo El Vehiculo con los datos a insertar
	 * @return int|false El ID del Vehiculo creado, o false si falla
	 * @throws Exception Si ocurre un error en la base de datos
	 * @throws InvalidArgumentException Si $vehiculo no es instancia de Vehiculo
	 */
	public function crearVehiculo($vehiculo){
		if (!$vehiculo instanceof Vehiculo) {
			throw new InvalidArgumentException("El parámetro \$vehiculo debe ser una instancia de Vehiculo.");
		}
		try {
			$success = $this->stmt_crearVehiculo->execute([
				$vehiculo->id_usuario,
				$vehiculo->marca,
				$vehiculo->modelo,
				$vehiculo->color,
				$vehiculo->tamano
			]);
			return $success ? (int) $this->db->lastInsertId() : false;
		} catch (PDOException $e) {
			error_log("Error en VehiculoHandler.crearVehiculo: " . $e);
			throw new Exception("Error en VehiculoHandler.crearVehiculo: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Modifica los datos de un Vehiculo existente
	 *
	 * @param int $id El ID del Vehiculo a modificar
	 * @param Vehiculo $vehiculo El Vehiculo con los datos actualizados
	 * @return true|false
	 * @throws Exception Si ocurre un error en la base de datos
	 * @throws InvalidArgumentException Si $id no es de tipo int o $vehiculo no es instancia de Vehiculo
	 */
	public function modificarVehiculo($id, $vehiculo){
		if (!is_int($id)) {
			throw new InvalidArgumentException("El parámetro \$id debe ser de tipo int.");
		}
		if (!$vehiculo instanceof Vehiculo) {
			throw new InvalidArgumentException("El parámetro \$vehiculo debe ser una instancia de Vehiculo.");
		}
		try {
			$success = $this->stmt_actualizarVehiculo->execute([
				$vehiculo->id_usuario,
				$vehiculo->marca,
				$vehiculo->modelo,
				$vehiculo->color,
				$vehiculo->tamano,
				$id
			]);
			return $success ? true : false;
		} catch (PDOException $e) {
			error_log("Error en VehiculoHandler.modificarVehiculo: " . $e);
			throw new Exception("Error en VehiculoHandler.modificarVehiculo: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Elimina un Vehiculo por su ID
	 *
	 * @param int $id El ID del Vehiculo a eliminar
	 * @return true|false
	 * @throws Exception Si ocurre un error en la base de datos
	 * @throws InvalidArgumentException Si $id no es de tipo int
	 */
	public function eliminarVehiculo($id){
		if (!is_int($id)) {
			throw new InvalidArgumentException("El parámetro \$id debe ser de tipo int.");
		}
		try {
			$success = $this->stmt_eliminarVehiculo->execute([$id]);
			return $success ? true : false;
		} catch (PDOException $e) {
			error_log("Error en VehiculoHandler.eliminarVehiculo: " . $e);
			throw new Exception("Error en VehiculoHandler.eliminarVehiculo: " . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Convierte un arreglo de consulta de base de datos a un objeto Vehiculo
	 *
	 * @param array $fetch
	 * @return Vehiculo
	 * @private
	 */
	private function fetchToVehiculo($fetch){
		return new Vehiculo(
			$fetch['id'],
			$fetch['id_usuario'],
			$fetch['marca'],
			$fetch['modelo'],
			$fetch['color'],
			$fetch['tamano']
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
			$this->stmt_obtenerPorIdVehiculo = $this->db->prepare('SELECT * FROM Vehiculo WHERE id = ?;');
			$this->stmt_obtenerPorIdUsuario  = $this->db->prepare('SELECT * FROM Vehiculo WHERE id_usuario = ?;');
			$this->stmt_obtenerTodos         = $this->db->prepare('SELECT * FROM Vehiculo;');
			$this->stmt_crearVehiculo        = $this->db->prepare('INSERT INTO Vehiculo (id_usuario, marca, modelo, color, tamano) VALUES (?,?,?,?,?);');
			$this->stmt_actualizarVehiculo   = $this->db->prepare('UPDATE Vehiculo SET id_usuario = ?, marca = ?, modelo = ?, color = ?, tamano = ? WHERE id = ?;');
			$this->stmt_eliminarVehiculo     = $this->db->prepare('DELETE FROM Vehiculo WHERE id = ?;');
		} catch (PDOException $e) {
			throw new Exception("Error en VehiculoHandler.prepare");
		}
	}
}
