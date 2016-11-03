<?php
namespace TarjetaMovi;


class MedioBoleto extends TarjetaMovi implements tarjeta{
	function __construct($id){
		parent :: __construct($id);
		$this->boletoColectivo = 4.25;
		$this->trasbordo = 1.32;
		$this->tipo = "Medio";
	}
}



?>