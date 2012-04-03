<?php
/**
 * Fichero de clase para campo tipo COLOR
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_FLDcolor extends tablon_FLD {
	public function getSQLType() {
		return "varchar(100)".$this->getSQLRequired();
	}


	public function getType() {
		return "text";
	}

	public function drawTableValue($value) {
		if($this->entitify){
			$value = htmlentities($value);
		}
		$accionEjec = 'select';
		if (($rFunc = $this->getTrigger($value,$accionEjec))!==false){
			if(in_array($accionEjec,$this->getTriggerOn())){
				$funcionTrigger = $rFunc.';';
				eval("\$value = $funcionTrigger");
				return $value;
			}
		}
		if(empty($value) && $value!="0" && $value!=0) return "";
		$value = $value."&nbsp;&nbsp;<span style='background-color:".$value."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
		return $value;
	}

	public function drawTableValueE($value) {
		if($this->entitify){
			$value = htmlentities($value);
		}
		$accionEjec = 'select';
		if (($rFunc = $this->getTrigger($value,$accionEjec))!==false){
			if(in_array($accionEjec,$this->getTriggerOn())){
				$funcionTrigger = $rFunc.';';
				eval("\$value = $funcionTrigger");
				return $value;
			}
		}
		if(empty($value) && $value!="0" && $value!=0) return "";
		return $value;
	}

	/* Aunque el resto de parámetros además de $value no se usan, hay que ponerlas por ser una clase que extiende a otra (fld) */
	public function drawTableValueEdit($value,$clone=false,$disabled=false) {
		$this->setValue($value);
		$html='<input type="'.$this->getType()
               . '" id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
		       . '" name="'.$this->getSQLFLD().(($clone)? '_clone':'').'" class="hexcolor" value="';
		if($this->getValue() !== false){
			$html.=$this->drawTableValueE($this->getValue());
		}
		$html.='" />';
	 	return $html;
	}

	public function drawEditJSON() {
		//$this->setValue($value);
		//$aRet['datef'] = $this->getdateformat(1);
		$aRet['name'] = $this->getSQLFLD();
		$aRet['theclass'] = "hexcolor";
		if(isset($this->conf['req']) && $this->conf['req'] == "1"){
			$aRet['req']=true;
		}
		return json_encode($aRet);
	}

	public function loadJS(){
		$js[] = "../modules/tablon/scripts/colorpicker.js";
		return $js;
	}

	public function getCl() {
		//var_dump($this->conf);
		return "hexcolor";
	}

}

?>