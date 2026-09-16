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
require_once '../handlers/HorarioHandler.php';
require_once '../handlers/UsuarioHandler.php';
require_once '../config/conexion.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

$horhnd = new HorarioHandler($conexion);
$usrhnd = new UsuarioHandler($conexion);

function crear($method, $horhnd) {
	$data = json_decode(file_get_contents('php://input'), true);
	$parametros = [
		'numero_hora',
		'dia_semana',
		'horario'
	];

	if($method != 'POST')
		sendBadMethod($allow = 'POST');
	if(!verificarAdministrador($usrhnd))
		sendUnauthorized();	
	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.horario.crear: " . $error);
		sendBadRequest('Bad Request', $error);
	}

	$horario = new Horario(
		$data["numero_hora"],
		$data["dia_semana"],
		$data["horario"]
	);
	try {
		$horhnd->crearHorario($horario);
		http_response_code(200);
		echo json_encode([
		    'success' => true
		]);
		exit;
	} catch(Exception $e) {
		error_log("Error en api.horario.crear: " . $e);
		sendServerError();
	}
}


function obtener($method, $horhnd, $usrhnd) {
	$data = json_decode(file_get_contents('php://input'), true);

	if($method != 'GET')
		sendBadMethod($allow = 'GET');
	if(!verificarAdministrador($usrhnd))
		sendUnauthorized();	

	try {
		http_response_code(200);
		$payload = $horhnd->obtenerTodos();
		echo json_encode([
			'success' => true,
			'horarios' => $payload
		]);
		exit;
	} catch(Exception $e) {
		error_log("Error en api.horario.obtener: " . $e);
		sendServerError();
	}
}

function eliminar($method, $horhnd) {
	$data = json_decode(file_get_contents('php://input'), true);
	$parametros = [
		'numero_hora',
		'dia_semana'
	];

	if($method != 'DELETE')
		sendBadMethod($allow = 'DELETE');
	if(!verificarAdministrador($usrhnd))
		sendUnauthorized();	
	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.horario.eliminar: " . $error);
		sendBadRequest('Bad Request', $error);
	}

	try {
		$horhnd->elminarHorario($numero_hora, $dia_semana);
		http_response_code(200);
		echo json_encode([
		    'success' => true
		]);
		exit;
	} catch(Exception $e) {
		error_log("Error en api.horario.eliminar: " . $e);
		sendServerError();
	}
}

$endpoint = $request[0] ?? '';
switch($endpoint){
	case 'crear':
		error_log("Call a api.horario.crear");
		crear($method, $horhnd);	
		break;
	case 'obtener':
		error_log("Call a api.horario.obtener");
		obtener($method, $horhnd, $usrhnd);	
		break;
////////case 'update':
////////	error_log("Call a api.horario.update");
////////	update($method, $horhnd);	
////////	break;
	case 'eliminar':
		error_log("Call a api.horario.eliminar");
		eliminar($method, $horhnd);
		break;
	default:
		error_log("Endpoint inexistente en api.horario: " . $request[0]);
		sendBadRequest();
}
