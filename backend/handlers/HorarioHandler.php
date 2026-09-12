<?php
require_once "../models/Horario.php";

/**
 * Clase HorarioHandler
 * Maneja las operaciones de base de datos para entidades Horario
 */
class HorarioHandler {
	private $db;
	private $stmt_obtenerTodos;
	private $stmt_obtenerPorNumeroHora;
	private $stmt_crearHorario;
	private $stmt_actualizarHorario;
	private $stmt_eliminarHorario;

	/**
	 * Obtiene todos los horarios
	 *
	 * @return Horario[] Un arreglo de objetos Horario
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerTodos(){
		try {
			$this->stmt_obtenerTodos->execute();
			$rows = $this->stmt_obtenerTodos->fetchAll(PDO::FETCH_ASSOC);
			$horarios = [];
			foreach ($rows as $fetch) {
				$horarios[] = $this->fetchToHorario($fetch);
			}
			return $horarios;
		} catch(PDOException $e) {
			error_log("Error en HorarioHandler.obtenerTodos: " . $e);
			throw new Exception("Error en HorarioHandler.obtenerTodos.");
		}
	}

	/**
	 * Obtiene un horario por su numero_hora
	 *
	 * @param int $numero_hora El numero_hora del horario a buscar
	 * @return Horario|false Retorna un objeto Horario si se encuentra, false si no
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerPorNumeroHora($numero_hora){
		try {
			$this->stmt_obtenerPorNumeroHora->execute([
				$numero_hora
			]);
			$fetch = $this->stmt_obtenerPorNumeroHora->fetch(PDO::FETCH_ASSOC);
			return ($fetch === false) ? false : $this->fetchToHorario($fetch);
		} catch(PDOException $e) {
			error_log("Error en HorarioHandler.obtenerPorNumeroHora: " . $e);
			throw new Exception("Error en HorarioHandler.obtenerPorNumeroHora.");
		}
	}

	/**
	 * Crea un nuevo horario en la base de datos
	 *
	 * @param Horario $horario Un objeto Horario con los datos a insertar
	 * @return int|false Retorna el numero_hora del horario creado, false en caso de fallo
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function crearHorario($horario){
		try {
			$success = $this->stmt_crearHorario->execute([
				$horario->numero_hora,
				$horario->dia_semana,
				$horario->horario,
			]);
			if($success){
				return $horario->numero_hora;
			} else {
				return false;
			}
		} catch(PDOException $e) {
			error_log("Error en HorarioHandler.crearHorario: " . $e);
			throw new Exception("Error en HorarioHandler.crearHorario.");
		}
	}

	/**
	 * Actualiza un horario existente en la base de datos
	 *
	 * @param Horario $horario Un objeto Horario con los datos actualizados
	 * @return true|false true en caso de actualizacion exitosa, false en caso de fallo
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function actualizarHorario($horario){
		try {
			$success = $this->stmt_actualizarHorario->execute([
				$horario->dia_semana,
				$horario->horario,
				$horario->numero_hora,
			]);
			if($success){
				return true;
			} else {
				return false;
			}
		} catch(PDOException $e) {
			error_log("Error en HorarioHandler.actualizarHorario: " . $e);
			throw new Exception("Error en HorarioHandler.actualizarHorario.");
		}
	}

	/**
	 * Elimina un horario por su numero_hora
	 *
	 * @param int $numero_hora El numero_hora del horario a eliminar
	 * @return true|false true en caso de eliminacion exitosa, false en caso de fallo
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function eliminarHorario($numero_hora){
		try {
			$success = $this->stmt_eliminarHorario->execute([
				$numero_hora
			]);
			if($success){
				return true;
			} else {
				return false;
			}
		} catch(PDOException $e) {
			error_log("Error en HorarioHandler.eliminarHorario: " . $e);
			throw new Exception("Error en HorarioHandler.eliminarHorario.");
		}
	}

	/**
	 * Convierte un arreglo de consulta de base de datos a un objeto Horario
	 *
	 * @param array $fetch Arreglo asociativo con los datos del horario
	 * @return Horario Un nuevo objeto Horario poblado con los datos del arreglo
	 * @private Metodo auxiliar usado internamente por la clase
	 */
	private function fetchToHorario($fetch){
		return new Horario(
			$fetch['numero_hora'],
			$fetch['dia_semana'],
			$fetch['horario']
		);
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
			$this->stmt_obtenerTodos = $this->db->prepare("SELECT * FROM Horario ORDER BY numero_hora;");
			$this->stmt_obtenerPorNumeroHora = $this->db->prepare("SELECT * FROM Horario WHERE numero_hora = ?;");
			$this->stmt_crearHorario = $this->db->prepare("INSERT INTO Horario (numero_hora, dia_semana, horario) VALUES (?,?,?);");
			$this->stmt_actualizarHorario = $this->db->prepare("UPDATE Horario SET dia_semana = ?, horario = ? WHERE numero_hora = ?;");
			$this->stmt_eliminarHorario = $this->db->prepare("DELETE FROM Horario WHERE numero_hora = ?;");
		} catch (PDOException $e){
			throw new Exception("Error en HorarioHandler.prepare");
		}
	}
}
