<?php
require_once '../../classes/api.php';
require_once '../../handlers/usuariohandler.php';
require_once '../../handlers/sesionhandler.php';
require_once '../../config/conexion.php';
require_once '../../api/api.php';

$usuarioApi = new Api(
	name: "usuario",
	handlers: [
		'usuario' => new UsuarioHandler($conexion),
		'sesion' => new SesionHandler($conexion)
	],
	prerun: null
);

$usuarioApi->addEndpoint(new Endpoint(
	name: "login",
	method: "POST",
	parameters: ['email', 'contrasena'],
	run: function (array $data, array $handlers): array {
		$usrhnd = $handlers['usuario'];
		$seshnd = $handlers['sesion'];
		try {
			$usuario = $usrhnd->obtenerPorEmail($data['email']);
			if(
				$usuario === false ||
				!password_verify($data['contrasena'], $usuario->contrasena_hash)
			){
				throw new GenericRuntimeException(
					httpCode: 401,
					message: "Invalid email or password."
				);
			}
			$token = startsession($usuario, $seshnd);
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
			throw GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage()
			);
		}
	}
));

$usuarioApi->addEndpoint(new Endpoint(
	name: "user",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers): array {
		$sesion = checksession($handlers['sesion']);
		if($sesion === false){
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized"
			);
		}
		$usuario = $handlers['usuario']->obtenerPorId($sesion->id_usuario);
		if($usuario === false){
			destroySession($seshnd);
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized"
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

$usuarioApi->addEndpoint(new Endpoint(
	name: "check",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers): array {
		if(checksession($handlers['sesion'])){
			return ['success' => true]
		} else {
			throw new GenericRuntimeException(
				httpCode: 401,
				message: 'Unauthorized'
			);
		}
	}
));

$usuarioApi->addEndpoint(new Endpoint(
	name: "logout",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers): array {
		destroySession($handlers['sesion']);
		return ['success' => true];
	}
));

$usuarioApi->addEndpoint(new Endpoint(
	name: "signin",
	method: "POST",
	parameters: ['nombre', 'apellido', 'email', 'contrasena'],
	run: function (array $data, array $handlers): array {
		$usrhnd = $handlers['usuario'];
		try{
			$existingUser = $usrhnd->obtenerPorEmail($data['email']);
		} catch(Throwable $e){
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage();
			);
		}
		if($existingUser !== false){
			throw new GenericRuntimeException(
				httpCode: 500,
				message: "Email already registered.";
			);
		}
		try{
			$usuario = new Usuario(
				null,
				$data['nombre'],
				$data['apellido'],
				$data['email'],
				password_hash($data['contrasena'], password_default),
				false,
				false,
				null
			);
			$nuevo_id = $usrhnd->crearUsuario($usuario);
			if($nuevo_id !== false){
				$usuario->id = (int)$nuevo_id; 
				startSession($usuario, $seshnd);
				//error_log("nuevo registro de usuario, email: ".$data['email']);
				return ['success' => true];
			} else {
				throw new GenericRuntimeException(
					httpCode: 500,
					message: $e->getMessage();
				);
			}
		}catch(Throwable $e){
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage();
			);
		}
	}
));

$main->addapi($usuarioApi);
