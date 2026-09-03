<?php
header('Content-Type: application/json');

//Verificar que el usuario tenga una cookie de sesion y este loggeado
session_start();
if(
	!isset($_SESSION['usuario_id']) ||
	!isset($_SESSION['usuario_loggeado'])
){
	http_response_code(401);
	echo json_encode(['error' => 'Unauthorized']);
	exit;	
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

