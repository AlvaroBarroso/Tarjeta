<?php
namespace TarjetaMovi;


class Colectivo extends Transporte{
	function __construct($numero){
		$this->numero = $numero;
		$this->tipo = "Colectivo";
	}
}


?>