<?php
require_once 'api.php';
require_once '../handlers/UsuarioHandler.php';
require_once '../config/conexion.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

$usrhnd = new UsuarioHandler($conexion);

function crear($method, $usrhnd){
	$data = json_decode(file_get_contents('php://input'), true);
	$parametros = [
		'nombre',
		'apellido',
		'email',
		'contrasena'
	];

	if($method != 'POST'){
		error_log("Bad Method en api.admin.crear: " . $method);
		sendBadMethod('POST');
	}
	if(!verificarAdministrador($usrhnd)){
		error_log("Unauthorized en api.admin.crear: ip: " . $_SERVER['REMOTE_ADDR']);
		sendUnauthorized('Unauthorized');
	}
	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.admin.crear: " . $error);
		sendBadRequest('Bad Request', $error);
	}

	try {
		//verificar que el email no este registrado
		$existingUser = $usrhnd->obtenerPorEmail($data['email']);
		if($existingUser !== false){
			http_response_code(409);
			echo json_encode([
				'success' => false,
				'error' => 'Email already registered'
			]);
			exit;
		}

		$usuario = new Usuario(
			null, //$id
			$data['nombre'], //$nombre
			$data['apellido'], //$apellido
			$data['email'], //$email
			password_hash($data['contrasena'], PASSWORD_DEFAULT), //$contrasena_hash
			$data['inactivo'], //$es_admin
			$data['es_admin'], //$inactivo
			null //$fecha_registro
		);

		$newId = $usrhnd->crearUsuario($usuario);
		if($newId){
			//crearUsuario no persiste es_admin ni inactivo, se aplican con modificarUsuario
			if($es_admin || $inactivo){
				$usuario->id = $newId;
				$usrhnd->modificarUsuario($newId, $usuario);
			}
			http_response_code(200);
			echo json_encode([
				'success' => true,
				'id' => $newId
			]);
			error_log("Admin creo nuevo usuario, email: ".$data['email']);
			exit;
		} else {
			error_log("Error en api.admin.crear: creacion de usuario fallida");
			sendServerError();
		}
	} catch(Exception $e){
		error_log("Error en api.admin.crear: " . $e);
		sendServerError();
	}
}

function obtener($method, $usrhnd){
	if($method != 'GET'){
		error_log("Bad Method en api.admin.obtener: " . $method);
		sendBadMethod('GET');
	}
	if(!verificarAdministrador($usrhnd)){
		error_log("Unauthorized en api.admin.obtener. ip: " . $_SERVER['REMOTE_ADDR']);
		sendUnauthorized('Unauthorized');
	}

	try {
		$usuarios = $usrhnd->obtenerTodos();
		$resultado = [];
		foreach($usuarios as $u){
			$resultado[] = [
				'id' => $u->id,
				'nombre' => $u->nombre,
				'apellido' => $u->apellido,
				'email' => $u->email,
				'inactivo' => $u->inactivo,
				'es_admin' => $u->es_admin,
				'fecha_registro' => $u->fecha_registro
			];
		}
		http_response_code(200);
		echo json_encode([
			'success' => true,
			'usuarios' => $resultado
		]);
		exit;
	} catch(Exception $e){
		error_log("Error en api.admin.obtener: " . $e);
		sendServerError();
	}
}

function modificar($method, $usrhnd){
	$parametros = [
		'id'
	];
	$data = json_decode(file_get_contents('php://input'), true);

	if($method != 'PUT'){
		error_log("Bad Method en api.admin.modificar: " . $method);
		sendBadMethod('PUT');
	}
	if(!verificarAdministrador($usrhnd)){
		error_log("Unauthorized en api.admin.modificar: ip: " . $_SERVER['REMOTE_ADDR']);
		sendUnauthorized('Unauthorized');
	}
	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.admin.modificar: " . $error);
		sendBadRequest('Bad Request', $error);
	}

	try {
		$usuario = $usrhnd->obtenerPorId($data['id']);
		if($usuario === false){
			http_response_code(404);
			echo json_encode([
				'success' => false,
				'error' => 'User not found'
			]);
			exit;
		}

		//si el email cambia, verificar que no este en uso por otro usuario
		if(isset($data['email']) && $data['email'] !== $usuario->email){
			$existingUser = $usrhnd->obtenerPorEmail($data['email']);
			if($existingUser !== false && $existingUser->id != $data['id']){
				http_response_code(409);
				echo json_encode([
					'success' => false,
					'error' => 'Email already registered'
				]);
				exit;
			}
		}

		//aplicar unicamente los parametros presentes en el body de la request
		if(isset($data['nombre'])) $usuario->nombre = $data['nombre'];
		if(isset($data['apellido'])) $usuario->apellido = $data['apellido'];
		if(isset($data['email'])) $usuario->email = $data['email'];
		if(isset($data['contrasena'])) $usuario->contrasena_hash = password_hash($data['contrasena'], PASSWORD_DEFAULT);
		if(isset($data['inactivo'])) $usuario->inactivo = (bool)$data['inactivo'];
		if(isset($data['es_admin'])) $usuario->es_admin = (bool)$data['es_admin'];

		if($usrhnd->modificarUsuario($data['id'], $usuario)){
			http_response_code(200);
			echo json_encode([
				'success' => true
			]);
			exit;
		} else {
			error_log("Error en api.admin.modificar: actualizacion fallida");
			sendServerError();
		}
	} catch(Exception $e){
		error_log("Error en api.admin.modificar: " . $e);
		sendServerError();
	}
}

function eliminar($method, $usrhnd){
	$data = json_decode(file_get_contents('php://input'), true);
	$parametros = [
		'id'
	];

	if($method != 'DELETE'){
		error_log("Bad Method en api.admin.eliminar: " . $method);
		sendBadMethod('DELETE');
	}
	if(!verificarAdministrador($usrhnd)){
		error_log("Unauthorized en api.admin.eliminar. ip: " . $_SERVER['REMOTE_ADDR']);
		sendUnauthorized('Unauthorized');
	}
	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.admin.eliminar: " . $error);
		sendBadRequest('Bad Request', $error);
	}

	try {
		//no permitir que un admin se elimine a si mismo
		session_start();
		if($_SESSION['user_id'] == $data['id']){
			error_log("Bad Request en api.admin.eliminar: intento de auto-eliminacion id: " . $data['id']);
			sendBadRequest('Bad Request', 'Cannot delete your own account');
		}

		if($usrhnd->eliminarUsuario($data['id'])){
			http_response_code(200);
			echo json_encode([
				'success' => true
			]);
			error_log("Admin elimino usuario id: " . $data['id']);
			exit;
		} else {
			error_log("Error en api.admin.eliminar: eliminacion fallida");
			sendServerError();
		}
	} catch(Exception $e){
		error_log("Error en api.admin.eliminar: " . $e);
		sendServerError();
	}
}

$endpoint = $request[0] ?? '';
switch($endpoint){
	case 'crear':
		error_log("Call a api.admin.crear");
		crear($method, $usrhnd);
		break;
	case 'obtener':
		error_log("Call a api.admin.obtener");
		obtener($method, $usrhnd);
		break;
	case 'modificar':
		error_log("Call a api.admin.modificar");
		modificar($method, $usrhnd);
		break;
	case 'eliminar':
		error_log("Call a api.admin.eliminar");
		eliminar($method, $usrhnd);
		break;
	default:
		error_log("Endpoint inexistente en api.admin: " . $request);
		sendBadRequest();
}
