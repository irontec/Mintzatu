<?php
/**
 * Fichero de clase para campo tipo SAFETEXT
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_FLDsafetext extends tablon_FLD
{

	public function getSQLType()
	{
	    $maxChars = 255;
	    if (isset($this->conf['max'])) {
	        $maxChars = $this->conf['max'];
	    }
		return "varchar($maxChars)" . $this->getSQLRequired() . $this->getSQLUnique();
	}

	public function drawTableValueEdit($value, $clone=false, $disabled=false)
	{
		$this->setValue($value);
		$clase = (($clone)? 'clone':'');

		if (isset($this->conf['req']) && $this->conf['req'] == "1") {
		    $clase .= ((empty($clase))?"required":" required");
		}

		if (!empty($clase)) {
            $clase = " class = '".$clase."'";
		}

		$toappend = "";

		if (($boton = $this->addrandombutton()) !== false) {
			$toappend = "".$boton;
		}

		return '<input type="'.$this->getType().'"  '
		      . ((isset($this->conf['max']))? 'maxlength="'.$this->conf['max'].'"':'')
		      . ' size="'.((isset($this->conf['size']))? $this->conf['size']:'37')
		      . '" name="'.$this->getSQLFLD().(($clone)? '_clone':'')
              . '" id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
		      . '" '.$clase
		      . ' value="'.$this->drawTableValue($this->getValue()).'" />'
		      . $toappend;
	}

	public function getType()
	{
		return "text";
	}

    public function getSearchOp()
    {
        $retorno = array();
        $retorno['op'] = 'like';
        $retorno['vals'] = array();

        $valLow = mb_strtolower($this->getSearchValue(), "UTF-8");
        $retorno['vals'][] = "'%".$valLow."%'";
        $valUp = mb_strtoupper($this->getSearchValue(), "UTF-8");
        $retorno['vals'][] = "'%".$valUp."%'";
        $retorno['vals'][] = "'%".$this->getSearchValue()."%'";
        return $retorno;
    }
}
