<?php
/**
 * Conector a base de datos MySQL
 * 
 * 
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

//if(defined("RUTA_PLANTILLAS"))
//define("RUTA_PLTS",dirname(__FILE__)."/../../../configuracion/tablon/");

class estadisticas_tablon extends tablon{
	public $aJs = array(
		"../modules/estadisticas/scripts/estadisticas.js"
	);
	
	private function drawDst(){
		$drawDst = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n
			<chart>\n
			<title>Gráfica llamadas Salientes</title>\n
			<name_value1>DST</name_value1>\n
			<type>pie</type>\n
			<header_height>20</header_height>\n
			<chart_data>\n
		";
		
		/*$cont = new con("SELECT max(id) FROM cdr_callcenter");
		$cont = $cont->getResult();
		
		foreach($cont as $cont => $u) $max = $u;
		
		$cont2 = new con("SELECT min(id) FROM cdr_callcenter");
		$cont2 = $cont2->getResult();
		
		foreach($cont2 as $cont2 => $i)$id = $i;*/
		
		$elementos = array();
		$con = new con("SELECT dst FROM cdr_callcenter");
		if($con->getNumRows()>0){
			while($r = $con->getResult()) {
				//$con3 = new con("SELECT dst FROM cdr_callcenter WHERE id='".$id."'");
				//$con3 = $con3->getResult();
				$dst = $r['dst'];
				
				if(!isset($elementos[$dst])) $elementos[$dst] = 1;
				else $elementos[$dst]++;		
				//$id++;
			}
		}
		
		foreach ($elementos as $elem => $num) {
			$drawDst .= "<piece>\n
				<name>".$elem."</name>\n
				<value1>".$num."</value1>\n
				</piece>\n
			";
		}
		
		$drawDst .= "</chart_data>\n
			<colours>\n
			<colour>0xDF0101</colour>\n
			<colour>0xDF7401</colour>\n
			<colour>0xD7DF01</colour>\n
			<colour>0x74DF00</colour>\n
			<colour>0x01DF01</colour>\n
			<colour>0x01DF74</colour>\n
			<colour>0x01DFD7</colour>\n
			<colour>0x0174DF</colour>\n
			<colour>0x0101DF</colour>\n
			<colour>0x7401DF</colour>\n
			<colour>0xDF01D7</colour>\n
			<colour>0xDF0174</colour>\n
			</colours>\n
			</chart>\n
		";
		return $drawDst;
	}
	
	private function _getPlt($sec) {
		if (!file_exists(RUTA_PLTS.$this->conf[$sec]['plt'])) return false;
		return RUTA_PLTS.$this->conf[$sec]['plt'];
	}
	
	public function draw() {
		if (!$plantilla = $this->getPlt()) {
			iError::error("Plantilla no encontrada");
			return false;
		}
		$this->plantillaPrincipal = new tablon_plantilla($plantilla);
		//var_dump($this);
		echo "<br/>***************************<br/>";
		var_dump(session_get_cookie_params());
		echo "<br/>***************************<br/>";
	}
}
	

