<?php
class Usuario {
<<<<<<< HEAD
	public $id;
	public $nombre;
	public $apellido;
	public $email;
	public $contrasena_hash;
	public $fecha_registro;

	public function __construct($id, $nombre, $apellido, $email, $contrasena_hash, $fecha_registro) {
=======
	public int $id;
	public string $nombre;
	public string $apellido;
	public string $email;
	public string $contrasena_hash;
	public bool $inactivo;
	public bool $es_admin;
	public $fecha_registro;

	public function __construct($id, $nombre, $apellido, $email, $contrasena_hash, $inactivo, $es_admin, $fecha_registro) {
>>>>>>> feature-horarios
		$this->id = $id;
		$this->nombre = $nombre;
		$this->apellido = $apellido;
		$this->email = $email;
		$this->contrasena_hash = $contrasena_hash;
<<<<<<< HEAD
=======
		$this->inactivo = $inactivo;
		$this->es_admin = $es_admin;
>>>>>>> feature-horarios
		$this->fecha_registro = $fecha_registro;
	}
}
