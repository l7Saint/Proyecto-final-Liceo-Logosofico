<?php
class Horario {
	public $numero_hora;
	public $dia_semana;
	public $horario;

	private const DIAS_VALIDOS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
	public function __construct($numero_hora, $dia_semana, $horario) {
		if ($dia_semana !== null && !in_array($dia_semana, self::DIAS_VALIDOS, true))
			throw new InvalidArgumentException('argumento invalido para dia_semana');

		$this->numero_hora = $numero_hora;
		$this->dia_semana  = $dia_semana;
		$this->horario     = $horario;
	}
}
