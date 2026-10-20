<?php
class Sesion {
	public function __construct(
		public ?string $token,
		public ?int $id_usuario,
		public ?string $fecha_creacion,
	) {}
}
