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

function verificarAdministrador($usrhnd, $seshnd){
	$sesion = checkSession($seshnd);
	if($sesion === false)
		return false;
	$user = $usrhnd->obtenerPorId($sesion->id_usuario);
	if(!$user)
		return false;
	error_log("Call a api.verificarAdministrador: $user->es_admin");
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

/* ============================================================
   EXTRACCIÓN DEL BEARER TOKEN
   ============================================================
   Apache no siempre reenvía el header Authorization a PHP.
   Esto depende del SAPI:
     - mod_php:                   HTTP_AUTHORIZATION disponible.
     - CGI/FastCGI:               suele requerir REDIRECT_HTTP_AUTHORIZATION
                                  o un rewrite rule en .htaccess.
     - Algunos proxys/frameworks: solo expuesto vía getallheaders().

   Este helper prueba todas las fuentes conocidas en orden y
   devuelve el token limpio, o null si no hay ninguno.
   ============================================================ */
function getBearerToken(){
	$header = '';

	// 1) Caso normal (mod_php)
	if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
		$header = $_SERVER['HTTP_AUTHORIZATION'];
	}
	// 2) Apache con CGI/FastCGI
	elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
		$header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
	}
	// 3) Fallback genérico: recorrer todos los headers
	elseif (function_exists('getallheaders')) {
		foreach (getallheaders() as $k => $v) {
			if (strcasecmp($k, 'Authorization') === 0) {
				$header = $v;
				break;
			}
		}
	}
	// 4) Último recurso: leer de apache_request_headers()
	elseif (function_exists('apache_request_headers')) {
		foreach (apache_request_headers() as $k => $v) {
			if (strcasecmp($k, 'Authorization') === 0) {
				$header = $v;
				break;
			}
		}
	}

	if ($header === '') {
		error_log('no hay header chavales');
		return null;
	}

	// Acepta "Bearer <token>" (case-insensitive)
	if (preg_match('/^\s*Bearer\s+(\S+)\s*$/i', $header, $m)) {
		return $m[1];
	}

	error_log('no hay header chavales');
	return null;
}

/* ============================================================
   CICLO DE VIDA DE LA SESIÓN
   ============================================================ */

/**
 * Crea una sesión en la BD y devuelve el token al llamador.
 * No envía cookie: el cliente lo guarda y lo manda como Bearer.
 *
 * @param Usuario       $usuario
 * @param SesionHandler $seshnd
 * @return string el token generado
 * @throws Exception si no se puede crear la sesión
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
 * Elimina la sesión de la BD asociada al Bearer token de la request.
 * Si no hay token, no hace nada.
 *
 * @param SesionHandler $seshnd
 */
function destroySession($seshnd){
	$token = getBearerToken();
	if($token === null){
		return;
	}
	try {
		$seshnd->eliminarSesion($token);
	} catch(Exception $e){
		error_log("destroySession: ".$e);
	}
}

/**
 * Valida el Bearer token contra la BD.
 *
 * @param SesionHandler $seshnd
 * @return Sesion|false el objeto Sesion si es válido, false si no
 */
function checkSession($seshnd){
	$token = getBearerToken();
	if($token === null){
		error_log("api.checkSession: no Bearer token.");
		return false;
	}
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
	error_log("SESION: $sesion->id_usuario");
	return $sesion;
}
