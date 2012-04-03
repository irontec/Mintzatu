<?php
/*
 * Clase para campo tipo GHOSTEXTERNAL
 * Se llama a un método de una clase externa, pasando como argumento el id del registro
 * Dibuja el texto que devuelva el método
 *
 * @author David Lores <david@irontec.com>
 * @version 2.0
 * @package karma
 */

class tablon_FLDghostexternal extends tablon_FLD
{

	public function getType()
	{
		return false;
	}

	public function getCl()
	{
		return "ghostexternal";
	}

	public function getSQLType()
	{
		return false;
	}

	public function getSQL($tab,$alias = true)
	{
	    return false;
	}

	public function drawTableValueEdit($v,$clone=false, $disabled=false)
	{
		return $this->drawTableValue($v);
	}

	public function drawTableValue($value)
	{
        if (!isset($this->conf['external_class'])) {
            iError::error("Hay que especificar una clase externa (external_class) para el campo GHOSTEXTERNAL");
            return false;
        }
        if (!isset($this->conf['external_method'])) {
            iError::error("Hay que especificar un método externa (external_method) para el campo GHOSTEXTERNAL");
            return false;
        }
        $extObject = new $this->conf['external_class']();
        if (!is_object($extObject)) {
            iError::error("No existe la clase ".$this->conf['external_class']);
            return false;
        }
        try {
            $value = call_user_func(array($extObject, $this->conf['external_method']), $this->currentID);
        } catch (Exception $e) {
            iError::error("Error al llamar al método ".$this->conf['external_method']);
            return false;
        }
		return $value;
	}

}

