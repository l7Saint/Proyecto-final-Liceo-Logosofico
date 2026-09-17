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
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 86400");

session_set_cookie_params([
	'samesite' => 'None',
	'secure'   => true,
	'httponly' => true,
]);

// Respond to preflight and stop
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function verificarAdministrador($usrhnd){
	if(!checkSession())	
		return false;
	$id = $_SESSION['user_id'];
	$user = $usrhnd->obtenerPorId($id);
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

function startSession($usuario){
	session_start();
	session_regenerate_id(true);
	$_SESSION['user_id'] = $usuario->id;
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['user-agent'] = $_SERVER['HTTP_USER_AGENT'];

	//error_log("SESSION DATA: " . print_r($_SESSION, true));

	//error_log("VALUES:::::");
	//error_log("user_id: " . $usuario->id);
	//error_log("user_agent: " . $_SERVER['HTTP_USER_AGENT']);
	//error_log("ip: " . $_SERVER['REMOTE_ADDR']);

	error_log("Session Started for user_id: ".$_SESSION['user_id']." ; ip address: ".$_SERVER['REMOTE_ADDR']);
}

function destroySession(){
	session_start();
	session_destroy();
}

function checkSession(){
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	} 
	//error_log("SESSION DATA: " . print_r($_SESSION, true));

	if(!isset($_SESSION['user_id'])){
		error_log("api.checkSession returned false: Session not opened.");
		return false;
	}
////////if($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR']){
////////	error_log("api.checkSession returned false: ip address mismatch, session ip = ".$_SESSION['ip']." | remote ip = ".$_SERVER['REMOTE_ADDR']);
////////	destroySession();
////////	return false;
////////}
	if($_SESSION['user-agent'] !== $_SERVER['HTTP_USER_AGENT']){
		error_log("api.checkSession returned false: user-agent mismatch; session user-agent = ".$_SESSION['user-agent']." ; remote user-agent = ".$_SERVER['HTTP_USER_AGENT']);
		destroySession();
		return false;
	}

	//si ningun error salto
	error_log("api.checkSession returned true.");
	return true;
}
