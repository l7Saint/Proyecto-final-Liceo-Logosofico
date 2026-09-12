<?php
class Usuario {
	public $id;
	public $nombre;
	public $apellido;
	public $email;
	public $contrasena_hash;
	public $fecha_registro;

	public function __construct($id, $nombre, $apellido, $email, $contrasena_hash, $fecha_registro) {
		$this->id = $id;
		$this->nombre = $nombre;
		$this->apellido = $apellido;
		$this->email = $email;
		$this->contrasena_hash = $contrasena_hash;
		$this->fecha_registro = $fecha_registro;
	}
}
