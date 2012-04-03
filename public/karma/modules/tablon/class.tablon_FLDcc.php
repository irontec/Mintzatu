<?php
/**
 * Fichero de clase para campo tipo CC (Cuenta Corriente)
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_FLDcc extends tablon_FLDsafetext {
	public function getSQLType() {
		return "varchar(100)".$this->getSQLRequired();
	}

	public function preInsertCheckValue(&$value) {
		if(!$this->isRequired() && empty($value)) return true;

		if (!i::checkcc($value,'-',$deb)) return array("1","El valor del número de la Cuenta Corriente es incorrecto.".$deb);

		/*$ret = i::valida_nif_cif_nie($value);
		if ($ret<=0) return array("1","El valor no es válido (".$ret.").");*/



		//if (!i::checkMail($value)) return array("1","El email no es una dirección de email válida.");
		return true;

	}
	/*
 *
 *  Tipo:  	???  	NIF  	CIF  	NIE
 *	Correcto: 		1   	2   	3
 *  Incorrecto: 	0   	-1   	-2   	-3
 */

	public function getType() {

		return "text";
	}
}


?>