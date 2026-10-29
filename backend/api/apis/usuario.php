<?php
//elog('debug', "Before Import API");
require_once __DIR__ . '/../../classes/Api.php';
require_once __DIR__ . '/../../handlers/UsuarioHandler.php';
require_once __DIR__ . '/../../handlers/SesionHandler.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../api/api.php';

//elog('debug', "Before Usuario API");
$usuarioApi = new Api(
	name: "usuario",
	handlers: [
		'usuario' => new UsuarioHandler($conexion),
		'sesion' => new SesionHandler($conexion)
	],
	prerun: null
);

//elog('debug', "Before login Endpoint");
$usuarioApi->addEndpoint(new Endpoint(
	name: "login",
	method: "POST",
	parameters: ['email', 'contrasena'],
	run: function (array $data, array $handlers, Closure $endLog): array {
		$usrhnd = $handlers['usuario'];
		$seshnd = $handlers['sesion'];
		try {
			$usuario = $usrhnd->obtenerPorEmail($data['email']);
			if(
				$usuario === false ||
				!password_verify($data['contrasena'], $usuario->contrasena_hash)
			){
				($endLog)('debug', "Failed login to user id=$usuario->id.");
				throw new GenericRuntimeException(
					httpCode: 401,
					message: "Invalid email or password.",
					getResponse: null
				);
			}
			$token = startsession($usuario, $seshnd);
			($endLog)('debug', "User with id=$usuario->id session started.");
			return [
				'success' => true,
				'token' => $token,
				'usuario' => [
					'nombre'         => $usuario->nombre,
					'apellido'       => $usuario->apellido,
					'email'          => $usuario->email,
					'es_admin'       => (int)$usuario->es_admin,
					'fecha_registro' => $usuario->fecha_registro
				]
			];
		} catch (Throwable $e) {
			$message = $e->getMessage();
			($endLog)('error', "Unexpected error.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage(),
				getResponse: null
			);
		}
	}
));

//elog('debug', "Before user Endpoint");
$usuarioApi->addEndpoint(new Endpoint(
	name: "user",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers, Closure $endLog): array {
		$sesion = checkSession($handlers['sesion']);
		if($sesion === false){
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized",
				getResponse: null
			);
		}
		$usuario = $handlers['usuario']->obtenerPorId($sesion->id_usuario);
		if($usuario === false){
			destroySession($seshnd);
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized",
				getResponse: null
			);
		}
		return [
			'success' => true,
			'usuario' => [
				'nombre'         => $usuario->nombre,
				'apellido'       => $usuario->apellido,
				'email'          => $usuario->email,
				'es_admin'       => (int)$usuario->es_admin,
				'fecha_registro' => $usuario->fecha_registro
			]
		];
	}
));

//elog('debug', "Before check Endpoint");
$usuarioApi->addEndpoint(new Endpoint(
	name: "check",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers, Closure $endLog): array {
		if(checkSession($handlers['sesion'])){
			return ['success' => true];
		} else {
			throw new GenericRuntimeException(
				httpCode: 401,
				message: 'Unauthorized',
				getResponse: null
			);
		}
	}
));

//elog('debug', "Before logout Endpoint");
$usuarioApi->addEndpoint(new Endpoint(
	name: "logout",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers, Closure $endLog): array {
		destroySession($handlers['sesion']);
		return ['success' => true];
	}
));

//elog('debug', "Before signin Endpoint");
$usuarioApi->addEndpoint(new Endpoint(
	name: "signin",
	method: "POST",
	parameters: ['nombre', 'apellido', 'email', 'contrasena'],
	run: function (array $data, array $handlers, Closure $endLog): array {
		$usrhnd = $handlers['usuario'];
		$seshnd = $handlers['sesion'];
		try{
			$existingUser = $usrhnd->obtenerPorEmail($data['email']);
		} catch(Throwable $e){
			$message = $e->getMessage()
			($endLog)('error', "Unexpected error, obtenerPorEmail failed with the following error: $message");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage(),
				getResponse: null
			);
		}
		if($existingUser !== false){
			throw new GenericRuntimeException(
				httpCode: 409,
				message: "Email already registered.",
				getResponse: null
			);
		}
		try{
			$usuario = new Usuario(
				null,
				$data['nombre'],
				$data['apellido'],
				$data['email'],
				password_hash($data['contrasena'], PASSWORD_DEFAULT),
				false,
				false,
				null
			);
			$nuevo_id = $usrhnd->crearUsuario($usuario);
			if($nuevo_id !== false){
				$usuario->id = (int)$nuevo_id; 
				startSession($usuario, $seshnd);
				($endLog)('debug', "New user registered with id=$nuevo_id");
				return ['success' => true];
			} else {
				($endLog)('error', "Unexpected error, crearUsuario returned 'false'.");
				throw new GenericRuntimeException(
					httpCode: 500,
					message: '',
					getResponse: null
				);
			}
		}catch(Throwable $e){
			$message = $e->getMessage();
			$dataM = implode(", ", $data);
			($endLog)('error', "Unexpected error creating new user, data: $dataM\nmessage: $message.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage(),
				getResponse: null
			);
		}
	}
));

//elog('debug', "Before addApi");
$main->addapi($usuarioApi);
//elog('debug', "usuario.php finalized");
