<?php
namespace TarjetaMovi;


abstract class Transporte{
	protected $tipo, $numero;
	function returnTipo(){
		return $this->tipo;
	}
	function numero(){
		return $this->numero;
	}
}

?>