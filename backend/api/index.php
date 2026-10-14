<?php
require_once '../classes/Main.php';

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

$main = new Main();
foreach (glob('apis/*.php') as $file) {
	require_once $file;
}
$main->run(
	request: explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'))
	method: $_SERVER['REQUEST_METHOD']
	data: json_decode(file_get_contents('php://input'), true)
);
