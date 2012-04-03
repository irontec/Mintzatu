<?php
/**
 * Fichero de clase para campo tipo ENUM
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_FLDtime extends tablon_FLD {
	public function getSQLType() {
		return "time ".$this->getSQLRequired()." default 00:00:00";
	}

	public function getType() {
		return "text";
	}

	public function preInsertCheckValue(&$value) {
		if(!empty($value)){
			$c = new con("select 1;");
			$v=tablon_FLD::cleanMySQLValue($value);
			$this->setValue($v);
			//$partes = $this->gettimeparts($this->getValue());
			//if(!preg_match("/[0-2]/",$v)
			$aFormato = date_parse($v);
			/* Lo quito , no se si se una en algun logar
			if($aFormato['error_count']!=0 || !preg_match("/.{2}:.{2}:.{2}/",$v)){
				return array(1,"Formato de hora incorrecto. ".((isset($this->conf['desc']))? $this->conf['desc']:""));
			}*/
		}
		return true;
	}

	protected function gettimeparts($value) {
		$ds = isset($this->conf['separator'])? $this->conf['separator']:"-";
		$aParts = explode($ds,$value);
		for($i=sizeof($aParts);$i<3;$i++){
			$aParts[$i] = intval($aParts[$i]);
		}
		if(sizeof($aParts)<3){
			for($i=sizeof($aParts);$i<3;$i++){
				$aParts[$i] = 0;
			}
		}
		return $aParts;
	}


	public function drawTableValueEdit($value,$clone=false,$disabled=false) {
		$this->setValue($value);
		if($disabled == true){
			$strDis = ' disabled = "disabled" ';
		}else{
			$strDis = "";
		}
		$clase = "timepick";
		$clase.=(($clone)? ' clone':'');
		$clase.=((isset($this->conf['req']) && $this->conf['req'] == "1")?" required":"");
		$clase = "class = '".$clase."'";

		return '<input style="width: 60px;" type="'.$this->getType()
            . '" id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
    		. '" name="'.$this->getSQLFLD().(($clone)? '_clone':'')
    		. '" ' .$clase
    		. ' value="'.$this->drawTableValue($this->getValue()).'" />';
	}

	public function getCl() {
		//var_dump($this->conf);
		return "jtime";
	}
}

?>