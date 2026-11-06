<?php
class Cuadra {
	public function __construct(
		public ?int $id,
		public ?int $numero_puerta_inicial,
		public ?int $numero_puerta_final,
		public ?string $calle,
		public ?float $latitud,
		public ?float $longitud,
	) {}
}
