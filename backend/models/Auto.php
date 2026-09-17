<?php
class Auto {
	public $id;
	public $matricula;
	public $color;
	public $tamano;

	public function __construct($id, $matricula, $color, $tamano) {
		$this->id = $id;
		$this->matricula = $matricula;
		$this->color = $color;
		$this->tamano = $tamano;
	}
}
