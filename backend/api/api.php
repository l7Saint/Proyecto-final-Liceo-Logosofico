<?php
//error handling ini
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// -- CORS --
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

header("Access-Control-Allow-Origin: $origin");
header("Vary: Origin");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 86400");

// Respond to preflight and stop
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// -- Token-based session config --
define('SESSION_COOKIE_NAME', 'session_token');
define('SESSION_COOKIE_LIFETIME', 3600); // 1 hora

function verificarAdministrador($usrhnd, $seshnd){
	$sesion = checkSession($seshnd);
	if($sesion === false)
		return false;
	$user = $usrhnd->obtenerPorId($sesion->id_usuario);
	if(!$user)
		return false;
	return $user->es_admin;
}

function sendBadRequest($message = 'Bad Request', $errors = null) {
	http_response_code(400);
	$response = [
		'success' => false,
		'status' => 400,
		'error' => $message
	];
	if ($errors) {
		$response['details'] = $errors;
	}
	echo json_encode($response);
	exit;
}

function sendBadMethod($allow){
	http_response_code(405);
	header("Allow: $allow");
	exit;
}

function sendServerError(){
	http_response_code(500);
	echo json_encode([
		'success' => false,
		'error' => 'Internal server error'
	]);
	exit;
}

function sendUnauthorized($message = 'Unauthorized', $errors = null){
	http_response_code(401);
	$response = [
		'success' => false,
		'status' => 401,
		'error' => $message
	];
	if ($errors) {
		$response['details'] = $errors;
	}
	echo json_encode($response);
	exit;
}

function checkParameters($data, $parametros){
	$error = null;
	foreach ($parametros as $p){
		if(!isset($data[$p])){
			if (!isset($error))
				$error = '';
			$error = $error . "Missing parameter: $p ";
		}
	}
	return $error;
}

/**
 * Crea una sesión en la BD y envía la cookie con el token al cliente.
 * @return string el token generado
 */
function startSession($usuario, $seshnd){
	$token = $seshnd->crearSesion($usuario->id);
	if($token === false){
		error_log("api.startSession: no se pudo crear la sesión para user_id ".$usuario->id);
		throw new Exception("No se pudo iniciar la sesión.");
	}
	error_log("Session Started for user_id: ".$usuario->id." ; ip address: ".$_SERVER['REMOTE_ADDR']);
	return $token;
}

/**
 * Elimina la sesión de la BD y borra la cookie.
 */
function destroySession($seshnd){
	$header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
	if(preg_match('/Bearer\s+(\S+)/', $header, $m)){
		try { $seshnd->eliminarSesion($m[1]); }
		catch(Exception $e){ error_log("destroySession: ".$e); }
	}
}

/**
 * Valida la cookie contra la BD.
 * @return Sesion|false el objeto Sesion si es válida, false si no
 */
function checkSession($seshnd){
	$header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
	if(!preg_match('/Bearer\s+(\S+)/', $header, $m)){
		error_log("api.checkSession: no Bearer token.");
		return false;
	}
	$token = $m[1];
	try {
		$sesion = $seshnd->obtenerPorToken($token);
	} catch(Exception $e){
		error_log("api.checkSession error: ".$e);
		return false;
	}
	if($sesion === false){
		error_log("api.checkSession: token no encontrado.");
		return false;
	}
	return $sesion;
}
