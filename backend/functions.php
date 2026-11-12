<?php
function elog(string $type, string $message): void {
	$prefix = '';
	switch($type){
		case 'debug':
			$prefix = '## DEBUG ## ';
			break;
		case 'warning':
			$prefix = '## WARNING ## ';
			break;
		case 'error':
			$prefix = '## ERROR ## ';
			break;
		default:
			throw new Exception("Wrong type at 'elog': {$type}");
	}
	error_log($prefix . $message);
}

/**
 * Registra una excepción reportando el origen real del error.
 *
 * Recorre la cadena con getPrevious() hasta la excepción más interna y
 * registra el archivo y la línea de origen, no los del catch que la
 * envolvió. Además registra la cadena completa de exterior a interior.
 *
 * @param Throwable $e La excepción capturada (puede estar envuelta)
 * @param string $context Nombre del módulo donde ocurrió, ej. 'api.foo.bar'
 * @return void
 */
function logException(Throwable $e, string $context): void {
	$chain = [];
	$root  = $e;
	$prev  = $e;
	while($prev !== null){
		$chain[] = sprintf(
			'%s: %s in %s:%d',
			$prev::class,
			$prev->getMessage(),
			$prev->getFile(),
			$prev->getLine()
		);
		$root = $prev;
		$prev = $prev->getPrevious();
	}
	$inner = $root->getFile() . ':' . $root->getLine();
	$trace = implode(' -> ', $chain);
	elog('error', "Error en {$context}: {$e->getMessage()} \nIn {$inner} \nChain: {$trace}");
}
