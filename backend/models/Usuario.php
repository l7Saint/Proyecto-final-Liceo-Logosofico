<?php
class Usuario {
	public function __construct(
		public ?int $id,
		public ?string $nombre,
		public ?string $apellido,
		public ?string $email,
		public ?string $contrasena_hash,
		public ?int $inactivo,
		public ?int $es_admin,
		public ?string $fecha_registro,
	) {}

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
