<?php
/**
 * Fichero de clase para campo tipo ENUM
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 *
 * ::PLT::
 * nullable
 * dependencia
 * keys
 * values
 * defaultKey
 * grupodependiente_X
 */

class tablon_FLDenum extends tablon_FLD
{
	public $type="select";
	private $_keys = array();
	private $_values = array();
	protected $loaded = false;
	protected $nullable = false;
	protected $dependencia = false;

	function __construct($conf, $idx, $plt = false)
	{
		parent::__construct($conf, $idx, $plt);

		if (isset($this->conf['nullable'])) {
			$this->nullable = $this->conf['nullable'];
		}
		if (isset($this->conf['dependencia']) && $this->conf['dependencia'] != "") {
			$this->dependencia = true;
		}
	}

	public function getSQLType()
	{
		return "enum('" . implode("','", explode("|", $this->conf['keys'])) . "')" .
	          $this->getSQLRequired() . $this->getSQLDefaultValue();

	}

	public function getType()
	{
		return "select";
	}

	private function loadKeysValues($value = false)
	{
		if ($this->loaded) {
		    return true;
		}
		$this->_keys = explode("|", $this->conf['keys']);
		$values = KarmaRegistry::getInstance()->get('translator')->translate($this->conf['values'], $this->conf, 'values');
		$this->_values = explode("|", $values);
		if (isset($this->nullable) && $this->nullable !== false) {
			array_unshift($this->_keys, "NULL");
			array_unshift($this->_values, $this->nullable);
		}
		$this->loaded = sizeof($this->_keys) == sizeof($this->_values);

		return $this->loaded;
	}

	public function drawEditJSON()
	{
		if (!$this->loadKeysValues()) {
		    return false;
		}
		$nKeys = sizeof($this->_keys);
		$aRet = array();

		for ($i=0; $i<$nKeys; $i++) {
			$aRet[$this->_keys[$i]] = $this->_values[$i];
			if ($this->dependencia ) {
				$aRet['grupo_dep']  = array();
				if (isset($this->conf['grupodependiente_' . $this->_keys[$i]])) {
					$aRet['dependencia'] = true;
					$aa[$this->_keys[$i]] = $this->conf['grupodependiente_' . $this->_keys[$i]];
				}
			}
		}
		if (isset($aa)) {
		    $aRet['grupo_dep'] = json_encode($aa);
		}
		$selected = (($selectedValue = $this->getValue()) !== false)? $this->getValue():$this->getDefault();
		if ($selected !== false ) {
		    $aRet['selected'] = $selected;
		}
		if (isset($this->conf['req']) && $this->conf['req'] == "1") {
			$aRet['req'] = true;
		}
		return json_encode($aRet);
	}

	public function drawTableValue($value)
	{
		if (empty($value)) {
			if (isset($this->conf['nullable']) && $this->conf['nullable'] !== false
			   && isset($this->conf['defaultKey']) && $this->conf['defaultKey'] == "__NULL") {
				return $this->conf['nullable'];
			}
		}
		$this->_keys = array();
		$this->_values = array();
		$this->loaded = false;
		if (!$this->loadKeysValues($value)) {
		    return false;
		}
		$sizeValues = sizeof($this->_keys);

		for ($i=0; $i<$sizeValues; $i++) {
			if ($this->_keys[$i]==$value) {
				return $this->_values[$i];
			}
		}
		return false;
	}

	public function drawTableValueEdit($value, $clone=false, $disabled=false)
	{
		$this->setvalue($value);
		if (!$this->loadKeysValues($value)) {
		    return false;
		}
		$nKeys = sizeof($this->_keys);

	    $selected = $this->getDefault();
	    if ($this->getValue() !== false) {
		    $selected = $this->getValue();
		}

		$dependencia = false;
		$clase = array();
		$strclase = "";
        if (isset($this->conf['autocomplete']) && $this->conf['autocomplete'] != '0') {
            $clase[] = 'autocomplete';
        }
		if (isset($this->conf['dependencia']) && $this->conf['dependencia'] != '0') {
			$dependencia = true;
			$clase[] = "condicionante";
		}
		if ($this->isRequired()) {
		    $clase[] = "required";
		}
		if (sizeof($clase)>0) {
		    $strclase = 'class = "' . implode(" ", $clase) . '"';
		}

		$ret = '<select name="' . $this->getSQLFLD() . '" '.$strclase.'>';
		foreach ($this->_keys as $k=>$v) {
			if ($dependencia && isset($this->conf['grupodependiente_' . $v])) {
				$ret .= '<option id="' . $this->conf['grupodependiente_' . $v] . '"
				    value="' . $this->_keys[$k] . '"' .
				    (($this->_keys[$k] === $selected)? 'selected="selected"':'') . '>' .
				    $this->_values[$k] . '</option>';
			} else {
				$ret .= '<option value="' . $this->_keys[$k] . '"' .
				    (($this->_keys[$k] === $selected)? 'selected="selected"':'') . '>' .
				    $this->_values[$k] . '</option>';
			}
		}
		$ret .= '</select>';
		return $ret;
	}


	public function processReturnJSONValue($v, $pl, $id, $edit=false)
	{
		$this->_keys = array();
		$this->_values = array();
		$this->loaded = false;
		if (!$this->loadKeysValues(false)) {
		    return false;
		}
		$sizeValues = sizeof($this->_keys);
		for ($i=0; $i<$sizeValues; $i++) {
			if ($this->_keys[$i]==$v) {
				return ($_REQUEST['op']=="tablon_edit")? $this->_keys[$i]:$this->_values[$i];
			}

		}
		return false;
	}

	public function getDependencia()
	{
		return ($this->dependencia)?' condicionante ':false;
	}

	public function setNullable($val = true)
	{
	    $this->nullable = $val;
	}

    public function getSearchOp()
    {
        return " = '" . $this->getSearchValue() . "'";
    }


}
