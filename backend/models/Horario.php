<?php
class Horario {
	private const DIAS_VALIDOS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
	public function __construct(
		public ?int $numero_hora,
		public ?string $dia_semana,
		public ?string $horario,
	) {
		if ($dia_semana !== null && !in_array($dia_semana, self::DIAS_VALIDOS, true))
			throw new InvalidArgumentException('argumento invalido para dia_semana');
	}
}
