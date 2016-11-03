<?php
namespace TarjetaMovi;


class Pase extends TarjetaMovi{
	function __construct($id){
		parent :: __construct($id);
		$this->boletoColectivo = 0;
		$this->boletoBici = 0;
		$this->trasbordo = 0;
	}
	function saldo(){
		return $this->credito;
	}
	function recargar($monto){
		return "No se le pueden hacer recargas";
	}
	function pagar(Transporte $transporte, $date){
		return "Sin cobro";
	}
}





?>