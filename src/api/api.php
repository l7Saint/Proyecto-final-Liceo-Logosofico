<?php
function sendBadRequest($message = 'Bad Request', $errors = null) {
	http_response_code(400);
	header('Content-Type: application/json');

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
	header('Content-Type: application/json');
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
	header('Content-Type: application/json');

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
	$error = '';
	foreach ($p as $parametros){
		if(!isset($data[$p]))
			$error += "Missing parameter $p";
	}
	return $error;
}
