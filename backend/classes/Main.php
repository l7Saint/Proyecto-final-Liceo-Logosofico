<?php
require_once __DIR__ . '/Api.php';
class BadMainException extends LogicException {}
class Main {
	public array $apis = [];

	public function run(
		array $request,
		string $method,
		?array $data
	): void {
		elog('debug', "In main");
		$apiName = $request[0] ?? '';
		$endpName = $request[1] ?? '';
		$api = $this->findApi($apiName);
		if($api === null){
			elog('error', "Requested access to nonexistant api: api.$apiName");
			http_response_code(404);
			echo json_encode([
				'error' => 'not_found',
				'message' => 'The requested endpoint does not exist.'
			]);
			return;
		}
		if(!isset($data))
			$data = [];
		$apiName = $request[0] ?? '';
		try {
			$responseBody = $api->runEndpoint($endpName, $method, $data);
			echo json_encode($responseBody);
			elog('debug', "Successful main");
		} catch (GenericRuntimeException $e) {
			elog('debug', "GenericRuntimeException: " . $e->getMessage());
			http_response_code($e->httpCode);
			if($e->getResponse !== null)
				$responseBody = ($e->getResponse)();
			echo json_encode($responseBody);
			return;
		} catch (InvalidEndpointException $e) {
			http_response_code(404);
			echo json_encode([
				'error' => 'not_found',
				'message' => 'The requested endpoint does not exist.'
			]);
			return;
		} catch (InvalidDataException $e) {
			http_response_code(400);
			$message = $e->getMessage();
			echo json_encode([
				'error' => 'bad_request',
				'message' => "{$message}"
			]);
			return;
		} catch (BadMethodException $e) {
			http_response_code(405);
			echo json_encode([
				'error' => 'bad_method',
				'message' => "Method not allowed."
			]);
			return;
		} catch (Throwable $e) {
			http_response_code(500);
			$msg = $e->getMessage();
			$line = $e->getLine();
			$file = $e->getFile();
			elog('error', "In {$file}:{$line} | Error: {$msg}");
			return;
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
