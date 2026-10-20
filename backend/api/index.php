<?php
require_once '../classes/Main.php';
require_once '../functions.php';

//error handling ini
//ini_set('display_errors', 0);
//ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);

// -- CORS --
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: $origin");
header("Vary: Origin");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 86400");

/*
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);   // "/api/foo/bar"
$uri = preg_replace('#^/api/?#', '', $uri);                // "foo/bar"
$request = array_values(array_filter(explode('/', $uri))); // ["foo", "bar"]
 */

// Respond to preflight and stop
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
	elog('debug', "Before Main");
	$main = new Main();
	foreach (glob('apis/*.php') as $file) {
		elog('debug', "Imported: {$file}");
		require_once __DIR__ . '/' . $file;
	}
} catch (Throwable $e) {
	$message = $e->getMessage();
	$file = $e->getFile();
	$line = $e->getLine();
	elog('error', "Error while importing, message: $message location: $file:$line");
	die();
}
try {
	elog('debug', "After Import, Before Run");
	$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);   // "/api/foo/bar"
	$uri = preg_replace('#^/api/?#', '', $uri);                // "foo/bar"
	$main->run(
		request: array_values(array_filter(explode('/', $uri))),
		method: $_SERVER['REQUEST_METHOD'],
		data: json_decode(file_get_contents('php://input'), true)
	);
	elog('debug', "After Run, End");
} catch (Throwable $e) {
	$message = $e->getMessage();
	$file = $e->getFile();
	$line = $e->getLine();
	elog('error', "Error while executing main, message: $message location: $file:$line");
}
