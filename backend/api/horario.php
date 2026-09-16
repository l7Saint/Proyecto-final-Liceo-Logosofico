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

function verificarAdministrador($usrhnd){
	if(!checkSession())	
		return false;
	$id = $_SESSION['user_id'];
	$user = $usrhnd->obtenerPorId($id);
	if(!$user)
		return false;
	return $user->es_admin;
}

function create($method, $horhnd) {
	$data = json_decode(file_get_contents('php://input'), true);
	$parametros = [
		'numero_hora',
		'dia_semana',
		'horario'
	];

	if($method != 'POST')
		sendBadMethod($allow = 'POST');
	if(!$verificarAdministrador($usrhnd))
		sendUnauthorized();	
	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.horario.create: " . $error);
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
		error_log("Error en api.horario.create: " . $e);
		sendServerError();
	}
}


function read($method, $horhnd) {
	$data = json_decode(file_get_contents('php://input'), true);

	if($method != 'GET')
		sendBadMethod($allow = 'GET');
	if(!$verificarAdministrador($usrhnd))
		sendUnauthorized();	

	try {
		http_response_code(200);
		$payload = $horhnd->obtenerTodos();
		echo var_dump($payload);
		echo json_encode([
			'success' => true,
			'body' => json_encode($payload)
		]);
		exit;
	} catch(Exception $e) {
		error_log("Error en api.horario.read: " . $e);
		sendServerError();
	}
}

function delete($method, $horhnd) {
	$data = json_decode(file_get_contents('php://input'), true);
	$parametros = [
		'numero_hora',
		'dia_semana'
	];

	if($method != 'POST')
		sendBadMethod($allow = 'POST');
	if(!$verificarAdministrador($usrhnd))
		sendUnauthorized();	
	$error = checkParameters($data, $parametros);
	if($error){
		error_log("Bad Request en api.horario.delete: " . $error);
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
		error_log("Error en api.horario.delete: " . $e);
		sendServerError();
	}
}

$endpoint = $request[0] ?? '';
switch($endpoint){
	case 'create':
		error_log("Call a api.horario.create");
		create($method, $horhnd);	
		break;
	case 'read':
		error_log("Call a api.horario.read");
		read($method, $horhnd);	
		break;
////////case 'update':
////////	error_log("Call a api.horario.update");
////////	update($method, $horhnd);	
////////	break;
	case 'delete':
		error_log("Call a api.horario.delete");
		delete($method, $horhnd);
		break;
	default:
		error_log("Endpoint inexistente en api.usuario: " . $request);
		sendBadRequest();
}
