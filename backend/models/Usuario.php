<?php
class Usuario {
	public $id;
	public $nombre;
	public $apellido;
	public $email;
	public $contrasenaHash;
	public $fechaRegistro;

	public function __construct($id, $nombre, $apellido, $email, $contrasenaHash, $fechaRegistro) {
		$this->id = $id;
		$this->nombre = $nombre;
		$this->apellido = $apellido;
		$this->email = $email;
		$this->contrasenaHash = $contrasenaHash;
		$this->fechaRegistro = $fechaRegistro;
	}
}
