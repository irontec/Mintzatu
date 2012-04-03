<?php
/**
 * Fichero de clase para campo tipo ENUMEXTERNAL
 *
 * Obtiene los keys y values de una clase externa y los muestra como un ENUM normal.
 * La clase externa deberá implementar la interfaz tablon_Interface_FLDenumexternalSource
 *
 *
 * @author Alayn Gortazar <alayn@irontec.com>
 * @version 1.0
 * @package karma
 *
 * ::PLT::
 * nullable: Valor que queremos que se muestre en caso de permitir el valor "null" o vacio
 * external_class: Nombre de la clase externa
 * dataset: Conjunto de datos que queremos obtener de la clase externa (parámetro de la función getKarmaEnumArray())
 */

class tablon_FLDenumexternal extends tablon_FLDenum
{

    public function getSQLType()
    {
        return "mediumint(8) unsigned " . $this->getSQLRequired() . $this->getSQLDefaultValue();
    }


    /*
     * Obtiene de "external_class" los keys y values y los almacena en:
     *  $this->keys
     *  $this->values
     */
    protected function loadKeysValues($a=false)
    {
        if ($this->loaded) return true;

        if (isset($this->nullable) && $this->nullable !==false) {
            $this->keys[] = "NULL";
            $this->values[] = $this->nullable;
        }
        if (!isset($this->conf['external_class'])) {
            iError::error("Hay que especificar una clase externa (external_class) para el campo ENUMExternal");
            return false;
        }
        $extClass = $this->conf['external_class'];

        $extObject = new $extClass();
        if (!($extObject instanceof tablon_Interface_FLDenumexternalSource)) {
            iError::warn(
                "La clase externa {$extClass} debe implementar la interfaz tablon_Interface_FLDenumexternalSource"
            );
            return false;
        } else {
            $dataset = null;
            if (isset($this->conf['dataset'])) {
                $dataset = $this->conf['dataset'];
            }
            if (($keyValues = $extObject->getKarmaEnumArray($dataset)) === false) {
                // algo ha ido mal
                return false;
            }

            foreach ($keyValues as $key => $value) {
                $this->keys[] = $key;
                $this->values[] = $value;
            }
            $this->loaded = true;
            return true;
        }
    }


    public function drawEditJSON()
    {
        $this->json=true;
        if (!$this->loadKeysValues()) return false;
        $nKeys = sizeof($this->keys);
        $aRet = array();
        for ($i=0;$i<$nKeys;$i++) {
            $aRet[$this->keys[$i]] = $this->values[$i];
        }
        $selected = (($selectedValue = $this->getValue())!==false)? $this->getValue():$this->getDefault();
        /*PUEDE QUE BUG*/
        if ($selected !== false ) $aRet['selected'] = $selected;
        if (isset($this->conf['req']) && $this->conf['req'] == "1") {
            $aRet['req']=true;
        }

        return json_encode($aRet);
    }

    public function drawTableValue($value)
    {
        $this->json=true;
        if (empty($value)) {
            if (isset($this->conf['nullable'])
                && $this->conf['nullable']!==false
                && isset($this->conf['defaultKey'])
                && $this->conf['defaultKey'] == "__NULL") {
                return $this->conf['nullable'];
            }
        }

        if ($value==NULL) {
            return false;
        }
        $this->keys = array();
        $this->values = array();
        $this->loaded=false;
        if (!$this->loadKeysValues()) return false;
        $sizeValues = sizeof($this->keys);
        for ($i=0;$i<$sizeValues;$i++) {
            if ($this->keys[$i]==$value) return $this->values[$i];
        }

        return false;
    }

	/**
	 * Dibuja la Select
	 * @param mixed $value Valor a dibujar
	 * @param none $clone No se usa
	 * @param none $disabled No se usa
	 */
    public function drawTableValueEdit($value, $clone=false, $disabled=false)
	{
        $this->setvalue($value);
        if (!$this->loadKeysValues()) return false;
        $nKeys = sizeof($this->keys);
        $selected = (($selectedValue = $this->getValue())!==false)? $this->getValue():$this->getDefault();
        if ($selected !== false ) $aRet['selected'] = $selected;
        $dependencia = false;
        $clase = array();
        $strclase = "";
        if (isset($this->conf['dependencia']) && $this->conf['dependencia']!= '0') {
            $dependencia = true;
            $clase[] = "condicionante";
        }

        if (isset($this->conf['autocomplete']) && $this->conf['autocomplete'] != '0') {
            $clase[] = 'autocomplete';
        }

        if($this->isRequired()) $clase[] = "required";
        if(sizeof($clase)>0) $strclase = 'class = "'. implode(" ", $clase).'"';

        $ret = '<select name="'.$this->getSQLFLD().'" '.$strclase.'>';
        foreach ($this->keys as $k=>$v) {
            if ($dependencia && isset($this->conf['grupodependiente_'.$v])) {
                $ret .= '<option id = "'
                    .$this->conf['grupodependiente_'.$v].'" value="'.$this->keys[$k].'"'
                    .(($this->keys[$k]==$selected)? 'selected="selected"':'').'>'
                    .$this->values[$k].'</option>';
            } else {
                $ret .= '<option value="'.$this->keys[$k].'"'
                    .(($this->keys[$k]==$selected)? ' selected="selected"':'').'>'
                    .$this->values[$k].'</option>';
            }
        }
        $ret .= '</select>';
        return $ret;
    }

    public function processReturnJSONValue($v,$pl,$id,$edit=false)
    {
        $this->json=true;
        $this->keys = array();
        $this->values = array();
        $this->loaded=false;
        if (!$this->loadKeysValues($v)) return false;
        $sizeValues = sizeof($this->keys);
        for ($i=0;$i<$sizeValues;$i++) {
            if ($this->keys[$i]==$v)
                return ($_REQUEST['op']=="tablon_edit")? $this->keys[$i]:$this->values[$i];
        }
        return false;
    }
}
