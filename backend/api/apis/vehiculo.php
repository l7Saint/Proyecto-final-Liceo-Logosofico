<?php
require_once __DIR__ . '/../../classes/Api.php';
require_once __DIR__ . '/../../handlers/VehiculoHandler.php';
require_once __DIR__ . '/../../handlers/SesionHandler.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../api/api.php';

$vehiculoApi = new Api(
	name: "vehiculo",
	handlers: [
		'vehiculo' => new VehiculoHandler($conexion),
		'sesion'   => new SesionHandler($conexion)
	],
	prerun: function (array $data, array $handlers, Closure $endLog): void {
		if (checkSession($handlers['sesion']) === false) {
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized",
				getResponse: null
			);
		}
	}
);

$vehiculoApi->addEndpoint(new Endpoint(
	name: "crear",
	method: "POST",
	parameters: ['marca', 'modelo', 'color', 'tamano'],
	run: function (array $data, array $handlers, Closure $endLog): void {
		$vehhnd = $handlers['vehiculo'];

		$sesion = checkSession($handlers['sesion']);
		if ($sesion === false) {
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized",
				getResponse: null
			);
		}

		$nuevo = new Vehiculo(
			null,
			(int) $sesion->id_usuario,
			$data['marca'],
			$data['modelo'],
			$data['color'],
			$data['tamano']
		);

		try {
			$nuevoId = $vehhnd->crearVehiculo($nuevo);
		} catch (GenericRuntimeException $e) {
			throw $e;
		} catch (Throwable $e) {
			($endLog)('error', "crearVehiculo failed: " . $e->getMessage());
			throw new GenericRuntimeException(
				httpCode: 500,
				message: "Could not create vehiculo.",
				getResponse: null
			);
		}

		if ($nuevoId === false) {
			($endLog)('error', "crearVehiculo returned 'false'.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: "Could not create vehiculo.",
				getResponse: null
			);
		}

		($endLog)('debug', "Vehiculo created with id=$nuevoId for usuario=" . (int) $sesion->id_usuario);
		echo json_encode(['success' => true, 'id' => (int) $nuevoId]);
	}
));

$vehiculoApi->addEndpoint(new Endpoint(
	name: "obtener",
	method: "GET",
	parameters: [],
	run: function (array $data, array $handlers, Closure $endLog): void {
		$vehhnd = $handlers['vehiculo'];

		$sesion = checkSession($handlers['sesion']);
		if ($sesion === false) {
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized",
				getResponse: null
			);
		}

		try {
			$vehiculos = $vehhnd->obtenerPorIdUsuario((int) $sesion->id_usuario);
		} catch (GenericRuntimeException $e) {
			throw $e;
		} catch (Throwable $e) {
			($endLog)('error', "obtenerPorIdUsuario failed: " . $e->getMessage());
			throw new GenericRuntimeException(
				httpCode: 500,
				message: "Could not read vehiculos.",
				getResponse: null
			);
		}

		$resultado = [];
		foreach ($vehiculos as $v) {
			$resultado[] = [
				'id'         => (int) $v->id,
				'id_usuario' => (int) $v->id_usuario,
				'marca'      => $v->marca,
				'modelo'     => $v->modelo,
				'color'      => $v->color,
				'tamano'     => $v->tamano
			];
		}

		echo json_encode(['success' => true, 'vehiculos' => $resultado]);
	}
));

$vehiculoApi->addEndpoint(new Endpoint(
	name: "modificar",
	method: "PUT",
	parameters: ['id'],
	run: function (array $data, array $handlers, Closure $endLog): void {
		$vehhnd = $handlers['vehiculo'];

		$sesion = checkSession($handlers['sesion']);
		if ($sesion === false) {
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized",
				getResponse: null
			);
		}

		$id = (int) $data['id'];

		try {
			$vehiculo = $vehhnd->obtenerPorIdVehiculo($id);
		} catch (GenericRuntimeException $e) {
			throw $e;
		} catch (Throwable $e) {
			($endLog)('error', "obtenerPorIdVehiculo failed: " . $e->getMessage());
			throw new GenericRuntimeException(
				httpCode: 500,
				message: "Could not read vehiculo.",
				getResponse: null
			);
		}

		if ($vehiculo === false) {
			throw new GenericRuntimeException(
				httpCode: 404,
				message: "Vehiculo not found.",
				getResponse: null
			);
		}

		if ((int) $vehiculo->id_usuario !== (int) $sesion->id_usuario) {
			throw new GenericRuntimeException(
				httpCode: 403,
				message: "You do not own this vehiculo.",
				getResponse: null
			);
		}

		// Campos opcionales: no declarados en parameters, se leen con isset().
		if (isset($data['marca']))  { $vehiculo->marca  = $data['marca']; }
		if (isset($data['modelo'])) { $vehiculo->modelo = $data['modelo']; }
		if (isset($data['color']))  { $vehiculo->color  = $data['color']; }
		if (isset($data['tamano'])) { $vehiculo->tamano = $data['tamano']; }

		try {
			$ok = $vehhnd->modificarVehiculo($id, $vehiculo);
		} catch (GenericRuntimeException $e) {
			throw $e;
		} catch (Throwable $e) {
			($endLog)('error', "modificarVehiculo failed: " . $e->getMessage());
			throw new GenericRuntimeException(
				httpCode: 500,
				message: "Could not modify vehiculo.",
				getResponse: null
			);
		}

		if ($ok === false) {
			($endLog)('error', "modificarVehiculo returned 'false' for id=$id.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: "Could not modify vehiculo.",
				getResponse: null
			);
		}

		($endLog)('debug', "Vehiculo modified with id=$id.");
		echo json_encode(['success' => true]);
	}
));

$vehiculoApi->addEndpoint(new Endpoint(
	name: "eliminar",
	method: "DELETE",
	parameters: ['id'],
	run: function (array $data, array $handlers, Closure $endLog): void {
		$vehhnd = $handlers['vehiculo'];

		$sesion = checkSession($handlers['sesion']);
		if ($sesion === false) {
			throw new GenericRuntimeException(
				httpCode: 401,
				message: "Unauthorized",
				getResponse: null
			);
		}

		$id = (int) $data['id'];

		try {
			$vehiculo = $vehhnd->obtenerPorIdVehiculo($id);
		} catch (GenericRuntimeException $e) {
			throw $e;
		} catch (Throwable $e) {
			($endLog)('error', "obtenerPorIdVehiculo failed: " . $e->getMessage());
			throw new GenericRuntimeException(
				httpCode: 500,
				message: "Could not read vehiculo.",
				getResponse: null
			);
		}

		if ($vehiculo === false) {
			throw new GenericRuntimeException(
				httpCode: 404,
				message: "Vehiculo not found.",
				getResponse: null
			);
		}

		if ((int) $vehiculo->id_usuario !== (int) $sesion->id_usuario) {
			throw new GenericRuntimeException(
				httpCode: 403,
				message: "You do not own this vehiculo.",
				getResponse: null
			);
		}

		try {
			$ok = $vehhnd->eliminarVehiculo($id);
		} catch (GenericRuntimeException $e) {
			throw $e;
		} catch (Throwable $e) {
			($endLog)('error', "eliminarVehiculo failed: " . $e->getMessage());
			throw new GenericRuntimeException(
				httpCode: 500,
				message: "Could not delete vehiculo.",
				getResponse: null
			);
		}

		if ($ok === false) {
			($endLog)('error', "eliminarVehiculo returned 'false' for id=$id.");
			throw new GenericRuntimeException(
				httpCode: 500,
				message: "Could not delete vehiculo.",
				getResponse: null
			);
		}

		($endLog)('debug', "Vehiculo deleted with id=$id.");
		echo json_encode(['success' => true]);
	}
));

$main->addApi($vehiculoApi);
