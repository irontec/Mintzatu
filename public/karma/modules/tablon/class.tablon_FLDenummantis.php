<?php
/**
 * Fichero de clase para campo tipo ENUM
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_FLDenummantis extends tablon_FLDenum {
	public $type="select";
	private $keys = array();
	private $values = array();

	var $json=false;

	public function getMysqlValue($value) {

		$c = new con("select 1;");
		$v=tablon_FLD::cleanMySQLValue($value);
		$this->setValue($v);
		if($this->nullable && $v == "NULL"){
			return tablon_FLD::cleanMySQLValue($this->getValue());
		}
		return '\''.tablon_FLD::cleanMySQLValue($this->getValue()).'\'';
	}

	public function getSQLType() {
		return "mediumint unsigned" . $this->getSQLRequired() . $this->getSQLDefaultValue();
	}

	private function loadKeysValues($a=false) {
		if ($this->loaded) return true;

		if(isset($this->nullable) && $this->nullable !==false){
			$this->keys[] = "NULL";
			$this->values[] = $this->nullable;
		}

		$client = new SoapClient($this->conf['soapurl']);

		$mc_projects = $client->mc_projects_get_user_accessible($this->conf['user'], $this->conf['pass']);
		foreach ($mc_projects as $project)
		{
			$this->keys[] = $project->id;
			$this->values[] = $project->name;
		}

		$this->loaded = true;
		return true;
	}

	public function drawEditJSON() {
		$this->json=true;
		if (!$this->loadKeysValues()) return false;
		$nKeys = sizeof($this->keys);
		$aRet = array();
		for($i=0;$i<$nKeys;$i++) {
			$aRet[$this->keys[$i]] = $this->values[$i];
		}
		$selected = (($selectedValue = $this->getValue())!==false)? $this->getValue():$this->getDefault();
		/*PUEDE QUE BUG*/
		if ($selected !== false ) $aRet['selected'] = $selected;
		if(isset($this->conf['req']) && $this->conf['req'] == "1"){
			$aRet['req']=true;
		}
		return json_encode($aRet);
	}

	public function drawTableValue($value) {
		$this->json=true;
		if(empty($value)){
			if(isset($this->conf['nullable']) && $this->conf['nullable']!==false && isset($this->conf['defaultKey']) && $this->conf['defaultKey'] == "__NULL"){
				return $this->conf['nullable'];
			}
		}

		if ($value==NULL){
			return false;
		}
		$this->keys = array();
		$this->values = array();
		$this->loaded=false;
		if (!$this->loadKeysValues($value)) return false;
		$size_values = sizeof($this->keys);
		for($i=0;$i<$size_values;$i++) {
			if ($this->keys[$i]==$value) return $this->values[$i];
		}

		return false;
	}

	/* Aunque el resto de parámetros además de $value no se usan, hay que ponerlas por ser una clase que extiende a otra (ENUM) */
	public function drawTableValueEdit($value,$clone=false,$disabled=false) {
		$this->setvalue($value);
		if (!$this->loadKeysValues()) return false;
		$nKeys = sizeof($this->keys);
		$selected = (($selectedValue = $this->getValue())!==false)? $this->getValue():$this->getDefault();
		if ($selected !== false ) $aRet['selected'] = $selected;
		$dependencia = false;
		$clase = array();
		$strclase = "";
		if(isset($this->conf['dependencia']) && $this->conf['dependencia']!= '0'){
			$dependencia = true;
			$clase[] = "condicionante";
		}

        if (isset($this->conf['autocomplete']) && $this->conf['autocomplete'] != '0') {
            $clase[] = 'autocomplete';
        }


		if($this->isRequired()) $clase[] = "required";
		if(sizeof($clase)>0) $strclase = 'class = "'.implode(" ",$clase).'"';

		$ret = '<select name="'.$this->getSQLFLD().'" '.$strclase.'>';
		foreach($this->keys as $k=>$v){
			if($dependencia && isset($this->conf['grupodependiente_'.$v])){
				$ret .= '<option id = "'.$this->conf['grupodependiente_'.$v].'" value="'.$this->keys[$k].'"'.(($this->keys[$k]==$selected)? 'selected="selected"':'').'>'.$this->values[$k].'</option>';
			}else{
				$ret .= '<option value="'.$this->keys[$k].'"'.(($this->keys[$k]==$selected)? ' selected="selected"':'').'>'.$this->values[$k].'</option>';
			}
		}
		$ret .= '</select>';
		return $ret;
	}

	public function processReturnJSONValue($v,$pl,$id,$edit=false) {
		$this->json=true;
		$this->keys = array();
		$this->values = array();
		$this->loaded=false;
		if (!$this->loadKeysValues($value)) return false;
		$size_values = sizeof($this->keys);
		for($i=0;$i<$size_values;$i++) {
			if ($this->keys[$i]==$v) return $this->values[$i];
		}
		return false;
	}
}

?>
