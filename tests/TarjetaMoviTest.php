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

	public function testColectivo(){
		// 128 Rojo
		$this->assertEquals($this->colectivo128Rojo->numero(), '128 Rojo' , "Número de colectivo");
		$this->assertEquals($this->colectivo128Rojo->returnTipo(), "Colectivo" , "Tipo Colectivo");


		// 144 Negro
		$this->assertEquals($this->colectivo144Negro->numero(), '144 Negro' , "Número de colectivo");
		$this->assertEquals($this->colectivo144Negro->returnTipo(), "Colectivo" , "Tipo Colectivo");
	}


	public function testTarjetaMovi(){
		// Valores iniciales
		$this->assertEquals($this->tarjeta->credito, 0, "Crédito inicial tarjeta");
		$this->assertEquals($this->tarjeta->boletoColectivo, 8.5, "Valor del boleto con tarjeta");
		$this->assertEquals($this->tarjeta->trasbordo, 2.64, "Valor del trasbordo con tarjeta");
		$this->assertEquals($this->tarjeta->boletoBici, 12.5, "Valor de la bici con tarjeta");


		// Saldo
		$this->tarjeta->recargar(8.5);
		$this->assertEquals($this->tarjeta->saldo(), 8.5, "Fúnción saldo");
		
		// Primer Viaje
		$this->assertEquals($this->tarjeta->pagar($this->colectivo144Negro, '30-10-2016 19:04'), [11, "Normal", "144 Negro", '30-10-2016 19:04', 0], "Primer viaje");
		$this->assertEquals($this->tarjeta->credito, 0, "Crédito primer viaje");
		
		


		// PlusHistorial [id, tipo de viaje, numero de transporte, fecha, credito restante]
		$this->assertEquals($this->tarjeta->pagar($this->colectivo144Negro, '1-11-2016 19:04'), [11, "Plus", "144 Negro", '1-11-2016 19:04', -8.5], "Primer Plus");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo144Negro, '1-11-2016 22:06'),[11, "Plus", "144 Negro", '1-11-2016 22:06', -17.0], "Segundo Plus");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo144Negro, '1-11-2016 23:50'), "No quedan viajes Plus", "Sin Plus disponible");
		
		
		// Crédito insuficiente
		$this->assertEquals($this->tarjeta->pagar($this->bici, '24-05-2016 13:04'), "Crédito insuficiente", "Bicicleta sin crédito");
		
		$this->assertEquals($this->tarjeta->pagar($this->colectivo144Negro, '4-11-2016 19:04'), "No quedan viajes Plus", "Pasaje Normal");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo128Rojo, '4-11-2016 19:44'), "No quedan viajes Plus", "Pasaje Normal");
		
		$this->assertEquals($this->tarjeta->pagar($this->colectivo144Negro, '5-11-2016 7:04'), "No quedan viajes Plus", "Pasaje Normal");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo128Rojo, '5-11-2016 7:44'), "No quedan viajes Plus", "Pasaje Normal");
		
		$this->assertEquals($this->tarjeta->pagar($this->colectivo144Negro, '5-11-2016 19:04'), "No quedan viajes Plus", "Pasaje Normal");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo128Rojo, '5-11-2016 19:44'), "No quedan viajes Plus", "Pasaje Normal");
		
		$this->assertEquals($this->tarjeta->pagar($this->colectivo144Negro, '6-11-2016 19:04'), "No quedan viajes Plus", "Pasaje Normal");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo128Rojo, '6-11-2016 19:44'), "No quedan viajes Plus", "Pasaje Normal");
		
		$this->assertEquals($this->tarjeta->pagar($this->colectivo144Negro, '5-11-2016 23:04'), "No quedan viajes Plus", "Pasaje Normal");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo128Rojo, '5-11-2016 23:44'), "No quedan viajes Plus", "Pasaje Normal");
		

		// Recarga
		$this->tarjeta->recargar(17);
		$this->assertEquals($this->tarjeta->saldo(), 0, "Función saldo");
		
		$this->tarjeta->recargar(40);
		$this->assertEquals($this->tarjeta->credito, 40, "Recargar 40 tarjeta");

		$this->tarjeta->recargar(272);
		$this->assertEquals($this->tarjeta->credito, 360, "Recargar 272 tarjeta");

		$this->tarjeta->recargar(320);
		$this->assertEquals($this->tarjeta->credito, 860, "Recargar 320 tarjeta");

		// Pagar
		$this->tarjeta->pagar($this->bici, '2-11-2016 13:04');
		$this->assertEquals($this->tarjeta->credito, 847.5, "Alquilar bici con tarjeta");

		$this->tarjeta->pagar($this->colectivo144Negro, '4-11-2016 13:04');
		$this->assertEquals($this->tarjeta->credito, 839, "Colectivo 1 con tarjeta");
		
		$this->tarjeta->pagar($this->colectivo128Rojo, '4-11-2016 13:44');
		$this->assertEquals($this->tarjeta->credito, 836.36, "Trasbordo lunes a viernes");


		$this->tarjeta->recargar(3.64);

		$this->tarjeta->pagar($this->colectivo144Negro, '5-11-2016 6:04');
		$this->assertEquals($this->tarjeta->credito, 831.5, "Colectivo 2 con tarjeta");
		$this->tarjeta->recargar(8.5);
		$this->tarjeta->pagar($this->colectivo128Rojo, '5-11-2016 6:44');
		$this->assertEquals($this->tarjeta->credito, 837.36, "Trasbordo sabado 6 a 14");

		$this->tarjeta->recargar(2.64);

		$this->tarjeta->pagar($this->colectivo144Negro, '5-11-2016 14:04');
		$this->assertEquals($this->tarjeta->credito, 831.5, "Colectivo 2 con tarjeta");
		$this->tarjeta->recargar(8.5);
		$this->tarjeta->pagar($this->colectivo128Rojo, '5-11-2016 14:44');
		$this->assertEquals($this->tarjeta->credito, 837.36, "Trasbordo sabado 14 a 22");		

		$this->tarjeta->recargar(2.64);

		$this->tarjeta->pagar($this->colectivo144Negro, '6-11-2016 6:04');
		$this->assertEquals($this->tarjeta->credito, 831.5, "Colectivo 2 con tarjeta");
		$this->tarjeta->recargar(8.5);
		$this->tarjeta->pagar($this->colectivo128Rojo, '6-11-2016 6:44');
		$this->assertEquals($this->tarjeta->credito, 837.36, "Trasbordo domingo 6 a 22");


		$this->tarjeta->recargar(2.64);

		$this->tarjeta->pagar($this->colectivo144Negro, '6-11-2016 22:04');
		$this->assertEquals($this->tarjeta->credito, 831.5, "Colectivo 2 con tarjeta");
		$this->tarjeta->recargar(8.5);
		$this->tarjeta->pagar($this->colectivo128Rojo, '6-11-2016 22:44');
		$this->assertEquals($this->tarjeta->credito, 837.36, "Trasbordo noche 22 a 6");


		
		// Viajes realizados
		$this->assertEquals($this->tarjeta->viajesRealizados()[1], [11, "Normal", "144 Negro", '30-10-2016 19:04', 0], "Viajes realizados");

	}

	public function testMedioBoleto(){
		// Valores iniciales
		$this->assertEquals($this->medio->credito, 0, "Crédito inicial medio");
		$this->assertEquals($this->medio->boletoColectivo, 4.25, "Boleto medio");
		$this->assertEquals($this->medio->trasbordo, 1.32, "Trasbordo medio");
		$this->assertEquals($this->medio->boletoBici, 12.5, "Bici medio");

		/*

		// Recarga
		$this->medio->recargar(40);
		$this->assertEquals($this->medio->credito, 40, "Recargar 40 medio");

		$this->medio->recargar(272);
		$this->assertEquals($this->medio->credito, 360, "Recargar 272 medio");

		$this->medio->recargar(320);
		$this->assertEquals($this->medio->credito, 860, "Recargar 320 medio");
	


		// Pagar
		$this->medio->pagar($this->bici, '24-05-2016 13:04');
		$this->assertEquals($this->medio->credito, 847.5, "Alquilar bici con medio");

		$this->medio->pagar($this->colectivo144Negro, '24-05-2016 13:04');
		$this->assertEquals($this->medio->credito, 843.25, "Colectivo con medio 1");
		
		$this->medio->pagar($this->colectivo144Negro, '24-05-2016 13:44');
		$this->assertEquals($this->medio->credito, 841.85, "Trasbordo con medio");

		$this->medio->pagar($this->colectivo144Negro, '24-05-2016 15:04');
		$this->assertEquals($this->medio->credito, 837.60, "Colectivo con medio 2");
		*/
	}

	public function testPase(){
		// Valores iniciales
		$this->assertEquals($this->pase->credito, 0, "Crédito pase");
		$this->assertEquals($this->pase->boletoColectivo, 0, "Boleto colectivo pase");
		$this->assertEquals($this->pase->trasbordo, 0, "Trasbordo pase");
		$this->assertEquals($this->pase->boletoBici, 0, "Bici pase");

		// Saldo
		$this->assertEquals($this->pase->saldo(), 0, "Función saldo");

		// Recarga
		$this->assertEquals($this->pase->recargar(40), "No se le pueden hacer recargas" , "Recargar pase");

		// Pagar
		$this->assertEquals($this->pase->pagar($this->colectivo144Negro, '24-05-2016 15:04'), "Sin cobro", "Pagar con pase");

	}


} 





?>

