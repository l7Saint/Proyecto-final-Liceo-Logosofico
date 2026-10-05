<?php
require_once 'api.php';
require_once '../handlers/UsuarioHandler.php';
require_once '../handlers/SesionHandler.php';
require_once '../config/conexion.php';
header('Content-Type: application/json');

$method  = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

$usrhnd = new UsuarioHandler($conexion);
$seshnd = new SesionHandler($conexion);

function login($method, $usrhnd, $seshnd) {
	$data = json_decode(file_get_contents('php://input'), true);
	$parametros = ['email', 'contrasena'];

	if($method != 'POST'){
		error_log("Bad Method en api.usuario.login: " . $method);
		sendBadMethod('POST');
	}

	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.usuario.login: " . $error);
		sendBadRequest('Bad Request', $error);
	}

	try{
		$usuario = $usrhnd->obtenerPorEmail($data['email']);
		if(
			$usuario === false ||
			!password_verify($data['contrasena'], $usuario->contrasena_hash)
		){
			error_log("Unauthorized en api.usuario.login: email: " . $data['email']);
			sendUnauthorized('Invalid email or password');
		}

		$token = startSession($usuario, $seshnd);
		http_response_code(200);
		echo json_encode([
			'success' => true,
			'token' => $token,
			'usuario' => [
				'nombre'         => $usuario->nombre,
				'apellido'       => $usuario->apellido,
				'email'          => $usuario->email,
				'es_admin'       => (int)$usuario->es_admin,
				'fecha_registro' => $usuario->fecha_registro
			]
		]);
		exit;

	} catch (Exception $e) {
		error_log("Error en api.usuario.login: " . $e);
		sendServerError();
	}
}

function user($method, $usrhnd, $seshnd){
	if($method != 'GET'){
		error_log("Bad Method en api.usuario.user: " . $method);
		sendBadMethod('GET');
	}
	$sesion = checkSession($seshnd);
	if($sesion === false){
		error_log("Unauthorized en api.usuario.user: ip: " . $_SERVER['REMOTE_ADDR']);
		sendUnauthorized('Unauthorized');
	}
	$usuario = $usrhnd->obtenerPorId($sesion->id_usuario);
	if($usuario === false){
		// Hay token válido pero el usuario ya no existe: limpiar y rechazar
		destroySession($seshnd);
		sendUnauthorized('Unauthorized');
	}
	http_response_code(200);
	echo json_encode([
		'success' => true,
		'usuario' => [
			'nombre'         => $usuario->nombre,
			'apellido'       => $usuario->apellido,
			'email'          => $usuario->email,
			'fecha_registro' => $usuario->fecha_registro
		]
	]);
	exit;
}

function check($method, $seshnd){
	if($method != 'GET'){
		error_log("Bad Method en api.usuario.check: " . $method);
		sendBadMethod('GET');
	}
	if(checkSession($seshnd) !== false){
		http_response_code(200);
		echo json_encode(['success' => true]);
	} else {
		http_response_code(401);
		echo json_encode(['success' => false]);
	}
	exit;
}

function logout($method, $seshnd){
	if($method != 'GET'){
		error_log("Bad Method en api.usuario.logout: " . $method);
		sendBadMethod('GET');
	}
	destroySession($seshnd);
	http_response_code(200);
	echo json_encode(['success' => true]);
	exit;
}

function signin($method, $usrhnd, $seshnd) {
	$data = json_decode(file_get_contents('php://input'), true);
	$parametros = ['nombre', 'apellido', 'email', 'contrasena'];

	if($method != 'POST'){
		error_log("Bad Method en api.usuario.signin: " . $method);
		sendBadMethod('POST');
	}

	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.usuario.signin: " . $error);
		sendBadRequest('Bad Request', $error);
	}

	try{
		$existingUser = $usrhnd->obtenerPorEmail($data['email']);
	} catch(Exception $e){
		error_log("Error en api.usuario.signin: " . $e);
		sendServerError();
	}
	if($existingUser !== false){
		http_response_code(409);
		echo json_encode([
			'success' => false,
			'error'   => 'Email already registered'
		]);
		exit;
	}

	try{
		$usuario = new Usuario(
			null,
			$data['nombre'],
			$data['apellido'],
			$data['email'],
			password_hash($data['contrasena'], PASSWORD_DEFAULT),
			false,
			false,
			null
		);
		$nuevo_id = $usrhnd->crearUsuario($usuario);
		if($nuevo_id !== false){
			$usuario->id = (int)$nuevo_id; // imprescindible para startSession
			startSession($usuario, $seshnd);
			http_response_code(200);
			echo json_encode(['success' => true]);
			error_log("Nuevo registro de usuario, email: ".$data['email']);
			exit;
		} else {
			error_log("Error en api.usuario.signin: creacion de usuario fallida");
			sendServerError();
		}
	}catch(Exception $e){
		error_log("Error en api.usuario.signin: " . $e);
		sendServerError();
	}
}

$endpoint = $request[0] ?? '';
switch($endpoint){
	case 'login':
		error_log("Call a api.usuario.login");
		login($method, $usrhnd, $seshnd);
		break;
	case 'signin':
		error_log("Call a api.usuario.signin");
		signin($method, $usrhnd, $seshnd);
		break;
	case 'logout':
		error_log("Call a api.usuario.logout");
		logout($method, $seshnd);
		break;
	case 'check':
		error_log("Call a api.usuario.check");
		check($method, $seshnd);
		break;
	case 'user':
		error_log("Call a api.usuario.user");
		user($method, $usrhnd, $seshnd);
		break;
	default:
		error_log("Endpoint inexistente en api.usuario: " . $request);
		sendBadRequest();
}
