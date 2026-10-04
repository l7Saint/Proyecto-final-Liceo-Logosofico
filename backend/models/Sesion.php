<?php
class Sesion {
	public $token;
	public $id_usuario;
	public $fecha_creacion;

	public function __construct($token, $id_usuario, $fecha_creacion){
		$this->token = $token;
		$this->id_usuario = $id_usuario;
		$this->fecha_creacion = $fecha_creacion;
	}
}
