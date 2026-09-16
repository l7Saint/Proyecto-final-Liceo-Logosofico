<?php

/**
 * Clase TieneHandler
 * Maneja las operaciones de base de datos para la relacion Tiene
 */
class TieneHandler {
	private $db;
	private $stmt_obtenerTodos;
	private $stmt_obtenerPorIDUsuario;
	private $stmt_obtenerPorIDU_DS;
	private $stmt_obtenerPorNumeroHora;
	private $stmt_obtenerPorDiaSemana;
	private $stmt_obtenerPorNH_DS;
	private $stmt_asignarHorario;
	private $stmt_desasignarHorario;

	/**
	 * Obtiene todas las relaciones Tiene
	 *
	 * @return array[] Un arreglo de arreglos asociativos con los datos de Tiene
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerTodos(){
		try {
			$this->stmt_obtenerTodos->execute();
			return $this->stmt_obtenerTodos->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			error_log("Error en TieneHandler.obtenerTodos: " . $e);
			throw new Exception("Error en TieneHandler.obtenerTodos.");
		}
	}

	/**
	 * Obtiene las relaciones Tiene por id_usuario
	 *
	 * @param int $id_usuario El id_usuario a buscar
	 * @return array[] Un arreglo de arreglos asociativos con los datos de Tiene
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerPorIDUsuario($id_usuario){
		try {
			$this->stmt_obtenerPorIDUsuario->execute([
				$id_usuario
			]);
			return $this->stmt_obtenerPorIDUsuario->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			error_log("Error en TieneHandler.obtenerPorIDUsuario: " . $e);
			throw new Exception("Error en TieneHandler.obtenerPorIDUsuario.");
		}
	}

	/**
	 * Obtiene las relaciones Tiene por id_usuario y dia_semana
	 *
	 * @param int $id_usuario El id_usuario a buscar
	 * @param string $dia_semana El dia_semana a buscar
	 * @return array[] Un arreglo de arreglos asociativos con los datos de Tiene
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerPorIDU_DS($id_usuario, $dia_semana){
		try {
			$this->stmt_obtenerPorIDU_DS->execute([
				$id_usuario,
				$dia_semana
			]);
			return $this->stmt_obtenerPorIDU_DS->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			error_log("Error en TieneHandler.obtenerPorIDU_DS: " . $e);
			throw new Exception("Error en TieneHandler.obtenerPorIDU_DS.");
		}
	}

	/**
	 * Obtiene las relaciones Tiene por numero_hora
	 *
	 * @param int $numero_hora El numero_hora a buscar
	 * @return array[] Un arreglo de arreglos asociativos con los datos de Tiene
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerPorNumeroHora($numero_hora){
		try {
			$this->stmt_obtenerPorNumeroHora->execute([
				$numero_hora
			]);
			return $this->stmt_obtenerPorNumeroHora->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			error_log("Error en TieneHandler.obtenerPorNumeroHora: " . $e);
			throw new Exception("Error en TieneHandler.obtenerPorNumeroHora.");
		}
	}

	/**
	 * Obtiene las relaciones Tiene por dia_semana
	 *
	 * @param string $dia_semana El dia_semana a buscar
	 * @return array[] Un arreglo de arreglos asociativos con los datos de Tiene
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerPorDiaSemana($dia_semana){
		try {
			$this->stmt_obtenerPorDiaSemana->execute([
				$dia_semana
			]);
			return $this->stmt_obtenerPorDiaSemana->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			error_log("Error en TieneHandler.obtenerPorDiaSemana: " . $e);
			throw new Exception("Error en TieneHandler.obtenerPorDiaSemana.");
		}
	}

	/**
	 * Obtiene las relaciones Tiene por numero_hora y dia_semana
	 *
	 * @param int $numero_hora El numero_hora a buscar
	 * @param string $dia_semana El dia_semana a buscar
	 * @return array[] Un arreglo de arreglos asociativos con los datos de Tiene
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerPorNH_DS($numero_hora, $dia_semana){
		try {
			$this->stmt_obtenerPorNH_DS->execute([
				$numero_hora,
				$dia_semana
			]);
			return $this->stmt_obtenerPorNH_DS->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			error_log("Error en TieneHandler.obtenerPorNH_DS: " . $e);
			throw new Exception("Error en TieneHandler.obtenerPorNH_DS.");
		}
	}

	/**
	 * Asigna un horario a un usuario creando una nueva relacion Tiene
	 *
	 * @param int $id_usuario El id_usuario a asignar
	 * @param int $numero_hora El numero_hora a asignar
	 * @param string $dia_semana El dia_semana a asignar
	 * @return true|false true en caso de insercion exitosa, false en caso de fallo
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function asignarHorario($id_usuario, $numero_hora, $dia_semana){
		try {
			$success = $this->stmt_asignarHorario->execute([
				$id_usuario,
				$numero_hora,
				$dia_semana,
			]);
			if($success){
				return true;
			} else {
				return false;
			}
		} catch(PDOException $e) {
			error_log("Error en TieneHandler.asignarHorario: " . $e);
			throw new Exception("Error en TieneHandler.asignarHorario.");
		}
	}

	/**
	 * Desasigna un horario de un usuario eliminando la relacion Tiene
	 *
	 * @param int $id_usuario El id_usuario de la relacion a eliminar
	 * @param int $numero_hora El numero_hora de la relacion a eliminar
	 * @param string $dia_semana El dia_semana de la relacion a eliminar
	 * @return true|false true en caso de eliminacion exitosa, false en caso de fallo
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function desasignarHorario($id_usuario, $numero_hora, $dia_semana){
		try {
			$success = $this->stmt_desasignarHorario->execute([
				$id_usuario,
				$numero_hora,
				$dia_semana,
			]);
			if($success){
				return true;
			} else {
				return false;
			}
		} catch(PDOException $e) {
			error_log("Error en TieneHandler.desasignarHorario: " . $e);
			throw new Exception("Error en TieneHandler.desasignarHorario.");
		}
	}

	/**
	 * Constructor - Inicializa el manejador con una conexion a la base de datos y prepara las sentencias
	 *
	 * @param PDO $db Un objeto PDO valido de conexion a la base de datos
	 * @throws InvalidArgumentException Si el $db proporcionado no es una instancia de PDO
	 * @throws Exception Si ocurre un error al preparar las sentencias SQL
	 */
	public function __construct($db){
		if (!$db instanceof PDO) {
			throw new InvalidArgumentException("Instancia invalida de PDO.");
		}
		$this->db = $db;
		try {
			$this->stmt_obtenerTodos = $this->db->prepare('SELECT * FROM Tiene;');
			$this->stmt_obtenerPorIDUsuario = $this->db->prepare('SELECT * FROM Tiene WHERE id_usuario = ?;');
			$this->stmt_obtenerPorIDU_DS = $this->db->prepare('SELECT * FROM Tiene WHERE id_usuario = ? AND dia_semana = ?;');
			$this->stmt_obtenerPorNumeroHora = $this->db->prepare('SELECT * FROM Tiene WHERE numero_hora = ?;');
			$this->stmt_obtenerPorDiaSemana = $this->db->prepare('SELECT * FROM Tiene WHERE dia_semana = ?;');
			$this->stmt_obtenerPorNH_DS = $this->db->prepare('SELECT * FROM Tiene WHERE numero_hora = ? AND dia_semana = ?;');
			$this->stmt_asignarHorario = $this->db->prepare('INSERT INTO Tiene (id_usuario, numero_hora, dia_semana) VALUES (?,?,?)');
			$this->stmt_desasignarHorario = $this->db->prepare('DELETE FROM Tiene WHERE id_usuario = ? AND numero_hora = ? AND dia_semana = ?;');
		} catch (PDOException $e){
			throw new Exception("Error en TieneHandler.prepare");
		}
	}
}
