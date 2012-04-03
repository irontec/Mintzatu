<?php
/**
 * Fichero de clase abstracta para campo de estadistica
 *
 *
 * @author Eider Bilbao <eider@irontec.com>
 * @version 1.0
 * @package karma
 */

class estadisticas_FLDtablon{
	protected $conf;
	protected $index;
	protected $plt;
	protected $unique = false;
	protected $value;
	protected $transform;
	//protected $transTtl = false;

	function __construct($conf,$idx,$plt = false) {
		$this->conf = $conf;
		if (isset($this->conf['unique'])) $this->unique = true;
		if (isset($this->conf['transform'])) $this->transform = $this->conf['transform'];
		//if (isset($this->conf['transfTtl'])) $this->transTtl = $this->conf['transfTtl'];
		$this->index = $idx;
		$this->plt = $plt;
	}

	public function getTitle() {
		return $this->getAlias();
	}

	public function getSQL($tab,$alias = true) {
		$ret = "";
		$ret = $this->getSQLFLDRequest();
		if ($alias) $ret .= " as '".$this->getAlias()."'";
		return $ret;
	}

	public function getAlias() {
		return $this->conf['alias'];
	}

	public function getSQLFLDRequest() {
		return $this->getSQLFLD();

	}

	public function getSQLFLD() {
		if (isset($this->conf['sql'])) return $this->conf['sql'];
		else return $this->getIndex();
	}

	public function getCl() {
		return "";
	}

	public function getIndex() {
		return $this->index;
	}

	public function drawTableValue($value) {

		if (($rFunc = $this->getTrigger($value))!==false){
			if(in_array('select',$this->getTriggerOn())){
				$funcionTrigger = $rFunc.';';
				eval("\$value = $funcionTrigger");
				return $value;
			}
		}

		return $value;

	}

	static function cleanMysqlValue($value) {
		$c = new con("select 1");
		return mysql_real_escape_string($value);
	}

	public function setValue($v) {
		$this->value = $v;
	}

	public function getMysqlValue($value) {
		$c = new con("select 1;");
		$v=tablon_FLD::cleanMySQLValue($value);
		$this->setValue($v);
		return '\''.tablon_FLD::cleanMySQLValue($this->getValue()).'\'';
	}

	public static function getMysqldelValue($value) {
		$c = new con("select 1;");
		$v=tablon_FLD::cleanMySQLValue($value);
		return '\''.tablon_FLD::cleanMySQLValue($v).'\'';
	}

	public function getValue() {
		if(isset($this->conf['nullable']) && $this->conf['nullable']!==false && isset($this->conf['defaultKey']) && $this->conf['defaultKey'] == "__NULL"){
			if(empty($this->value)){
				return "NULL";
			}
		}
		return $this->value;
	}

	public function getConstantTypeAjaxUpload() {
		return "_GET";
	}

	protected function getPlt() {
		return $this->plt;
	}

	public function getTransfTtl() {
		if(isset($this->conf['transfTtl']) && !empty($this->conf['transfTtl'])){
			return $this->conf['transfTtl'];
		}else{
			return false;
		}
	}

	public function getTransform() {
		if(isset($this->conf['transform']) && !empty($this->conf['transform'])){
			return $this->conf['transform'];
		}else{
			return false;
		}
	}

}


?>