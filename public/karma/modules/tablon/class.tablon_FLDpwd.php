<?php
/**
 * Fichero de clase para campo tipo SAFETEXT
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_FLDpwd extends tablon_FLD {
	private $eregpwd = "";
	private $saveclear = false;

	public function getSQLType() {
		return "varchar(40)".$this->getSQLRequired();
	}

	function __construct($conf,$idx,$plt = false) {
		parent::__construct($conf,$idx,$plt);
		if(isset($this->conf['ereg'])){
				$this->eregpwd = $this->conf['ereg'];
		}
		if(isset($this->conf['saveclear']) && $this->conf['saveclear'] == "1"){
			$this->saveclear = true;
		}
	}

	public function getMysqlValue($value) {
		$c = new con("select 1;");
		$this->setValue(tablon_FLD::cleanMySQLValue($value));
		if($this->saveclear === true) return '\''.$this->getValue().'\'';
		if (isset($this->conf['md5']) && $this->conf['md5']=="true") return '\''.md5($this->getValue()).'\'';

		return '\''.i::cifrar($this->getValue()).'\'';
	}

	public function preInsertCheckValue(&$value) {
		if (!isset($this->eregpwd) || empty($this->eregpwd)) return true;
		if(empty($value) && (!isset($this->conf['req']) || $this->conf['req'] == "0")){
			return true;
		}
		if (!preg_match("/{$this->eregpwd}/",$value)) {
			$error = "{$this->l->l('Formato Incorrecto')}. ".((isset($this->conf['desc']))? $this->conf['desc']:"");
			return array(1,$error);
		}
		return true;
	}

	public function getType() {
		return "password";
	}
}


?>
