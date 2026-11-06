<?php
class Normalizar {
	// Todas las normalizaciones son f: [0;$k] -> [0;1]
	// $x representa el valor del dato (ej. 10)
	// $k representa el maximo valor posible del dato (ej. 100)
	// $u representa que tan pronunciada es la curva exponencial con la
	// cual se normaliza el dato (ej. 2.5), tambien puede ser negativo y se
	// transforma en una curva logaritmica.
	// Las funciones '_dPositivo' mappean un dato $x = A mas cercano a 1 que,
	// por ejemplo, otro dato $x = B siempre que A > B.
	// Las funciones '_dNegativo' hacen lo contrario, mappean un dato $x = A mas
	// cercano a 1 que un dato $x = B siempre que A < B.

	public static function exponente_dPositivo(float $x, float $k, float $u): float {
		return (exp(($u*$x)/$k) - 1) / (exp($u) - 1);}
	public static function exponente_dNegativo(float $x, float $k, float $u): float {
		$x = ($k - $x);
		return (exp(($u*$x)/$k) - 1) / (exp($u) - 1);}
	public static function lineal_dPositivo(float $x, float $k): float {
		return $x/$k;}
	public static function lineal_dNegativo(float $x, float $k): float {
		$x = ($k - $x);
		return $x/$k;}
	public static function mediana(array $valores) : float {
		$cantidad = count($valores);
		sort($valores, SORT_NUMERIC);
		$indice_medio = intdiv($cantidad, 2);
		return ($cantidad % 2 === 1)
			? (float) $valores[$indice_medio]
			: (float) ($valores[$indice_medio - 1] + $valores[$indice_medio])/2;
	}
	public static function medianaPercentilDesplazada(array $valores, float $desplazamiento = 0.0): float {
		$cantidad = count($valores);
		sort($valores, SORT_NUMERIC);
		$desplazamiento = max(-0.5, min(0.5, $desplazamiento));
		$p = 0.5 + $desplazamiento;
		$pos = $p * ($cantidad - 1);
		$lo  = (int) floor($pos);
		$hi  = (int) ceil($pos);
		if ($lo === $hi)
			return (float) $valores[$lo];
		$frac = $pos - $lo;
		return (float) ($valores[$lo] + $frac * ($valores[$hi] - $valores[$lo]));

	}
	public static function obtenerPercentil(array $valores, float $valor){
		$cantidad = count($valores);
		sort($valores, SORT_NUMERIC);
		for($i = 0; $i < $cantidad; $i++){
			if($valores[$i] === $valor)
				break;
		}
		if($i === $cantidad && $valor !== $valores[$cantidad-1])
			throw new LogicException('El valor no esta en la lista');
		return $i/$cantidad;
	}
}
