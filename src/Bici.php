<?php
namespace TarjetaMovi;


class Bici extends Transporte{
	function __construct($numero){
		$this->numero = $numero;
		$this->tipo = "Bicicleta";
	}
}

?>