<?php

namespace TarjetaMovi;

use PHPUnit\Framework\TestCase;

class TarjetaMoviTest extends TestCase{
	protected $bici, $colectivo144Negro, $colectivo128Rojo, $tarjeta, $medio, $pase;


	public function setup(){
		$this->bici = new Bici(9424);
		$this->colectivo144Negro = new Colectivo('144 Negro');
		$this->colectivo128Rojo = new Colectivo('128 Rojo');
		
		$this->tarjeta = new TarjetaMovi(11);
		$this->medio = new MedioBoleto(22);
		$this->pase = new Pase(33);
	}

	public function testBici(){
		// Bici
		$this->assertEquals($this->bici->numero(), 9424 , "Número de bicicleta");
		$this->assertEquals($this->bici->returnTipo(), "Bicicleta" , "Tipo Bicicleta");
	}
	




?>

