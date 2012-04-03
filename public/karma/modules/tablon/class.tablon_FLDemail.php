<?php
/**
 * Fichero de clase para campo tipo EMAIL
 * 
 * 
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_FLDemail extends tablon_FLDsafetext {
	public function getSQLType() {
		return "varchar(100)".$this->getSQLRequired();
	}	
	
	public function preInsertCheckValue(&$value) {
		if(!$this->isRequired() && empty($value)) return true;
		if (!i::checkMail($value)) return array("1","El email no es una dirección de email válida.");
		return true;
	
	}
	
	
	public function getOCl() {
		//var_dump($this->conf);
		return "emailField";
	}
	public function getType() {
		
		return "text";
	}
}


?>