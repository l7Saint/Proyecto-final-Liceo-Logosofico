<?php
require_once '/functions.php';

class Endpoint {
	public function __construct(
		public string $name,
		public string $method,
		public array $parameters,
		/**
		 * @param Closure(array $data, array $handlers): void $run
		 */
		public Closure $run,
	) {}
}

class BadApiException extends LogicException {}
class InvalidDataException extends RuntimeException {}
class BadMethodException extends RuntimeException {}
class GenericRuntimeException extends RuntimeException {
	public int $httpCode;
	public function __construct(int $httpCode, string $message){
		$this->httpCode = $httpCode;
		parent::__construct($message);
	}
}

class Api {
	public string $name;
	/**
	 * @param ?Closure(array $data, array $handlers): void $prerun
	 */
	private ?Closure $prerun;
	private array $endpoints = [];
	private array $handlers = [];

	public function runEndpoint(string $endpName, string $method, array $data): void{
		$endp = $this->findEndpoint($endpName);
		if($endp === null)
			throw new BadApiException("Invalid endpoint name: {$endpName}.");
		elog('debug', "Call to api.{$this->name}.{$endp->name}.");
		$errorMsg = $this->checkParameters($data, $endp->parameters);
		if($errorMsg !== null)
			throw new InvalidDataException($errorMsg);
		if($method !== $endp->method)
			throw new BadMethodException("Invalid method expected \'{$endp->method}\' obtained \'{$method}\'");
		try {
			if(isset($this->prerun))
				($this->prerun)($data, $this->handlers);
			($endp->run)($data, $this->handlers);
		} catch (GenericRuntimeException $e) {
			//500 error code es irrecuperable
			if($e->httpCode === 500){
				$msg = $e->getMessage();
				elog('error', "Error en api.{$this->name}.{$endp->name}: {$msg}");
				http_response_code($e->httpCode);
				die();
			} else {
				throw $e;
			}
		} catch (Throwable $e) {
			$msg  = $e->getMessage();
			$file = $e->getFile();
			$line = $e->getLine();
			elog('error', "Error en api.{$this->name}.{$endp->name}: {$msg} \nIn {$file}:{$line}");
			http_response_code(500);
			die();
		}
	}

	public function addEndpoint(Endpoint $endp): void{
		if($this->findEndpoint($endp->name) !== null)
			throw new BadApiException("Duplicate endpoint: {$endp->name} in {$this->name} API.");
		$this->endpoints[] = $endp;
	}

	public function __construct(string $name, array $handlers, ?Closure $prerun = null){
		$this->name = $name;
		$this->handlers = $handlers;
		$this->prerun = $prerun;
	}

	private function checkParameters(array $data, array $parameters): ?string{
		$error = null;
		foreach ($parameters as $p){
			if(!isset($data[$p])){
				if (!isset($error))
					$error = '';
				$error = $error . "Missing parameter: $p ";
			}
		}
		return $error;
	}

	private function findEndpoint(string $name): ?Endpoint{
		foreach ($this->endpoints as $end){
			if($end->name === $name)
				return $end;
		}
		return null;
	}
}
