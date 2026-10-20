<?php
class Auto {
	public function __construct(
		public ?int $id,
		public ?int $id_usuario,
		public ?string $matricula,
		public ?string $marca,
		public ?string $color,
		public ?string $tamano,
	) {}
}
