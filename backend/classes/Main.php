<?php
require_once 'Api.php';
class BadMainException extends LogicException {}
class Main {
	public array $apis = [];

	public function run(
		array $request,
		string $method,
		array $data
	): void {
		$apiName = $request[0] ?? '';
		$endpName = $request[1] ?? '';
		$api = $this->findApi($apiName);
		if($api === null){
			http_response_code(404);
			echo json_encode([
				'error': 'not_found',
				'message': 'The requested endpoint does not exist.'
			]);
			die();
		}

		try {
			$responseBody = $api->runEndpoint($endpName, $method, $data);
			echo json_encode($responseBody);
		} catch (GenericRuntimeException $e) {
			http_response_code($e->httpCode);
			$responseBody = ($e->getResponse)();
			echo json_encode($responseBody);
			die();
		} catch (InvalidEndpointException $e) {
			http_response_code(404);
			echo json_encode([
				'error': 'not_found',
				'message': 'The requested endpoint does not exist.'
			]);
			die();
		} catch (InvalidDataException $e) {
			http_response_code(400);
			$message = $e->getMessage();
			echo json_encode([
				'error': 'bad_request',
				'message': "{$message}"
			]);
			die();
		} catch (BadMethodException $e) {
			http_response_code(405);
			echo json_encode([
				'error': 'bad_method',
				'message': "Method not allowed."
			]);
			die();
		}
	}

	public function addApi(Api $api): void {
		if($this->findApi($api->name) !== null)
			throw new BadMainException("Duplicate api: {$api->name}");
		$this->apis[] = $api;
	}

	private function findApi(string $name): ?Api{
		foreach ($this->apis as $api){
			if($api->name === $name)
				return $api;
		}
		return null;
	}

	public function __construct() {}
}
