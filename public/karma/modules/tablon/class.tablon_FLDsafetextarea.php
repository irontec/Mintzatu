<?php
/**
 * Fichero de clase para campo tipo SAFETEXT
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */


//TODO: Añadir parametro max para poder asignar un máximo de caracteres introducibles.

class tablon_FLDsafetextarea extends tablon_FLDsafetext
{

	public function getSQLType()
	{
		return "text";
	}

	public function getConstantTypeAjaxUpload()
    {
        return "_POST";
    }

    public function getType()
	{
		return "textarea";
	}

	public function drawTableValueEdit($value, $clone=false, $disabled=false)
	{
        $rc = ' rows="7" cols="37 " ';

		if (isset($this->conf['rows']) || isset($this->conf['cols'])) {
			$rows =  isset($this->conf['rows']) ? ($this->conf['rows']):'50';
			$cols =  isset($this->conf['cols']) ? ($this->conf['cols']):'100';
			$rc = ' rows="'.$rows.'" cols="'.$cols.'" ';
		}

		return '<textarea name="' . $this->getSQLFLD() . '" class="'
		      . (($this->isRequired())? ' required' : '') . ' defaultTextarea"'
		      . $rc
		      . ((isset($this->conf['max']))? ' maxlength="' . $this->conf['max'] . '"' : '')
		      . '>'
		      . $this->drawTableValue($value)
		      . '</textarea>';
	}

}
