<?php
function verificarAdministrador($usrhnd, $seshnd){
	$sesion = checkSession($seshnd);
	if($sesion === false)
		return false;
	$user = $usrhnd->obtenerPorId($sesion->id_usuario);
	if(!$user)
		return false;
	//error_log("Call a api.verificarAdministrador: $user->es_admin");
	return $user->es_admin;
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
		elog('debug', 'No header in session check.');
		return null;
	}

	// Acepta "Bearer <token>" (case-insensitive)
	if (preg_match('/^\s*Bearer\s+(\S+)\s*$/i', $header, $m)) {
		return $m[1];
	}

	elog('debug', 'No header in session check.');
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

