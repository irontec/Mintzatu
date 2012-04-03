<?php
/**
 * Fichero de clase para campo tipo SAFETEXT
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 *
 * ::PLT::
 * msjEreg:
 * ereg: Expresión regular a matchear
 * desc: Descripción del tipo de formato aceptado. Se mostrará como mensaje de error
 */

class tablon_FLDeregtext extends tablon_FLDsafetext {
	private $mensaje = "";

	function __construct($conf,$idx,$plt = false) {
		parent::__construct($conf,$idx,$plt);
		if(isset($this->conf['msjEreg'])){
				$this->mensaje = $this->conf['msjEreg'];
		}
	}
	public function getSQLType() {
		return "varchar(255)".$this->getSQLRequired();;
	}

	public function getType() {
		return "text";
	}

	public function preInsertCheckValue(&$value) {
		if (!isset($this->conf['ereg'])) return true;
		if(empty($value) && (!isset($this->conf['req']) || $this->conf['req'] == "0")){
			return true;
		}
		if(isset($this->conf['ereg_excluir']) && $this->conf['ereg_excluir'] == "1"){
			if (preg_match("/{$this->conf['ereg']}/",$value)) {
				$error = "{$this->l->l('Formato Incorrecto')}. ".((isset($this->conf['desc']))? $this->conf['desc']:"");
				if($this->mensaje!= ""){
					$error = "\n".$this->mensaje;
				}
				return array(1,$error);
			}
		}else{
			if (!preg_match("/{$this->conf['ereg']}/",$value)) {
				$error = "{$this->l->l('Formato Incorrecto')}. ".((isset($this->conf['desc']))? $this->conf['desc']:"");
				if($this->mensaje!= ""){
					$error = "\n".$this->mensaje;
				}
				return array(1,$error);
			}
		}
		return true;
	}

}


?>
