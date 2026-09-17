<?php
require_once 'api.php';
require_once '../handlers/UsuarioHandler.php';
require_once '../config/conexion.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

$usrhnd = new UsuarioHandler($conexion);

function login($method, $usrhnd) {
	$data = json_decode(file_get_contents('php://input'), true);
	$parametros = [
		'email',
		'contrasena'
	];

	//verificaciones
	if($method != 'POST'){
		error_log("Bad Method en api.usuario.login: " . $method);
		sendBadMethod('POST');
	}

	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.usuario.login: " . $error);
		sendBadRequest('Bad Request', $error);
	}

	//logica del login
	try{
		$usuario = $usrhnd->obtenerPorEmail($data['email']);
		if(
			$usuario === false ||
			!password_verify($data['contrasena'], $usuario->contrasena_hash)
		){
			error_log("Unauthorized en api.usuario.login: email: " . $data['email']);
			sendUnauthorized('Invalid email or password');
		}

		startSession($usuario);
		http_response_code(200);
		echo json_encode([
		    'success' => true,
		    'usuario' => [
			'nombre' => $usuario->nombre,
			'apellido' => $usuario->apellido,
			'email' => $usuario->email,
			'fecha_registro' => $usuario->fecha_registro
		    ]
		]);
		exit;

	} catch (Exception $e) {
		error_log("Error en api.usuario.login: " . $e);
		sendServerError();
	}
}

function user($method, $usrhnd){
	if($method != 'GET'){
		error_log("Bad Method en api.usuario.user: " . $method);
		sendBadMethod('GET');
	}
	if(!checkSession()){
		//sendUnauthorized
		error_log("Unauthorized en api.usuario.user: ip: " . $_SERVER['REMOTE_ADDR']);
		http_response_code(401);
		echo json_encode([
		    'success' => false
		]);
		exit;
	}
	http_response_code(200);
	echo json_encode([
	    'success' => true,
	    'usuario' => [
		'nombre' => $usuario->nombre,
		'apellido' => $usuario->apellido,
		'email' => $usuario->email,
		'fecha_registro' => $usuario->fecha_registro
	    ]
	]);
}

function check($method){
	if($method != 'GET'){
		error_log("Bad Method en api.usuario.check: " . $method);
		sendBadMethod('GET');
	}
	if(checkSession()){
		http_response_code(200);
		echo json_encode([
		    'success' => true,
		]);
	} else {
		http_response_code(401);
		echo json_encode([
		    'success' => false,
		]);
	}
}

function logout($method){
	if($method != 'GET'){
		error_log("Bad Method en api.usuario.logout: " . $method);
		sendBadMethod('GET');
	}
	destroySession();	
	http_response_code(200);
	echo json_encode([
	    'success' => true,
	]);
}

function signin($method, $usrhnd) {
	$data = json_decode(file_get_contents('php://input'), true);
	$parametros = [
		'nombre',
		'apellido',
		'email',
		'contrasena'
	];

	//verificaciones
	if($method != 'POST'){
		error_log("Bad Method en api.usuario.signin: " . $method);
		sendBadMethod('POST');
	}

	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.usuario.signin: " . $error);
		sendBadRequest('Bad Request', $error);
	}

	$existingUser = $usrhnd->obtenerPorEmail($data['email']);
	if($existingUser !== false){
		http_response_code(409);
		echo json_encode([
			'success' => false,
			'error' => 'Email already registered'
		]);
		exit;
	}

	//logica del signin
	try{
		$usuario = new Usuario(
			null, //$id	
			$data['nombre'], //$nombre
			$data['apellido'], //$apellido
			$data['email'], //$email
			password_hash($data['contrasena'], PASSWORD_DEFAULT), //$contrasena_hash
			false, //$inactivo
			false, //$es_admin
			null //$fecha_registro
		);	
		if($usrhnd->crearUsuario($usuario)){
			http_response_code(200);
			echo json_encode([
			    'success' => true,
			]);
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
		login($method, $usrhnd);	
		break;
	case 'signin':
		error_log("Call a api.usuario.signin");
		signin($method, $usrhnd);	
		break;
	case 'logout':
		error_log("Call a api.usuario.logout");
		logout($method);	
		break;
	case 'check':
		error_log("Call a api.usuario.check");
		check($method);
		break;
	default:
		error_log("Endpoint inexistente en api.usuario: " . $request);
		sendBadRequest();
}
