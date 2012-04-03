<?php
/**
 * Fichero de clase para campo tipo DOSOMETHING
 *
 * Solo para tablon_edit. Muestra un botón al que clickando te muestra otro mensaje de confirmación.
 * Si confirmamos se ejecuta el trigger del "exec"
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 *
 * ::PLT::
 * confirm: Mensaje de confirmación a mostrar
 * literal: Texto a mostrar dentro del botón.
 * max: Maxlength del elemento (no se usa todavía)
 * size: Tamaño del botón
 * exec: Acción a ejecutar en caso de confirmar el confirm. Ej: exec="trigger::foo($id,$plt)"
 *
 */

class tablon_FLDdosomething extends tablon_FLD
{

	public function getType()
	{
		return "button";
	}
	public function getCl()
	{
		return "ghost";
	}
	public function getSQLType()
	{
		return false;
	}

	public function getInSQL()
	{
	    return false;
	}

	public function getSQL($tab,$alias = true)
	{

		$ret = "'".$this->getLiteral()."'";
		if ($alias) $ret .= " as '".$this->getAlias()."'";
		return $ret;
	}

	/*
	 * Aunque el resto de parámetros además de $value no se usan, hay que
	 * ponerlas por ser una clase que extiende a otra (fld)
	 */
	public function drawTableValueEdit($value, $clone=false, $disabled=false)
	{
		$this->setValue($value);

		$clase = "dosomethingFLD";
		if(isset($this->conf['req']) && $this->conf['req'] == "1") $clase .= ((empty($clase))?"required":" required");
		if(!empty($clase)) $clase = " class = '".$clase."'";
		$toappend = "";
		$extraAttrs = "";

		if(isset($this->conf['confirm']) ) $extraAttrs = ' confirm = "'.$this->conf['confirm'] .'" ';

        if (isset($this->conf['fields']) ) {
            $fields = explode("|", $this->conf['fields']);
            $ret = array();
            foreach ($fields as $idx => $f) {
                 list($i,$v) = explode(":", $f);
                 $ret[$i] = $v;
            }
            $extraAttrs = ' fields="'.rawurlencode(json_encode($ret)).'" ';
        }


		$exec = "";
		//if(isset($this->conf['exec']) ) $exec = ' exec = "'.$this->conf['exec'] .'" ';

		return '<input '.$extraAttrs
		       . ' id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
		       . '" type="'.$this->getType()
		       . '"  name="'.$this->getSQLFLD()
		       . '" '.$clase
		       . ' value="'.$this->getValue()
		       .'" />'.$toappend;
	}

	public function drawTableValue($value)
	{
		return $this->drawTableValueEdit($value);
	}
}
