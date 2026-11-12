<?php
class Estacionamiento {
	public function __construct(
		public ?int $id,
		public ?int $id_usuario,
		public ?int $id_vehiculo,
		public ?int $id_cuadra,
		public ?string $fecha,
		public ?string $hora_inicio,
		public ?string $hora_fin,
	) {}
}
