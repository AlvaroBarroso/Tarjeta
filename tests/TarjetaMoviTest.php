<?php

namespace TarjetaMovi;

use PHPUnit\Framework\TestCase;

class TarjetaMoviTest extends TestCase{
	protected $bici, $colectivo145Rojo, $colectivo142Negro, $tarjeta, $medio, $pase;

	public function setup(){
		$this->bici = new Bici(24);
		$this->colectivo145Rojo = new Colectivo('145 Rojo');
		$this->colectivo142Negro = new Colectivo('142 Negro');
		
		$this->tarjeta = new TarjetaMovi(11);
		$this->medio = new MedioBoleto(22);
		$this->pase = new Pase(42);
	}

	public function testPase(){
		$this->assertEquals($this->pase->saldo(), 0, "Credito");
		$this->assertEquals($this->pase->recargar(69), "No se le pueden hacer recargas", "Recarga pase");

			$this->assertEquals($this->pase->pagar($this->colectivo145Rojo, '4-11-2016 19:04'), "Sin cobro", "No paga, pase");


	}
		//MiBiceTuBici
	public function testBici(){
		
		$this->assertEquals($this->bici->numero(), 24 , "ID bici");
		$this->assertEquals($this->bici->returnTipo(), "Bicicleta" , "Es del tipo Bicicleta");
	}

	public function testColectivo(){
		// 142 Negro
		$this->assertEquals($this->colectivo142Negro->numero(), '142 Negro' , "Número y color de colectivo");
		$this->assertEquals($this->colectivo142Negro->returnTipo(), "Colectivo" , "es del tipo Colectivo");


		// 145 Rojo
		$this->assertEquals($this->colectivo145Rojo->numero(), '145 Rojo' , "Número y color de colectivo");
		$this->assertEquals($this->colectivo145Rojo->returnTipo(), "Colectivo" , "es del tipo Colectivo");
	}

	public function testTarjetaMovi(){
		// check valores iniciales
		$this->assertEquals($this->tarjeta->credito, 0, "Crédito al iniciar tarjeta");
		$this->assertEquals($this->tarjeta->boletoColectivo, 8.5, "Valor boleto tarjeta");
		$this->assertEquals($this->tarjeta->trasbordo, 2.64, "Valor trasbordo tarjeta");
		$this->assertEquals($this->tarjeta->boletoBici, 12.5, "Valor bici tarjeta");


		// Saldo
		$this->tarjeta->recargar(8.5);
		$this->assertEquals($this->tarjeta->saldo(), 8.5, "Fúnción saldo");
		
		// Primer Viaje
		$this->assertEquals($this->tarjeta->pagar($this->colectivo145Rojo, '30-10-2016 19:04'), [11, "Normal", "145 Rojo", '30-10-2016 19:04', 0], "Primer viaje");
		$this->assertEquals($this->tarjeta->credito, 0, "Crédito primer viaje");
		
		


		// PlusHistorial [id, tipo de viaje, numero de transporte, fecha, credito restante]
		$this->assertEquals($this->tarjeta->pagar($this->colectivo145Rojo, '1-11-2016 19:04'), [11, "Plus", "145 Rojo", '1-11-2016 19:04', -8.5], "Primer Plus");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo145Rojo, '1-11-2016 22:06'),[11, "Plus", "145 Rojo", '1-11-2016 22:06', -17.0], "Segundo Plus");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo145Rojo, '1-11-2016 23:50'), "No quedan viajes Plus", "Sin Plus disponible");
		
		
		// Crédito insuficiente
		$this->assertEquals($this->tarjeta->pagar($this->bici, '24-05-2016 13:04'), "Crédito insuficiente", "Bicicleta sin crédito");
		
		$this->assertEquals($this->tarjeta->pagar($this->colectivo145Rojo, '4-11-2016 19:04'), "No quedan viajes Plus", "Pasaje Normal");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo142Negro, '4-11-2016 19:44'), "No quedan viajes Plus", "Pasaje Normal");
		
		$this->assertEquals($this->tarjeta->pagar($this->colectivo145Rojo, '5-11-2016 7:04'), "No quedan viajes Plus", "Pasaje Normal");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo142Negro, '5-11-2016 7:44'), "No quedan viajes Plus", "Pasaje Normal");
		
		$this->assertEquals($this->tarjeta->pagar($this->colectivo145Rojo, '5-11-2016 19:04'), "No quedan viajes Plus", "Pasaje Normal");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo142Negro, '5-11-2016 19:44'), "No quedan viajes Plus", "Pasaje Normal");
		
		$this->assertEquals($this->tarjeta->pagar($this->colectivo145Rojo, '6-11-2016 19:04'), "No quedan viajes Plus", "Pasaje Normal");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo142Negro, '6-11-2016 19:44'), "No quedan viajes Plus", "Pasaje Normal");
		
		$this->assertEquals($this->tarjeta->pagar($this->colectivo145Rojo, '5-11-2016 23:04'), "No quedan viajes Plus", "Pasaje Normal");
		$this->assertEquals($this->tarjeta->pagar($this->colectivo142Negro, '5-11-2016 23:44'), "No quedan viajes Plus", "Pasaje Normal");
		

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

		$this->tarjeta->pagar($this->colectivo145Rojo, '4-11-2016 13:04');
		$this->assertEquals($this->tarjeta->credito, 839, "Colectivo 1 con tarjeta");
		
		$this->tarjeta->pagar($this->colectivo142Negro, '4-11-2016 13:44');
		$this->assertEquals($this->tarjeta->credito, 836.36, "Trasbordo lunes a viernes");


		$this->tarjeta->recargar(3.64);

		$this->tarjeta->pagar($this->colectivo145Rojo, '5-11-2016 6:04');
		$this->assertEquals($this->tarjeta->credito, 831.5, "Colectivo 2 con tarjeta");
		$this->tarjeta->recargar(8.5);
		$this->tarjeta->pagar($this->colectivo142Negro, '5-11-2016 6:44');
		$this->assertEquals($this->tarjeta->credito, 837.36, "Trasbordo sabado 6 a 14");

		$this->tarjeta->recargar(2.64);

		$this->tarjeta->pagar($this->colectivo145Rojo, '5-11-2016 14:04');
		$this->assertEquals($this->tarjeta->credito, 831.5, "Colectivo 2 con tarjeta");
		$this->tarjeta->recargar(8.5);
		$this->tarjeta->pagar($this->colectivo142Negro, '5-11-2016 14:44');
		$this->assertEquals($this->tarjeta->credito, 837.36, "Trasbordo sabado 14 a 22");		

		$this->tarjeta->recargar(2.64);

		$this->tarjeta->pagar($this->colectivo145Rojo, '6-11-2016 6:04');
		$this->assertEquals($this->tarjeta->credito, 831.5, "Colectivo 2 con tarjeta");
		$this->tarjeta->recargar(8.5);
		$this->tarjeta->pagar($this->colectivo142Negro, '6-11-2016 6:44');
		$this->assertEquals($this->tarjeta->credito, 837.36, "Trasbordo domingo 6 a 22");


		$this->tarjeta->recargar(2.64);

		$this->tarjeta->pagar($this->colectivo145Rojo, '6-11-2016 22:04');
		$this->assertEquals($this->tarjeta->credito, 831.5, "Colectivo 2 con tarjeta");
		$this->tarjeta->recargar(8.5);
		$this->tarjeta->pagar($this->colectivo142Negro, '6-11-2016 22:44');
		$this->assertEquals($this->tarjeta->credito, 837.36, "Trasbordo noche 22 a 6");


		
		// Viajes realizados
		$this->assertEquals($this->tarjeta->viajesRealizados()[1], [11, "Normal", "145 Rojo", '30-10-2016 19:04', 0], "Viajes realizados");

	}



} 





?>

