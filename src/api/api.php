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
