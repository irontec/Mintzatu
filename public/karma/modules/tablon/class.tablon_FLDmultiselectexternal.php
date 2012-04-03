<?php
/*
 * Clase para campo tipo MultiselectExternal
 * Sirve para poder seleccionar varios elementos obtenidos de una clase externa
 * y guardar la relación en una tabla relacional
 *
 * Obtiene los keys y values de una clase externa y los muestra como un ENUM multiselect normal.
 * La clase externa deberá implementar la interfaz tablon_Interface_FLDenumexternalSource
 *
 * ::::PLT::::
 * alias: nombre de campo a mostrar en la web
 * id: id de la tabla 1 o elemento actual [sólo es necesario si difiere del campo "union",
 *     aunque es recomendable ponerlo y punto :p]
 * external_class: Nombre de la clase externa de la que se obtendrán los datos.
 *
 * tab: nombre de la tabla relacionada (tabla_2)
 * tabconds: condición necesaria en la tabla2 para poder seleccionar el elemento (p.e. tabla2.deleted = '0')
 * tabid: id de la tabla 2
 * showfield: campo a mostrar de la segunda tabla
 * showfield_sql: sql necesario para mostrar cada uno de los datos (sobreescribe a showfield)
 *
 * separator: separador utilizado para los datos obtenidos de "showfield".
 *            No influye en el separador para los id's, que siempre será una coma
 * reltab: tabla de relaciones
 * relid: id de la tabla de relaciones
 * union: campo de la tabla de relaciones que referencia a los ids de la primera tabla o elemento actual (id_tabla_1)
 * relunion: campo de la tabla de relaciones que referencia a los ids de la segunda tabla (id_tabla_2)
 * reldefaults: campos a los que se quiere añadir un valor por defecto,
 *              separados por pipes (|) (p.e. "active='1'|insert_date=now()")
 *
 *
 * @author Alayn Gortazar <alayn@irontec.com>
 * @version 1.0
 * @package karma
 */
class tablon_FLDmultiselectexternal extends tablon_FLD
{


	public function getType()
    {
		return "multiselectng";
	}

    public function loadJS()
    {
        return array(
        "../modules/tablon/scripts/jqueryMultiSelect.js"
        );
    }

    public function loadCSS()
    {
        return array(
            "../modules/tablon/css/jqueryMultiSelect.css"
        );
    }


    /*
     * Sobreescribimos getMysqlValue, y devolvemos noInsertBecauseFileEsp
     * para que tablon_plantilla no intente guardar nada
     *
     * Aquí lo hacemos todo :)
     *
     * FIXME: Esto está cogido del multiselectng aquí probablemente no funciona correctamente
     */
    public function getMysqlValue($value)
    {

        $c = new con("select 1;");
        $aValues = explode(",", $value);
        $aValues = array_unique($aValues);
        $id = explode('::', $_GET['id']);
        if (!is_array($id) || !isset($id[2]) || $id[2] == 0 || $id[2] == false) {
            return 'noInsertBecauseFileEsp';
        }

        //Obtenemos las relaciones previamente existentes
        $sql = "select ".$this->conf['relunion']." as IDO , ".$this->conf['relid']." as RELID from ".$this->conf['reltab']." where ".$this->conf['union']." ='".$id[2]."'";
        $c = new con($sql);
        $aRes = array();
        while ($r = $c->getResult()) {
        	$aRes[]= $r['IDO'];
        }
        $toInsert = array_diff($aValues, $aRes);
        $toDelete = array_diff($aRes, $aValues);

        if (sizeof($toInsert > 0)) {
        	foreach ($toInsert as $insertValue) {
                $acampos = array();
                $avalores = array();

                $acampos[] = $this->conf['union'];
                $avalores[] = "'{$id[2]}'";
                $acampos[] = $this->conf['relunion'];
                $avalores[] = "'{$insertValue}'";

                if (isset($this->conf['reldefaults'])) {
                	$aRelDefaults = explode("|", $this->conf["reldefaults"]);
                	foreach ($aRelDefaults as $relDefault) {
	                    list($acampos[], $avalores[]) = explode('=', $relDefault);
                	}
                }

        		$sql = "insert into {$this->conf["reltab"]} (".implode(",", $acampos).") values (".implode(",", $avalores).")";
        		$c = new con($sql);
        	}
        }

        if (sizeof($toDelete > 0)) {
        	foreach ($toDelete as $deleteValue) {
        		$sql = "delete from {$this->conf["reltab"]} where {$this->conf["relunion"]} = '{$deleteValue}' and {$this->conf["union"]} = '{$id[2]}'";
                $c = new con($sql);
        	}
        }

        $accionEjec = 'insert';
        if ($this->trigger !== false && isset($this->triggerParamsEsp) && !empty($this->triggerParamsEsp)) {
			if (in_array('insert', $this->getTriggerOn())) {
				$_datosForTrigger = array(
					"valueFLD" => $value
				);
				$cad = $this->runTriggerNew($id[2], $accionEjec, $_datosForTrigger);
				if (is_array($cad)) {
	            	return $cad;
	            }
			}
		} else {
	        if ($this->getTrigger($id[2], $accionEjec)!==false) {
	            if (in_array($accionEjec, $this->triggerOn) || in_array('update', $this->triggerOn)) {
	                $funcionTrigger = $this->getTrigger($id[2], $accionEjec).';';
	                eval("\$cad = $funcionTrigger");
	                if (is_array($cad)) {
	                    return $cad;
	                }
	            }
	        }
		}
        return 'noInsertBecauseFileEsp';
    }


	public function getSQLFLDRaw($t=false)
    {

			return $this->getSQL($t, $this->alias);
	}

	/**
	 * Devuelve el sql necesario para obtener la lista de id's separados por pipes
	 * @param unknown_type $tab
	 * @param unknown_type $alias
	 */
	public function getSQL($tab, $alias = true)
    {
		$ret = "( select group_concat(" . $this->conf['relunion'] . " SEPARATOR '|')
		FROM {$this->conf['reltab']}
		WHERE {$this->conf['reltab']}.{$this->conf['union']} = {$tab}." . ((isset($this->conf['id']))? $this->conf['id']:$this->conf['union']) . ")";

		if ($alias) $ret .= " as '".$this->getAlias()."'";
		return $ret;
	}

    public function drawTableValueEdit($value, $clone = false, $disabled = false)
    {

        $extClass = $this->conf['external_class'];
        $extObject = new $extClass();
        if (!($extObject instanceof tablon_Interface_FLDenumexternalSource)) {
            iError::warn("La clase externa {$extClass} debe implementar la interfaz tablon_Interface_FLDenumexternalSource");
            return false;
        } else {
            $dataset = null;
            if (isset($this->conf['dataset'])) {
                $dataset = $this->conf['dataset'];
            }
            $keyValues = $extObject->getKarmaEnumArray($dataset);
        }

        $html = '';

        $arr = explode($this->conf['separator'], $this->drawTableValue($value));

        $html.= '<select  multiple="multiple '.(($this->isRequired())? 'required':'').'"  size="1"  style="visibility:hidden;"  class="multiselect"  name="'.$this->getSQLFDL().'">';
        foreach ($keyValues as $key => $val) {
            $html.= '<option value="'.$key.'"  '.((in_array($val, $arr))? "selected=\"selected\"":"").'>'.$val.'</option>';
        }
        $html .= '</select><input type="hidden" bind="true"'
              . ' name="'.$this->getSQLFLD()
              . '" id="' . $this->getSQLFLD() . '_' . $this->getCurrentID()
              . '" class="multivals"  value="' . $this->drawTableValue($value)
              . '" />';

        return $html;
    }

    public function drawTableValue($value)
    {
        $retValues = array();
        $ids = explode('|', $value);

        if (sizeof($ids)) {
            $extClass = $this->conf['external_class'];
            $extObject = new $extClass();
            if (!($extObject instanceof tablon_Interface_FLDenumexternalSource)) {
                iError::warn("La clase externa {$extClass} debe implementar la interfaz tablon_Interface_FLDenumexternalSource");
                return false;
            } else {
                $dataset = null;
                if (isset($this->conf['dataset'])) {
                    $dataset = $this->conf['dataset'];
                }
                $keyValues = $extObject->getKarmaEnumArray($dataset);
                foreach ($ids as $id) {
                    if (isset($keyValues[$id])) {
                        $retValues[] = $keyValues[$id];
                    }
                }
            }
        }
        return implode($this->conf['separator'], $retValues);
    }

    /**
     * FIXME: Esto está cogido del multiselectng aquí probablemente no funciona correctamente
     */
    public function insertAfterCreateMain($idMain, $value)
    {
        $c = new con("select 1;");
        $aValues = explode(",", $value);
        $aValues = array_unique($aValues);

        //Obtenemos las relaciones previamente existentes
        $sql = "select ".$this->conf['relunion']." as IDO , ".$this->conf['relid']." as RELID from ".$this->conf['reltab']." where ".$this->conf['union']." ='".$idMain."'";
        $c = new con($sql);
        $aRes = array();
        while ($r = $c->getResult()) {
            $aRes[]= $r['IDO'];
        }
    	$accionEjec = 'before_insert';
		if ($this->trigger !== false && isset($this->triggerParamsEsp) && !empty($this->triggerParamsEsp)) {
			if (in_array('insert', $this->getTriggerOn())) {
				$_datosForTrigger = array(
					"valueFLD" => $value
				);
				$accionEjec = 'before_insert';
				$cad = $this->runTriggerNew($id[2], $accionEjec, $_datosForTrigger);
				if (is_array($cad)) {
					if (is_string($cad[0]) && $cad[0]=="DoNoInsertBecauseTriggerSays") {
						return "noInsertBecauseFileEsp";
					}
				}
			}
		}
        $toInsert = array_diff($aValues, $aRes);
        $toDelete = array_diff($aRes, $aValues);

        if (sizeof($toInsert > 0)) {
            foreach ($toInsert as $insertValue) {
                $acampos = array();
                $avalores = array();

                $acampos[] = $this->conf['union'];
                $avalores[] = "'{$idMain}'";
                $acampos[] = $this->conf['relunion'];
                $avalores[] = "'{$insertValue}'";

                if (isset($this->conf['reldefaults'])) {
                    $aRelDefaults = explode("|", $this->conf["reldefaults"]);
                    foreach ($aRelDefaults as $relDefault) {
                        list($acampos[], $avalores[]) = explode('=', $relDefault);
                    }
                }

                $sql = "insert into {$this->conf["reltab"]} (".implode(",", $acampos).") values (".implode(",", $avalores).")";
                $c = new con($sql);
            }
        }

        if (sizeof($toDelete > 0)) {
            foreach ($toDelete as $deleteValue) {
                $sql = "delete from {$this->conf["reltab"]} where {$this->conf["relunion"]} = '{$deleteValue}' and {$this->conf["union"]} = '{$idMain}'";
                $c = new con($sql);
            }
        }

        $accionEjec = 'insert';
        if ($this->trigger !== false && isset($this->triggerParamsEsp) && !empty($this->triggerParamsEsp)) {
			if (in_array('insert', $this->getTriggerOn())) {
				$_datosForTrigger = array(
					"valueFLD" => $value
				);
				$cad = $this->runTriggerNew($idM, $accionEjec, $_datosForTrigger);
				if (is_array($cad)) {
                    return $cad;
                }
			}
		} else {
	        if ($this->getTrigger($idMain, $accionEjec)!==false) {
	            if (in_array($accionEjec, $this->triggerOn) || in_array('update', $this->triggerOn)) {
	                $funcionTrigger = $this->getTrigger($idMain, $accionEjec).';';
	                eval("\$cad = $funcionTrigger");
	                if (is_array($cad)) {
	                    return $cad;
	                }
	            }
	        }
		}
        return 'noInsertBecauseFileEsp';
    }
}
