<?php
require_once "models/Usuario.php";

/**
 * Clase UsuarioHandler
 * Maneja las operaciones de base de datos para entidades Usuario
 */
class UsuarioHandler {
	private $db;
	private $stmt_obtenerPorId;
	private $stmt_crearUsuario;

	/**
	 * Obtiene un usuario por su ID
	 * 
	 * @param int $id El ID del usuario a buscar
	 * @return Usuario|false Retorna un objeto Usuario si se encuentra, false si no se encuentra
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerPorId($id){
		try {
			$this->stmt_obtenerPorId->execute([
				$id
			]);
			$fetch = $this->stmt_obtenerPorId->fetch(PDO::FETCH_ASSOC);
			return ($fetch === false) ? false : $this->fetchToUsuario($fetch);
		} catch(PDOException $e) {
			error_log("Error en obtenerPorId: " . $e);
			throw new Exception("Error en obtenerPorId.");
		}
	}

	/**
	 * Crea un nuevo usuario en la base de datos
	 * 
	 * @param Usuario $usuario Un objeto Usuario que contiene los datos del usuario a insertar
	 * @return int|false Retorna el ID del usuario recién creado en caso de éxito, false en caso de fallo
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function crearUsuario($usuario){
		try {
			$success = $this->stmt_crearUsuario->execute([
				$usuario->nombre,
				$usuario->apellido,
				$usuario->email,
				$usuario->contrasenaHash,
			]);
			if($success){
				return $this->db->lastInsertId();
			} else {
				return false
			};
		} catch(PDOException $e) {
			error_log("Error en crearUsuario: " . $e);
			throw new Exception("Error en crearUsuario.");
		}
	}

	/**
	 * Convierte un arreglo de consulta de base de datos a un objeto Usuario
	 * 
	 * @param array $fetch Un arreglo asociativo que contiene los datos del usuario desde la base de datos
	 * @return Usuario Retorna un nuevo objeto Usuario poblado con los datos del arreglo
	 * @private Este es un método auxiliar usado internamente por la clase
	 */
	private function fetchToUsuario($fetch){
		return new Usuario(
			$fetch['id'],
			$fetch['nombre'],
			$fetch['apellido'],
			$fetch['email'],
			$fetch['contrasenaHash'],
			$fetch['fechaRegistro']
		);
	}

	/**
	 * Constructor - Inicializa el manejador con una conexión a la base de datos y prepara las sentencias
	 * 
	 * @param PDO $db Un objeto PDO válido de conexión a la base de datos
	 * @throws InvalidArgumentException Si el $db proporcionado no es una instancia de PDO
	 * @throws Exception Si ocurre un error al preparar las sentencias SQL
	 */
	public function __construct($db){
		if (!$db instanceof PDO) {
			throw new InvalidArgumentException("Instancia invalida de PDO.");
		}
		$this->db = $db;
		try {
			$this->stmt_obtenerPorId = $this->db->prepare("SELECT * FROM Usuario WHERE id = ?;");
			$this->stmt_crearUsuario = $this->db->prepare("INSERT INTO Usuario (nombre, apellido, email, contrasenaHash) VALUES (?,?,?,?);");
		} catch (PDOException $e){
			throw new Exception("Error en prepare");
		}
	}
}
