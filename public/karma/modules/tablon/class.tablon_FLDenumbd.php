<?php
/**
 * Fichero de clase para campo tipo ENUMBD
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 *
 * ::PLT::
 * nullable
 * tab: Tabla de la que se cogerán los datos
 * id: Campo id de la tabla secundaría
 * fld: Campo a mostrar de la tabla secundaria
 * grrel
 * morefld
 * lefttabs
 * leftconds
 * condicion
 * idcond
 * orcondicion
 * fldorder
 * pictures
 * grid
 * grfld
 * grtab
 * grcondicion
 * grfldorder
 * grupodependiente_X
 * dependencia
 */

class tablon_FLDenumbd extends tablon_FLDenum
{
    public $type = "select";
    protected $keys = array();
    protected $values = array();
    protected $_gKeys = array();
    protected $_gValues = array();
    protected $_gLoaded = false;
    public $json=false;

    public function getMysqlValue($value)
    {
        $c = new con("select 1;");
        $v = tablon_FLD::cleanMySQLValue($value);
        $this->setValue($v);
        if ($this->nullable && $v == "NULL") {
            return tablon_FLD::cleanMySQLValue($this->getValue());
        }
        return '\''.tablon_FLD::cleanMySQLValue($this->getValue()).'\'';
    }

    public function getSQLType()
    {
        return
            "mediumint unsigned" . $this->getSQLRequired() . $this->getSQLDefaultValue() .
             ",\n add index(" . $this->getIndex() . ") " .
            ",\n add foreign key(" . $this->getIndex() . ") references " . $this->conf['tab'] .
            "(" . $this->conf['id'] . ") on delete set null on update cascade\n";
    }

    protected function loadKeysValues($a = false)
    {
        if ($this->loaded) {
            return true;
        }
        $sql = "select ".$this->conf['id']." as id, ";
        if (isset($this->conf['grrel'])) {
            $sql .= $this->conf['grrel']." as REL , ";
        }
        if (isset($this->conf['morefld'])) {
            $sql .= $this->conf['morefld']." as morevlr , ";
        }
        $sql.= $this->conf['fld']." as vlr from ".$this->conf['tab'];
        if (isset($this->conf['lefttabs'])) {
            $leftTab = explode('|', $this->conf['lefttabs']);
            $leftCond = explode('|', $this->conf['leftconds']);
            if (sizeof($leftTab)>0 && sizeof($leftCond)>0 && sizeof($leftTab) == sizeof($leftCond)) {
                for ($i=0;$i<sizeof($leftTab);$i++) {
                    if ($i!=0) {
                        $sql .= " ";
                    }
                    $sql .= " left join ".$leftTab[$i]." on(".$leftCond[$i].") ";
                }
            }
        }

        if (isset($a)&&($a!=false)) {
            $sql .= " where ".$this->conf['id']." = '".$a."' limit 1";
        } else {
            $sqlcond = array();
            if (isset($this->conf['condicion'])) {
                 $sqlcond[] = str_replace("%id%", $this->currentID, $this->conf['condicion']);
            }
            if (isset($this->conf['idcond'])) {
                if (isset($_GET['id'])) {
                    $currentValue = explode('::', $_GET['id']);
                    $currentValue = $currentValue[2];
                    $sqlcond[] = $this->conf['idcond']." = '".$currentValue."' " ;
                } else {
                    $sqlcond[] = $this->conf['idcond']." = '".$this->currentID."' " ;
                }
            }
            if (sizeof($sqlcond)>0) {
                $sql .= " where ";
                if (isset($this->conf['orcondicion'])) {
                    $sql .= "(" . implode(' and ', $sqlcond) . ") or (" . $this->conf['orcondicion'] . ")";
                } else {
                    $sql .= implode(' and ', $sqlcond);
                }
            }
            if (isset($this->conf['fldorder'])) {
                $sql .= " order by " . $this->conf['fldorder'];
            }
        }
        $c = new con($sql);
        if ($c->error()) {
            iError::error($c->getError());
            return false;
        }
        if (isset($this->nullable) && $this->nullable !==false && $a===false) {
            $this->keys[] = "NULL";
            $this->values[] = $this->nullable;
        }
        while ($r = $c->getResult()) {
            if (isset($this->conf['grrel'])&&!$this->json) {
                $this->keys[$r['REL']][] = $r['id'];
                $this->values[$r['REL']][] = $r['vlr'];
            } else {
                $this->keys[] = $r['id'];
                if (isset($this->conf['pictures'])) {
                    $this->values[] = "<img height=\"30px\" width=\"30px\" src=\"../cache/tablon/img/" .
                        $this->conf['pictures'] . $r['vlr']."?thumb\" />" .
                        ((isset($this->conf['morefld']))? $r['morevlr']:$r['vlr'] );
                } else {
                    if (isset($this->conf['grrel'])&&$this->json) {
                        $this->loadGrKeysValues();
                        foreach ($this->_gKeys as $kk=>$vv) {
                            if ($vv==$r['REL']) {
                                $this->values[] = $r['vlr'].'
                                    (<em>' . $this->_gValues[$kk] . '</em>)';
                                break;
                            } else {
                                continue;
                            }
                        }
                    } else {
                    	if (isset($this->conf['valueUtf8Encode']) && $this->conf['valueUtf8Encode']=="true") {
                    		$this->values[] = utf8_encode($r['vlr']);
                    	} else {
                    		$this->values[] = $r['vlr'];
                    	}
                    }
                }
            }
        }
        $this->loaded = true;
        return true;
    }

    protected function loadGrKeysValues($a=false)
    {
        if ($this->_gLoaded) {
            return true;
        }
        $sql = "select " . $this->conf['grid'] . " as id, " . $this->conf['grfld'] . " as vlr
                from " . $this->conf['grtab'];
        if (isset($a)&&($a!==false)) {
        } else {
            if (isset($this->conf['grcondicion'])) {
                $sql .= " where " . $this->conf['grcondicion'];
            }
            if (isset($this->conf['grfldorder'])) {
                $sql .= " order by " . $this->conf['grfldorder'];
            }
        }
        $c = new con($sql);
        if ($c->error()) {
            iError::error($c->getError());
            return false;
        }
        while ($r = $c->getResult()) {
            $this->_gKeys[] = $r['id'];
            $this->_gValues[] = $r['vlr'];
        }
        $this->_gLoaded = true;
        return true;
    }

    public function drawEditJSON()
    {
        $this->json = true;
        if (!$this->loadKeysValues()) {
            return false;
        }
        $nKeys = sizeof($this->keys);
        $aRet = array();
        for ($i=0;$i<$nKeys;$i++) {
            $aRet[$this->keys[$i]] = $this->values[$i];
            if ($this->dependencia) {
                if (isset($this->conf['grupodependiente_'.$this->keys[$i]])) {
                    $aRet['dependencia'] = true;
                    $aRet["d_".$this->keys[$i]] = $this->conf['grupodependiente_'.$this->keys[$i]];
                }
            }
        }
        $selected = (($selectedValue = $this->getValue())!==false)? $this->getValue():$this->getDefault();
        /*PUEDE QUE BUG*/
        if ($selected !== false ) {
            $aRet['selected'] = $selected;
        }
        if (isset($this->conf['req']) && $this->conf['req'] == "1") {
            $aRet['req'] = true;
        }
        return json_encode($aRet);
    }

    public function drawTableValue($value)
    {
        $this->json=true;
        if (empty($value)) {
            if (isset($this->conf['nullable']) && $this->conf['nullable'] !== false
               && isset($this->conf['defaultKey']) && $this->conf['defaultKey'] == "__NULL") {
                return $this->conf['nullable'];
            }
        }

        if ($value==NULL) {
            return false;
        }
        $this->keys = array();
        $this->values = array();
        $this->loaded = false;
        if (!$this->loadKeysValues($value)) {
            return false;
        }
        $sizeValues = sizeof($this->keys);
        for ($i=0;$i<$sizeValues;$i++) {
            if ($this->keys[$i]==$value) {
                return $this->values[$i];
            }
        }
        return false;
    }

    public function drawTableValueEdit($value, $clone=false, $disabled=false)
    {
        $this->setvalue($value);
        if (!$this->loadKeysValues()) {
            return false;
        }
        $nKeys = sizeof($this->keys);
        $selected = (($selectedValue = $this->getValue())!==false)? $this->getValue():$this->getDefault();
        if ($selected !== false ) {
            $aRet['selected'] = $selected;
        }
        $dependencia = false;
        $clase = array();
        $strclase = "";
        if (isset($this->conf['dependencia']) && $this->conf['dependencia']!= '0') {
            $dependencia = true;
            $clase[] = "condicionante";
        }
        if ($this->isRequired()) {
            $clase[] = "required";
        }
        if (isset($this->conf['autocomplete']) && $this->conf['autocomplete'] != '0') {
            $clase[] = 'autocomplete';
        }
        $extradatos = "";
        $ret = "";
        if (count($this->keys) == 0 && $this->isRequired()
           && isset($this->conf['msgWhenNoData']) && !empty($this->conf['msgWhenNoData'])) {
            $ret .= "<span>".$this->conf['msgWhenNoData']."<br></span>";
        }
        if (sizeof($clase)>0) {
            $strclase = 'class = "' . implode(" ", $clase) . '"';
        }
        $ret .= '<select name="' . $this->getSQLFLD() . '" ' . $strclase . ' ' . $extradatos . '>';
        if (isset($this->conf['grrel'])&&!$this->json) {
            if (!$this->loadGrKeysValues()) {
                return false;
            }
            if ($this->keys[0]=='NULL') {
                $ret .= '<option value="' . $this->keys[0] . '"' .
                    (($this->keys[0]==$selected)? ' selected="selected"':'') . '>' .
                    $this->values[0] . '</option>';
                unset($this->keys[0]);
            }
            foreach ($this->_gKeys as $kk=>$vv) {
                $ret .= "<optgroup label=\"" . $this->_gValues[$kk] . "\" >";
                if (!isset($this->keys[$this->_gKeys[$kk]]) || count($this->keys[$this->_gKeys[$kk]]) < 1) {
                    continue;
                }

                foreach ($this->keys[$this->_gKeys[$kk]] as $k=>$v) {
                    $ret .= '<option value="' . $this->keys[$this->_gKeys[$kk]][$k] . '"' .
                        (($this->keys[$this->_gKeys[$kk]][$k]==$selected)? ' selected="selected"':'') . '>' .
                        $this->values[$this->_gKeys[$kk]][$k] . '</option>';

                }
                $ret .= "</optgroup>";
            }
        } else {
            foreach ($this->keys as $k=>$v) {
                if ($dependencia && isset($this->conf['grupodependiente_'.$v])) {
                    $ret .= '<option id = "' . $this->conf['grupodependiente_'.$v] . '"
                         value="' . $this->keys[$k] . '"' .
                        (($this->keys[$k]==$selected)? 'selected="selected"':'') . '>' .
                        $this->values[$k] . '</option>';
                } else {
                    $ret .= '<option value="' . $this->keys[$k] . '"' .
                        (($this->keys[$k]==$selected)? ' selected="selected"':'') . '>' .
                        $this->values[$k] . '</option>';
                }
            }
        }
        $ret .= '</select>';
        return $ret;
    }

    public function processReturnJSONValue($v, $pl, $id, $edit=false)
    {
        $this->json = true;
        $this->keys = array();
        $this->values = array();
        $this->loaded = false;
        if ($id) {
            $this->currentID = $id;
        }
        if (!$this->loadKeysValues(false)) {
            return false;
        }
        $sizeValues = sizeof($this->keys);
        for ($i=0; $i<$sizeValues; $i++) {
            if ($this->keys[$i] == $v) {
                return ($_REQUEST['op']=="tablon_edit")? $this->keys[$i]:$this->values[$i];
            }
        }
        return false;
    }
}
