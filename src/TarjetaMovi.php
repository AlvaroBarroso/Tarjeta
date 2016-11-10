<?php
namespace TarjetaMovi;


interface Tarjeta{
	public function pagar(Transporte $transporte, $fecha_y_hora);
	public function recargar($monto);
	public function saldo();
	public function viajesRealizados();
}

class TarjetaMovi{
	public $id, $credito, $historial, $contadorViajes, $boletoColectivo, $boletoBici, $trasbordo, $tipo, $viajesPlus;
	function __construct($id){
		$this->id = $id;
		$this->credito = 0;
		$this->historial = array();
		$this->contadorViajes = 0;
		$this->boletoColectivo = 8.50;
		$this->trasbordo = 2.64;
		$this->boletoBici = 12.50;
		$this->tipo = "Normal";
		$this->viajesPlus = 0;
	}
	
	function saldo(){
		return $this->credito;
	}

	function recargar($monto){
		if($monto == 272){
			$this->credito += 320;
		}
		else if($monto == 320){
			$this->credito += 500;
		}
		else{
			$this->credito += $monto;
		}

		if($this->credito >= 0){
			$this->viajesPlus = 0;
		}
	}

	function pagar(Transporte $transporte, $date){ // Historial [id, tipo de viaje, numero de transporte, fecha, credito restante]
		// Colectivo
		if($transporte->returnTipo() == "Colectivo"){
			// Más de un viaje -> Posibilidad de Trasbordo
			if ($this->contadorViajes != 0){
				// Lunes a viernes 6 a 22 
				if( date("N", strtotime($date)) <= 5 && date("G", strtotime($date)) >= 6 && date("G", strtotime($date)) < 22){
					// Viaje antes de 1 hora
					if( (strtotime($date) - strtotime($this->historial[$this->contadorViajes][3])) / 3600 < 1){	
						// Diferente colectivo
						if($this->historial[$this->contadorViajes][2] != $transporte->numero()){
							// Credito Suficiente para el trasbordo
							if($this->credito >= $this->trasbordo){				
								$this->credito -= $this->trasbordo;
								$this->agregarAlHistorial($this->id, "Trasbordo" , $transporte->numero(), $date, $this->credito);

								return $this->historial[$this->contadorViajes];
							}
							// Crédito insuficiente para el trasbordo
							else{
								#return $this->plus($transporte, $date);
							}
						}
					}
				}
				
				// Sábados 6 a 14
				if ( date("N", strtotime($date)) == 6 && date("G", strtotime($date)) >= 6 && date("G", strtotime($date)) < 14){
					// Viaje antes de 1 hora
					if( (strtotime($date) - strtotime($this->historial[$this->contadorViajes][3])) / 3600 < 1){					
						// Diferente colectivo
						if($this->historial[$this->contadorViajes][2] != $transporte->numero()){				
							// Credito Suficiente para el trasbordo
							if($this->credito >= $this->trasbordo){				
								$this->credito -= $this->trasbordo;
								$this->agregarAlHistorial($this->id, "Trasbordo" , $transporte->numero(), $date, $this->credito);

								return $this->historial[$this->contadorViajes];
							}
							// Crédito insuficiente para el trasbordo
							else{
								return $this->plus($transporte, $date);
							}
						}
					}
				}
				
				// Sábados de 14 a 22
				if ( date("N", strtotime($date)) == 6 && date("G", strtotime($date)) >= 14 && date("G", strtotime($date)) < 22){
					// Viaje antes de 1:30 hora
					if( (strtotime($date) - strtotime($this->historial[$this->contadorViajes][3])) / 5400 < 1){					
						// Diferente colectivo
						if($this->historial[$this->contadorViajes][2] != $transporte->numero()){				
							// Credito Suficiente para el trasbordo
							if($this->credito >= $this->trasbordo){				
								$this->credito -= $this->trasbordo;
								$this->agregarAlHistorial($this->id, "Trasbordo" , $transporte->numero(), $date, $this->credito);

								return $this->historial[$this->contadorViajes];
							}
							// Crédito insuficiente para el trasbordo
							else{
								return $this->plus($transporte, $date);
							}
						}
					}
				}	
				
				// Domingos de 6 a 22
				if ( date("N", strtotime($date)) == 7 && date("G", strtotime($date)) >= 6 && date("G", strtotime($date)) < 22){
					// Viaje antes de 1:30 hora
					if( (strtotime($date) - strtotime($this->historial[$this->contadorViajes][3])) / 5400 < 1){					
						// Diferente colectivo
						if($this->historial[$this->contadorViajes][2] != $transporte->numero()){				
							// Credito Suficiente para el trasbordo
							if($this->credito >= $this->trasbordo){				
								$this->credito -= $this->trasbordo;
								$this->agregarAlHistorial($this->id, "Trasbordo" , $transporte->numero(), $date, $this->credito);

								return $this->historial[$this->contadorViajes];
							}
							// Crédito insuficiente para el trasbordo
							else{
								return $this->plus($transporte, $date);
							}
						}
					}
				}
				// Noche 
				if( date("G", strtotime($date)) >= 22 || date("G", strtotime($date)) < 6){
					if( (strtotime($date) - strtotime($this->historial[$this->contadorViajes][3])) / 5400 < 1){					
						// Diferente colectivo
						if($this->historial[$this->contadorViajes][2] != $transporte->numero()){				
							// Credito Suficiente para el trasbordo
							if($this->credito >= $this->trasbordo){				
								$this->credito -= $this->trasbordo;
								$this->agregarAlHistorial($this->id, "Trasbordo" , $transporte->numero(), $date, $this->credito);

								return $this->historial[$this->contadorViajes];
							}
							// Crédito insuficiente para el trasbordo
							else{
								return $this->plus($transporte, $date);
							}
						}
					}
					else{
						if($this->credito >= $this->boletoColectivo){
							$this->credito -= $this->boletoColectivo;
							$this->agregarAlHistorial($this->id, $this->tipo, $transporte->numero(), $date, $this->credito);

							return $this->historial[$this->contadorViajes];
						}
						// Crédito insuficiente para el boleto
						else{
							echo "plus\n";
							return $this->plus($transporte, $date);
						}
					}
				}	

				// Boleto normal
				else{
					// Crédito suficiente para el pasaje
					if($this->credito >= $this->boletoColectivo){
						$this->credito -= $this->boletoColectivo;
						$this->agregarAlHistorial($this->id, $this->tipo, $transporte->numero(), $date, $this->credito);

						return $this->historial[$this->contadorViajes];
					}
					// Crédito insuficiente para el boleto
					else{
						return $this->plus($transporte, $date);
					}
				}
			}
			// Primer Viaje
			else{
				// Crédito suficiente para el pasaje
				if($this->credito >= $this->boletoColectivo){
					$this->credito -= $this->boletoColectivo;
					$this->agregarAlHistorial($this->id, $this->tipo, $transporte->numero(), $date, $this->credito);

					return $this->historial[$this->contadorViajes];
				}
				// Crédito insuficiente para el boleto
				else{
					return $this->plus($transporte, $date);
				}
			}
		}
		// Bicicleta
		else if ($transporte->returnTipo() == "Bicicleta"){
			// Crédito suficiente para la bicicleta
			if($this->credito >= $this->boletoBici){
				$this->credito -= $this->boletoBici;
				$this->agregarAlHistorial($this->id, "Bicicleta", $transporte->numero(), $date, $this->credito);

				return $this->historial[$this->contadorViajes];
			}
			// Crédito insuficiente para la bicicleta
			else{
				return "Crédito insuficiente";
			}
		}
	}


	protected function plus($transporte, $date){
		if($this->viajesPlus < 2){
			$this->credito -= $this->boletoColectivo;
			$this->viajesPlus++;
			$this->agregarAlHistorial($this->id, "Plus", $transporte->numero(), $date, $this->credito);

			return $this->historial[$this->contadorViajes];
		}
		else{
			return "No quedan viajes Plus";
		}
	}



	// Historial [id, tipo de viaje, numero de transporte, fecha, credito restante]	
	protected function agregarAlHistorial($id,$tipo,$numero, $fecha,$credito){
		$this->contadorViajes++;
		$this->historial[$this->contadorViajes] = [$id, $tipo, $numero, $fecha, $credito];
	}
	

	function viajesRealizados(){
		return $this->historial;
	}
}

?>
