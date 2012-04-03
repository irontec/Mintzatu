<?php
/*
 * Clase para campo tipo GHOST
 * Está en la tabla, se consulta a mysql (aunque sea con concat), pero ni se actualiza, ni se inserta
 * Sirve para dibujar una imagen, cuya ruta depende de algún campo, o cosas así
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */
class tablon_FLDsectionlink extends tablon_FLD {
	protected $forceupdate = false;

	public function getType() {
		return false;
	}
	public function getCl() {
		return "ghost";
	}
	public function getSQLType() {
		return false;
	}

	/* Aunque el resto de parámetros además de $v no se usan, hay que ponerlas por ser una clase que extiende a otra (FLD) */
	public function drawTableValueEdit($v,$clone=false,$disabled=false) {
		return $this->drawTableValue($v);
	}

	public function drawTableValue($value,$ret = false, $pl = false) {
		if ($ret !== false && $pl !== false ) {

			$c = new con("select ".$this->conf['sql']." as gh from ".$pl->getTab()." where ".$pl->getId()." = '".$ret."' ");
			$r = $c->getResult();
			return $r['gh'];
		}

		if (isset($this->conf['link']) ){
			$link = str_replace("%id%",$value,$this->conf['link']);
			$link = '<a href="'.$link.'"  class="nomove" >'.$this->conf['alias'].' ('.$value.')</a>';
			return $link;
		}

		return $value;

	}

	public function setSearchValue($v) {
		if (preg_match("/value=\"(.*)\"/iU",$v,$ret)) {
			$this->searchValue = $ret[1];
		} else $this->searchValue = $v;
	}


	public function getSearchValue() {
		return '<input type="text'
            . '" id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
		    . '" name="'.$this->getIndex()
		    . '" value="' . $this->searchValue
		    . '" />';
	}

	public function getSQLFLDSearch($alias = false) {
		return ($alias)? $this->getAlias():$this->getIndex();
	}


 	public function getSearchVarType() {
 		return "aHaveSearch";
 	}

	public function getSearchOp() {
		return ' like \'%'.$this->searchValue.'%\'';
	}

 	public function forceUpdate($force=false){
 		$this->forceupdate = $force;
 	}

	public function getForceUpdate(){
 		return $this->forceupdate;
 	}

	public function getSQLFLD($r=false) {
		if (isset($this->conf['sql']) && $this->forceupdate===false) return $this->conf['sql'];
		else return (($r&&$this->getReal()!="")? $this->getReal():$this->getIndex());
	}

}


?>