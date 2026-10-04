<?php
require_once "../models/Sesion.php";

/**
 * Clase SesionHandler
 * Maneja las operaciones de base de datos para sesiones de login
 */
class SesionHandler {
	private $db;
	private $stmt_crearSesion;
	private $stmt_obtenerPorToken;
	private $stmt_obtenerPorUsuario;
	private $stmt_eliminarSesion;
	private $stmt_eliminarSesionesPorUsuario;

	/**
	 * Crea una nueva sesión para un usuario, generando un token seguro
	 *
	 * @param int $id_usuario El ID del usuario para el cual se crea la sesión
	 * @return string|false Retorna el token generado en caso de éxito, false en caso de fallo
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function crearSesion($id_usuario){
		try {
			$token = $this->generarToken();
			$success = $this->stmt_crearSesion->execute([
				$token,
				$id_usuario
			]);
			if($success){
				return $token;
			} else {
				return false;
			}
		} catch(PDOException $e) {
			error_log("Error en SesionHandler.crearSesion: " . $e);
			throw new Exception("Error en SesionHandler.crearSesion.");
		}
	}

	/**
	 * Obtiene una sesión por su token
	 *
	 * @param string $token El token de la sesión a buscar
	 * @return Sesion|false Retorna un objeto Sesion si se encuentra, false si no se encuentra
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerPorToken($token){
		try {
			$this->stmt_obtenerPorToken->execute([$token]);
			$fetch = $this->stmt_obtenerPorToken->fetch(PDO::FETCH_ASSOC);
			return ($fetch === false) ? false : $this->fetchToSesion($fetch);
		} catch(PDOException $e) {
			error_log("Error en SesionHandler.obtenerPorToken: " . $e);
			throw new Exception("Error en SesionHandler.obtenerPorToken.");
		}
	}

	/**
	 * Obtiene todas las sesiones activas de un usuario
	 *
	 * @param int $id_usuario El ID del usuario cuyas sesiones se desean obtener
	 * @return Sesion[] Retorna un arreglo de objetos Sesion (vacío si no hay sesiones)
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function obtenerPorUsuario($id_usuario){
		try {
			$this->stmt_obtenerPorUsuario->execute([$id_usuario]);
			$sesiones = [];
			while ($fetch = $this->stmt_obtenerPorUsuario->fetch(PDO::FETCH_ASSOC)) {
				$sesiones[] = $this->fetchToSesion($fetch);
			}
			return $sesiones;
		} catch(PDOException $e) {
			error_log("Error en SesionHandler.obtenerPorUsuario: " . $e);
			throw new Exception("Error en SesionHandler.obtenerPorUsuario.");
		}
	}

	/**
	 * Elimina una sesión específica (logout)
	 *
	 * @param string $token El token de la sesión a eliminar
	 * @return true|false true en caso de eliminación exitosa, false en caso de fallo
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function eliminarSesion($token){
		try {
			$success = $this->stmt_eliminarSesion->execute([$token]);
			if($success){
				return true;
			} else {
				return false;
			}
		} catch(PDOException $e) {
			error_log("Error en SesionHandler.eliminarSesion: " . $e);
			throw new Exception("Error en SesionHandler.eliminarSesion.");
		}
	}

	/**
	 * Elimina todas las sesiones activas de un usuario
	 * (útil para "cerrar sesión en todos los dispositivos" o al desactivar la cuenta)
	 *
	 * @param int $id_usuario El ID del usuario cuyas sesiones se eliminarán
	 * @return int|false Retorna el número de sesiones eliminadas, false en caso de fallo
	 * @throws Exception Si ocurre un error en la base de datos
	 */
	public function eliminarSesionesPorUsuario($id_usuario){
		try {
			$success = $this->stmt_eliminarSesionesPorUsuario->execute([$id_usuario]);
			if($success){
				return $this->stmt_eliminarSesionesPorUsuario->rowCount();
			} else {
				return false;
			}
		} catch(PDOException $e) {
			error_log("Error en SesionHandler.eliminarSesionesPorUsuario: " . $e);
			throw new Exception("Error en SesionHandler.eliminarSesionesPorUsuario.");
		}
	}

	/**
	 * Genera un token criptográficamente seguro (64 caracteres hexadecimales)
	 *
	 * @return string Un token único y seguro para usar como clave de sesión
	 * @private Este es un método auxiliar usado internamente por la clase
	 */
	private function generarToken(){
		return bin2hex(random_bytes(32));
	}

	/**
	 * Convierte un arreglo de consulta de base de datos a un objeto Sesion
	 *
	 * @param array $fetch Un arreglo asociativo que contiene los datos de la sesión
	 * @return Sesion Retorna un nuevo objeto Sesion poblado con los datos del arreglo
	 * @private Este es un método auxiliar usado internamente por la clase
	 */
	private function fetchToSesion($fetch){
		$s = new Sesion(
			$fetch['token'],
			$fetch['id_usuario'],
			$fetch['fecha_creacion']
		);
		return $s;
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
			$this->stmt_crearSesion = $this->db->prepare("INSERT INTO Sesion (token, id_usuario) VALUES (?,?);");
			$this->stmt_obtenerPorToken = $this->db->prepare("SELECT * FROM Sesion WHERE token = ?;");
			$this->stmt_obtenerPorUsuario = $this->db->prepare("SELECT * FROM Sesion WHERE id_usuario = ?;");
			$this->stmt_eliminarSesion = $this->db->prepare("DELETE FROM Sesion WHERE token = ?;");
			$this->stmt_eliminarSesionesPorUsuario = $this->db->prepare("DELETE FROM Sesion WHERE id_usuario = ?;");
		} catch (PDOException $e){
			throw new Exception("Error en SesionHandler.prepare");
		}
	}
}
