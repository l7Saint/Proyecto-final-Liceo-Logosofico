<?php
class Usuario {
	public $id;
	public $nombre;
	public $apellido;
	public $email;
	public $contrasena_hash;
	public $inactivo;
	public $es_admin;
	public $fecha_registro;

	public function __construct($id, $nombre, $apellido, $email, $contrasena_hash, $inactivo, $es_admin, $fecha_registro) {
		$this->id = $id;
		$this->nombre = $nombre;
		$this->apellido = $apellido;
		$this->email = $email;
		$this->contrasena_hash = $contrasena_hash;
		$this->inactivo = $inactivo;
		$this->es_admin = $es_admin;
		$this->fecha_registro = $fecha_registro;
	}

	public function __toString(){
		return "
id: $this->id
nombre: $this->nombre
apellido: $this->apellido
email: $this->email
contrasena_hash: $this->contrasena_hash
inactivo: $this->inactivo
es_admin: $this->es_admin
fecha_registro: $this->fecha_registro
		";
	}
}
