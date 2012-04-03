<?php
/**
 * Fichero de clase para campo tipo ENUM
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_FLDenumbdtext extends tablon_FLDenumbd {


	protected $flagForSearch = false;

	public function getType() {
		return "text";
	}

	/* Aunque el resto de parámetros además de $value no se usan, hay que ponerlas por ser una clase que extiende a otra (ENUMBD) */
	public function drawTableValueEdit($value,$clone=false,$disabled=false) {
		if ($this->flagForSearch) {
			return parent::drawTableValueEdit($value,$clone,$disabled);
		}
		$this->setvalue($value);

		if (!$this->loadKeysValues()) return '->'.false;
		$nKeys = sizeof($this->keys);
		$selected = (($selectedValue = $this->getValue())!==false)? $this->getValue():$this->getDefault();

		if (isset($this->keys[$selected])) $value = $this->values[$selected];
		else $value = "";

		$clase = "class = 'enumbdtext'";
		if(isset($this->conf['tab']) && isset($this->conf['fld'])) {
			return '<input type="text'
			       . '" id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
			       . '" name="'.$this->getSQLFLD()
			       . '" '.$clase
			       . ' value="'.$value
			       . '" autocompletetab="'.$this->conf['tab']
			       . '" autocompletefield = "'.$this->conf['fld']
			       . '" />';
		} else {
			return '<input type="text'
                   . '" id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
                   . '" name="'.$this->getSQLFLD()
                   . '" value="'.$value.'"  />';
		}

	}

	/*
	* Devolvemos el ID
	*/
	public function getMysqlValue($value) {
		$con = new con("select 1"); // Abrir el enlace, ya que real_escape_string tira de servidor
		$sql = "select ".$this->conf['id']." as id from ".$this->conf['tab']." where ".$this->conf['fld']."='".mysql_real_escape_string($value)."'";
		$c = new con($sql);
		if ($c->error()) {
			iError::error($c->getError());
			return array(false,$c->getError());
		}

		// La palabra ya existe, devuelve el ID.
		if ($c->getNumRows()==1) {
			$r = $c->getResult();
			$this->setValue($r['id']);
			return $r['id'];
		}
	if (isset($this->conf['noinsert'])) {
		return array("404","El campo ".$this->getTitle()." no existe.");
	}

	$aFields = array();
        $fValues = array();

        $aFields[] = $this->conf['fld'];
        $fValues[] = "'".mysql_real_escape_string($value)."'";

        if($clean = isset($this->conf["tabclean"])?$this->conf["tabclean"]:false){
            $aFields[] = $clean;
            $aClean = $this->toCleanUnique();
			if($aClean!==false && is_array($aClean)){
				$cleanValue = $this->doClean($clean,$value,false,false,$aClean);
			}else{
				$cleanValue = $this->doClean($clean,$value);
			}
            //$cleanValue = $this->doClean($clean,$value);
            if ($cleanValue === false) {
                return array("99","Error with clean field [$clean => ".$this->fields[$i]->getSQLFLD()."]");
            }
            if(empty($cleanValue) || $cleanValue == "''" || $cleanValue == ''){
                $cleanValue = $this->valueOnEmpty($cleanValue);
            }
            $fValues[] = "'".$cleanValue."'";
        }

        $sql = "insert into ".$this->conf['tab']." (".implode(",",$aFields).") values(".implode(",",$fValues).")";
		$c = new con($sql);
		if ($c->error()) {
			iError::error($c->getError());
			return array(false,$c->getError());
		}

		$__id = $c->getId();
		$this->setValue($__id);
		return $__id;
	}

    protected function doClean($c,$v,$id=false,$idc=false,$cleanUniqueFlds=false){
        $cont=0;
        $v = i::clean($v);
        while ($this->existe($c,$v,$id,$idc,$cleanUniqueFlds)) {
            $sql = "select ".$c." as url from ".$this->conf["tab"]." where ".$c." = '".$v."' ";
            $con = new con($sql);
            if ($con->error()) return false;
            $r=$con->getResult();
            $a = explode('_',$r['url']);
            $z = $a[(sizeof($a)-1)];
            if (is_numeric($z)){
                $x = "_".$z;
                $xx ="_".$cont;
                $v = str_replace($x,$xx,$v);
            }else{
                $v = $v."_".($cont);
            }
            $cont++;
        }
        return $v;
    }

    protected function existe($c,$v,$id=false,$idc=false,$cleanUniqueFlds=false) {
    	if($cleanUniqueFlds!==false && is_array($cleanUniqueFlds)){
			$cond = array();
			$cond2 = array();
			for($i=0;$i<count($cleanUniqueFlds);$i++){
				$cond[] = $cleanUniqueFlds[$i]."='".$v."'";
				if ( $id!==false && $idc!==false && $cleanUniqueFlds[$i]!=$c){
					$cond2[] = $cleanUniqueFlds[$i]."='".$v."'";
				}
			}
			if ( $id!==false && $idc!==false ){
				$sql = "select ".$c.",".$id." from ".$this->conf["tab"]." where
				((".implode(" or ",$cond).") and ".$idc." != '".$id."') or
				((".implode(" or ",$cond2).") and ".$idc." = '".$id."')";
			}else{
				$sql = "select ".$c.",".$id." from ".$this->tab." where (".implode(" or ",$cond).") ";
			}
		}else{
			$sql = "select ".$c." from ".$this->conf["tab"]." where ".$c." = '".$v."' ";
			if ( $id!==false && $idc!==false ) $sql.= " and ".$idc." != '".$id."'";
		}
		$con = new con($sql);
		return ($con->getNumRows()!==0);
    }



	public function processReturnJSONValue($v,$pl,$id,$edit=false) {
		return $v;
	}

	public function drawTableValue($v) {
		$this->loadKeysValues();
		if (($idx = array_search($v,$this->keys)) !== false) {
			return $this->values[$idx];
		}
		else return $v;
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
		if(isset($this->conf['tab']) && isset($this->conf['showfield'])){
			$aRet['autocompletetab'] = $this->conf['tab'];
			$aRet['autocompletetabconds'] = $this->conf['tabconds'];
			$aRet['autocompletefield'] = $this->conf['showfield'];
		}
		return json_encode($aRet);
	}

	public function setEditForSearch($val = true) {
		$this->flagForSearch = $val;
	}
}

?>
