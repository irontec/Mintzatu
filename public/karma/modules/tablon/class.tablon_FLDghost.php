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
class tablon_FLDghost extends tablon_FLD {
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

	public function drawTableValueEdit($v,$clone=false, $disabled=false) {
		return $this->drawTableValue($v);
	}

	public function drawTableValue($value,$ret = false, $pl = false) {
		if ($ret !== false && $pl !== false ) {

			$c = new con("select ".$this->conf['sql']." as gh from ".$pl->getTab()." where ".$pl->getId()." = '".$ret."' ");
			$r = $c->getResult();
			return $r['gh'];
		}
		return $value;

	}

	public function setSearchValue($v) {
		if (preg_match("/value=\"(.*)\"/iU",$v,$ret)) {
			$this->searchValue = $ret[1];
		} else $this->searchValue = $v;
	}


	public function getSearchValue() {
		return '<input type="text" name="'.$this->getIndex().'" value="'.$this->searchValue.'" />';
	}

	public function getSQLFLDSearch($alias = false) {

		if (isset($this->conf['sqlSearch'])) {
		    return $this->conf['sqlSearch'];
		}
		//return "`".$this->getIndex()."`";
		return ($alias)? $this->getAlias():$this->getIndex();

	}



 	public function getSearchVarType()
 	{
 		if (isset($this->conf['modeSearch'])) {
			if ($this->conf['modeSearch'] == 'where')
				return "aCondSearch";
 		}

 		//return "aCondSearch";
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


 	/*
 	 * Devuelve el código sql para obtener el valor del campo.
 	 * En caso de que haya valor para el 'sql' en el plt, el valor del campo será la ejecución de esa sentencia sql.
 	 * Si además existe el campo 'phpCodeInSql', se indica que dentro del para sql hay que ejecutar primero un código php mediante eval para luego incluir dicho valor en el sql.
 	 * @param $r:	Si no existe el campo 'sql' en el plt y este parámtero está a true y hay valor de campo 'real', se devuelve este, sino se devuelve el index
 	 *
 	 *
 	 */

	public function getSQLFLD($r=false) {
		if (isset($this->conf['sql']) && $this->forceupdate===false){
			if(isset($this->conf['phpCodeInSql']) && !empty($this->conf['phpCodeInSql'])){
				// Si hay que ejecutar algo en php primero, se ejecuta (eval), y luego se sustituye el resultado en el comando sql, dónde pone %phpCodeInSql%
				$parte = $this->conf['phpCodeInSql'];
				eval("\$sust = $parte;");
				$sql = str_replace('%phpCodeInSql%',$sust,$this->conf['sql']);
				return $sql;
			}else
				return $this->conf['sql'];
		}
		else return (($r&&$this->getReal()!="")? $this->getReal():$this->getIndex());
	}

	/*
	* Necesitamos devolver siempre el index real, a la hora de dibujar el header, para comprobar si está oi no en el noshownfields
	* para el resto de cosas útiles (searcher, etc, etc... ?), tira bien como esta. (en principio
	* Fdo. Lander
	*/

    public function getIndex($forceReal = false)
    {

        if ( (isset($this->conf['sqlSearch'])) && ($forceReal === false) ) {
            return $this->conf['sqlSearch'];
        }
        
        return $this->index;
    }

}


?>
