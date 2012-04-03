<?php
/**
 * Fichero de clase para campo tipo ENUMBDSELECTOR
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_FLDenumbdselectorcreate extends tablon_FLDenumbd
{

	public function loadJS()
	{
		$js[] = "../modules/tablon/scripts/tiny_mce/tiny_mce.js";
		if (isset($this->conf['main']['tinyConf']) ) {
			$js[] = "../../configuracion/js/".$this->conf['main']['tinyConf'];
		} else {
			$js[] = "../modules/tablon/scripts/tiny_conf.js";
		}
		return $js;
	}

	public function getSQLType()
    {
		return
			"mediumint unsigned" . $this->getSQLRequired() . $this->getSQLDefaultValue()
		    . ",\n add index(".$this->getIndex().") "
			. ",\n add foreign key(".$this->getIndex().") references ".$this->conf['tab']
			. "(".$this->conf['id'].") on delete set null on update cascade\n";
	}

	public function drawEditJSON()
    {
		$this->json=true;
		if (!$this->loadKeysValues()) return false;
		$nKeys = sizeof($this->keys);
		$aRet = array();
		for ($i=0;$i<$nKeys;$i++) {
			$aRet[$this->keys[$i]] = $this->values[$i];
		}
		$selected = (($selectedValue = $this->getValue())!==false)? $this->getValue():$this->getDefault();
		/*PUEDE QUE BUG*/
		if ($selected !== false ) $aRet['selected'] = $selected;
		if (isset($this->conf['req']) && $this->conf['req'] == "1") {
			$aRet['req']=true;
		}
		return json_encode($aRet);
	}

	public function getOplt()
    {
		return $this->conf['oplt'];
	}

	public function drawTableValueEdit($value, $clone=false, $disabled=false)
    {
		$this->setvalue($value);
		if (!$this->loadKeysValues()) return false;
		$nKeys = sizeof($this->keys);
		$selected = (($selectedValue = $this->getValue())!==false)? $this->getValue():$this->getDefault();
		if ($selected !== false ) $aRet['selected'] = $selected;
		$dependencia = false;
		$clase = array();
		$strclase = "";
		if (isset($this->conf['dependencia']) && $this->conf['dependencia']!= '0') {
			$dependencia = true;
			$clase[] = "condicionante";
		}

		if (isset($this->conf['autocomplete']) && $this->conf['autocomplete'] != '0') {
            $clase[] = 'autocomplete';
        }

		if ($this->isRequired())	$clase[] = "required";
		if (sizeof($clase)>0) $strclase = 'class = "'.implode(" ", $clase).'"';

		$ret = '<select name="'.$this->getSQLFLD().'" '.$strclase.'>';
		if (isset($this->conf['grrel'])&&!$this->json) {

			if (!$this->loadGrKeysValues()) return false;

			foreach ($this->_gKeys as $kk=>$vv) {
				$ret .= "<optgroup label=\"".$this->_gValues[$kk]."\" >";
				if ( !isset($this->keys[$this->_gKeys[$kk]]) || sizeof($this->keys[$this->_gKeys[$kk]])<1) continue;
				foreach ($this->keys[$this->_gKeys[$kk]] as $k=>$v) {
					$ret .= '<option value="'.$this->keys[$this->_gKeys[$kk]][$k].'"'
					     . (($this->keys[$this->_gKeys[$kk]][$k]==$selected)? ' selected="selected"':'') . '>'
					     . $this->values[$this->_gKeys[$kk]][$k]
					     . '</option>';
				}
				$ret .= "</optgroup>";
			}
		} else {
			foreach ($this->keys as $k=>$v) {
				if ($dependencia && isset($this->conf['grupodependiente_'.$v])) {
					$ret .= '<option id = "'.$this->conf['grupodependiente_'.$v] . '" value="'.$this->keys[$k].'"'
					     . (($this->keys[$k]==$selected)? 'selected="selected"':'').'>'
					     . $this->values[$k]
					     . '</option>';
				} else {
					$ret .= '<option value="'.$this->keys[$k].'"'
					     . (($this->keys[$k]==$selected)? ' selected="selected"':'').'>'
					     . $this->values[$k]
					     . '</option>';
				}
			}

		}
		$ret .= '</select>';
		return $ret;
	}

	public function processReturnJSONValue($v, $pl, $id, $edit=false)
    {
		$this->json=true;
		$this->keys = array();
		$this->values = array();
		$this->loaded=false;
//		if (!$this->loadKeysValues($value)) return false;
		$sizeValuess = sizeof($this->keys);
		for ($i=0; $i < $sizeValuess; $i++) {
			if ($this->keys[$i]==$v) return $this->values[$i];
		}
		return false;
	}

	public function setNullable($val = true)
    {
        $this->nullable = $val;
        $this->loaded = false;
    }

}
