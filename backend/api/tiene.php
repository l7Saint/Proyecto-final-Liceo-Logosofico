<?php
//error handling ini
//ini_set('display_errors', 0);
//ini_set('display_startup_errors', 0);
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

// -- CORS -- ni idea que es
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

header("Access-Control-Allow-Origin: $origin");
header("Vary: Origin");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 86400");

// Respond to preflight and stop
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once 'api.php';
require_once '../handlers/TieneHandler.php';
require_once '../config/conexion.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

$tiehnd = new TieneHandler($conexion);

function asignar($method, $tiehnd) {
	$data = json_decode(file_get_contents('php://input'), true);
	$parametros = [
		'numero_hora',
		'dia_semana'
	];

	if($method != 'POST')
		sendBadMethod($allow = 'POST');
	if(!checkSession())
		sendUnauthorized();
	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.tiene.asignar: " . $error);
		sendBadRequest('Bad Request', $error);
	}

	session_start();
	$id_usuario = $_SESSION['user_id'];
	try {
		if($tiehnd->asignarHorario(
			$id_usuario,
			$data['numero_hora'],
			$data['dia_semana']
		)){
			http_response_code(200);
			echo json_encode([
				'success' => true
			]);
			exit;
		} else {
			error_log("Error inesperado en api.tiene.asignar");
			sendServerError();
		}
	} catch(Exception $e) {
		error_log("Error en api.tiene.asignar: " . $e);
		sendServerError();
	}
}

function desasignar($method, $tiehnd) {
	$data = json_decode(file_get_contents('php://input'), true);
	$parametros = [
		'numero_hora',
		'dia_semana'
	];

	if($method != 'POST')
		sendBadMethod($allow = 'POST');
	if(!checkSession())
		sendUnauthorized();
	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.tiene.desasignar: " . $error);
		sendBadRequest('Bad Request', $error);
	}

	session_start();
	$id_usuario = $_SESSION['user_id'];
	try {
		if($tiehnd->desasignarHorario(
			$id_usuario,
			$data['numero_hora'],
			$data['dia_semana']
		)){
			http_response_code(200);
			echo json_encode([
				'success' => true
			]);
			exit;
		} else {
			error_log("Error inesperado en api.tiene.desasignar.");
			sendServerError();
		}
	} catch(Exception $e) {
		error_log("Error en api.tiene.desasignar: " . $e);
		sendServerError();
	}
}

function obtener($method, $tiehnd) {
	$data = json_decode(file_get_contents('php://input'), true);

	if($method != 'GET')
		sendBadMethod($allow = 'GET');
	if(!checkSession())
		sendUnauthorized();

	session_start();
	$id_usuario = $_SESSION['user_id'];
	try {
		$horarios = $tiehnd->obtenerPorIDUsuario($id_usuario);
		http_response_code(200);
		echo json_encode([
			'success' => true,
			'horarios' => $horarios
		]);
		exit;
	} catch(Exception $e) {
		error_log("Error en api.tiene.obtener: " . $e);
		sendServerError();
	}
}

$endpoint = $request[0] ?? '';
switch($endpoint){
	case 'asignar':
		error_log("Call a api.tiene.asignar");
		asignar($method, $tiehnd);	
		break;
	case 'desasignar':
		error_log("Call a api.tiene.desasignar");
		desasignar($method, $tiehnd);	
		break;
	case 'obtener':
		error_log("Call a api.tiene.obtener");
		obtener($method, $tiehnd);	
		break;
	default:
		error_log("Endpoint inexistente en api.tiene: " . $request[0]);
		sendBadRequest();
}
