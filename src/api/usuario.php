<?php
//error handling ini
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

//session ini
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 3600); 
ini_set('session.cookie_path', '/');  
ini_set('session.save_path', '/tmp'); 
ini_set('session.cookie_domain', '');
session_name('PHPSESSID');            

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
			!password_verify($data['contrasena'], $usuario->contrasenaHash)
		){
			error_log("Unauthorized en api.usuario.login: email: " . $data['email']);
			sendUnauthorized('Invalid email or password');
		}

		startSession($usuario);
		http_response_code(200);
		echo json_encode([
		    'success' => true,
		    'message' => 'Login successful',
		    'usuario' => [
			'nombre' => $usuario->nombre,
			'apellido' => $usuario->apellido,
			'email' => $usuario->email,
			'fechaRegistro' => $usuario->fechaRegistro
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
		error_log("Unauthorized en api.usuario.user: ip: " . $_SERVER['REMOTE_ADDR'])
		http_response_code(401);
		echo json_encode([
		    'success' => false,
		    'message' => 'Session inactive',
		]);
		exit;
	}
	http_response_code(200);
	echo json_encode([
	    'success' => true,
	    'message' => 'Login successful',
	    'usuario' => [
		'nombre' => $usuario->nombre,
		'apellido' => $usuario->apellido,
		'email' => $usuario->email,
		'fechaRegistro' => $usuario->fechaRegistro
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
		    'message' => 'Session active',
		]);
	} else {
		http_response_code(401);
		echo json_encode([
		    'success' => false,
		    'message' => 'Unauthorized',
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
	    'message' => 'Logout successful'
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
			password_hash($data['contrasena'], PASSWORD_DEFAULT), //$contrasenaHash
			null //$fechaRegistro
		);	
		if($usrhnd->crearUsuario($usuario)){
			http_response_code(200);
			echo json_encode([
			    'success' => true,
			    'message' => 'Signin successful',
			]);
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
