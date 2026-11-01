<?php
class Vehiculo {
	public function __construct(
		public ?int $id,
		public ?int $id_usuario,
		public ?string $marca,
		public ?string $modelo,
		public ?string $color,
		public ?string $tamano,
	) {}
}
