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
