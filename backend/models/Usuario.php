<?php
class Usuario {
	public int $id;
	public string $nombre;
	public string $apellido;
	public string $email;
	public string $contrasena_hash;
	public bool $inactivo;
	public bool $es_admin;
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
}
