<?php
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

function setSession($usuario){
	session_start();
	session_regenerate_id(true);
	$_SESSION['user_id'] = $usuario->id;
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['user-agent'] = $_SERVER['HTTP_USER_AGENT'];
}

function checkSession(){
	session_start();

	if(!isset($_SESSION['user_id'])){
		return false;
	}
	if($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR']){
		error_log("Session hijacking detected reason: ip address mismatch, session ip = ".$_SESSION['ip']." | remote ip = ".$_SERVER['REMOTE_ADDR']);
		return false;
	}
	if($_SESSION['user-agent'] !== $_SERVER['HTTP_USER_AGENT']){
		error_log("Session hijacking detected reason: user-agent mismatch; session user-agent = ".$_SESSION['user-agent']." | remote user-agent = ".$_SERVER['HTTP_USER_AGENT']);
		return false;
	}

	//si ningun error salto
	return true;
}

function destroySession(){
	session_start();
	session_destroy();
}
