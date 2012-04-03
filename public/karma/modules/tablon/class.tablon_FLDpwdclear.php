<?php
class tablon_FLDpwdclear extends tablon_FLDpwd {

	public function getMysqlValue($value) {
		$c = new con("select 1;");
		$this->setValue(tablon_FLD::cleanMySQLValue($value));
		return '\''.$this->getValue().'\'';
	}
}
?>