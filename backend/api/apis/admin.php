<?php
require_once __DIR__ . '/../../classes/Api.php';
require_once __DIR__ . '/../../handlers/UsuarioHandler.php';
require_once __DIR__ . '/../../handlers/SesionHandler.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../api/api.php';

$adminApi = new Api(
	name: "admin",
	handlers: [
		'usuario' => new UsuarioHandler($conexion),
		'sesion' => new SesionHandler($conexion)
	],
	prerun: function (array $data, array $handlers): void {
		if(verificarAdministrador($handlers['usuario'], $handlers['sesion']) !== 1){
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized",
				getResponse: null
			);
		}
	}
);

$adminApi->addEndpoint(new Endpoint(
	name: "crear",
	method: "POST",
	parameters: ['nombre', 'apellido', 'email', 'contrasena'],
	run: function (array $data, array $handlers, Closure $endLog): array {
		$usrhnd = handlers['usuario'];
		try {
			$existingUser = $usrhnd->obtenerPorEmail($data['email']);
			if($existingUser !== false){
				throw new GenericRuntimeException(
					httpCode: 409,
					message: 'Email already registered',
					getResponse: null
				);
			}
			$usuario = new Usuario(
				null, //$id
				$data['nombre'], //$nombre
				$data['apellido'], //$apellido
				$data['email'], //$email
				password_hash($data['contrasena'], PASSWORD_DEFAULT), //$contrasena_hash
				$data['inactivo'], //$inactivo
				$data['es_admin'], //$es_admin
				null //$fecha_registro
			);
			$newId = $usrhnd->crearUsuarioAdmin($usuario);
			if($newId){
				//error_log("Admin creo nuevo usuario, email: ".$data['email']);
				return [
					'success' => true,
					'id' => $newId
				];
			} else {
				($endLog)('error', "Unexpected error, crearUsuarioAdmin returned 'false'.");
				throw new GenericRuntimeException(
					httpCode: 500,
					message: "Couldnt create user for some reason",
					getResponse: null
				);
			}
		} catch(Throwable $e){
			$message = $e->getMessage();
			($endLog)('error', "Unexpected error, message: $message.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage(),
				getResponse: null
			);
		}
	}
));

$adminApi->addEndpoint(new Endpoint(
	name: "obtener",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers, Closure $endLog): array {
		$usrhnd = handlers['usuario'];
		try {
			$usuarios = $usrhnd->obtenerTodos();
			$resultado = [];
			foreach($usuarios as $u){
				$resultado[] = [
					'id' => $u->id,
					'nombre' => $u->nombre,
					'apellido' => $u->apellido,
					'email' => $u->email,
					'inactivo' => $u->inactivo,
					'es_admin' => $u->es_admin,
					'fecha_registro' => $u->fecha_registro
				];
			}
			return [
				'success' => true,
				'usuarios' => $resultado
			];
		} catch(Throwable $e){
			$message = $e->getMessage();
			($endLog)('error', "Unexpected error, message: $message.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage(),
				getResponse: null
			);
		}
	}
));

$adminApi->addEndpoint(new Endpoint(
	name: "modificar",
	method: "PUT",
	parameters: ['id'],
	run: function (array $data, array $handlers, Closure $endLog): array {
		$usrhnd = handlers['usuario'];
		try {
			$usuario = $usrhnd->obtenerPorId($data['id']);
			if($usuario === false){
				throw new GenericRuntimeException(
					httpCode: 404,
					message: "User not found.",
					getResponse: null
				);
			}

			if(isset($data['email']) && $data['email'] !== $usuario->email){
				$existingUser = $usrhnd->obtenerPorEmail($data['email']);
				if($existingUser !== false && $existingUser->id != $data['id']){
					throw new GenericRuntimeException(
						httpCode: 409,
						message: "Email already registered.",
						getResponse: null
					);
				}
			}

			if(isset($data['nombre'])) $usuario->nombre = $data['nombre'];
			if(isset($data['apellido'])) $usuario->apellido = $data['apellido'];
			if(isset($data['email'])) $usuario->email = $data['email'];
			if(isset($data['contrasena'])) $usuario->contrasena_hash = password_hash($data['contrasena'], PASSWORD_DEFAULT);
			if(isset($data['inactivo'])) $usuario->inactivo = $data['inactivo'] ? 1 : 0;
			if(isset($data['es_admin'])) $usuario->es_admin = $data['es_admin'] ? 1 : 0;

			if($usrhnd->modificarUsuario($data['id'], $usuario)){
				return [
					'success' => true
				];
			} else {
				($endLog)('error', "Unexpected error, modificarUsuario returned 'false'.");
				throw new GenericRuntimeException(
					httpCode: 500,
					message: "Couldnt modify user.",
					getResponse: null
				);
			}
		} catch(Throwable $e){
			$message = $e->getMessage();
			($endLog)('error', "Unexpected error, message: $message.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage(),
				getResponse: null
			);
		}
	}
));

$adminApi->addEndpoint(new Endpoint(
	name: "eliminar",
	method: "DELETE",
	parameters: ['id'],
	run: function (array $data, array $handlers, Closure $endLog): array {
		$usrhnd = handlers['usuario'];
		try {
			//no permitir que un admin se elimine a si mismo
			session_start();
			if($_SESSION['user_id'] == $data['id']){
				throw new GenericRuntimeException(
					httpCode: 400,
					message: 'Cant delete your own user',
					getResponse: null
				);
			}

			if($usrhnd->eliminarUsuario($data['id'])){
				http_response_code(200);
				echo json_encode([
					'success' => true
				]);
				error_log("Admin elimino usuario id: " . $data['id']);
			} else {
				($endLog)('error', "Unexpected error, eliminarUsuario returned 'false'.");
				throw new GenericRuntimeException(
					httpCode: 500,
					message: 'Couldnt delete user',
					getResponse: null
				);
			}
		} catch(Throwable $e){
			$message = $e->getMessage();
			($endLog)('error', "Unexpected error, message: $message.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: $e->getMessage(),
				getResponse: null
			);
		}
	}
));

$main->addApi($adminApi);
