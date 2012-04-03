<?php
/*
 * Clase para campo tipo MultiselectNG
 * Sirve para poder seleccionar varias filas de otra tabla y guardar la relación en una tabla relacional
 *
 * ::::PLT::::
 * alias: nombre de campo a mostrar en la web
 * id: id de la tabla 1 o elemento actual [sólo es necesario si difiere del campo "union", aunque es recomendable ponerlo y punto :p]
 * tab: nombre de la tabla relacionada (tabla_2)
 * tabconds: condición necesaria en la tabla2 para poder seleccionar el elemento (p.e. tabla2.deleted = '0')
 * tabid: id de la tabla 2
 * showfield: campo a mostrar de la segunda tabla
 * showfield_sql: sql necesario para mostrar cada uno de los datos (sobreescribe a showfield)
 * separator: separador utilizado para los datos obtenidos de "showfield". No influye en el separador para los id's, que siempre será una coma
 * reltab: tabla de relaciones
 * relid: id de la tabla de relaciones
 * union: campo de la tabla de relaciones que referencia a los ids de la primera tabla o elemento actual (id_tabla_1)
 * relunion: campo de la tabla de relaciones que referencia a los ids de la segunda tabla (id_tabla_2)
 * reldefaults: campos a los que se quiere añadir un valor por defecto, separados por pipes (|) (p.e. "active='1'|insert_date=now()")
 * extrafield: campo extra de la tabla de relaciones, a parte de los campos de ids
 * extravalue: valor del campo extrafield que se guardará al hacer un insert
 *
 *
 * @author Alayn Gortazar <alayn@irontec.com>
 * @version 1.0
 * @package karma
 */
class tablon_FLDmultiselectng extends tablon_FLD {


	public function getType() {
		return "multiselectng";
	}

    public function loadJS() {
        return array(
        "../modules/tablon/scripts/jqueryMultiSelect.js"
        );
    }

    public function loadCSS() {
        return array(
            "../modules/tablon/css/jqueryMultiSelect.css"
        );
    }


    //Sobreescribimos getMysqlValue, y devolvemos noInsertBecauseFileEsp para que no intente guardar nada la plantilla
    //Aquí nos encargamos de hacerlo todo :)
    public function getMysqlValue($value) {

        $c = new con("select 1;");
        $aValues = explode(",",$value);
        $aValues = array_unique($aValues);
        $id = explode('::',$_GET['id']);
        if(!is_array($id) || !isset($id[2]) || $id[2] == 0 || $id[2] == false){
            return 'noInsertBecauseFileEsp';
        }

        //Obtenemos las relaciones previamente existentes
        $sql = "select ".$this->conf['relunion']." as IDO , ".$this->conf['relid']." as RELID from ".$this->conf['reltab']." where ".$this->conf['union']." ='".$id[2]."'";
        if (isset($this->conf['extrafield']) && $this->conf['extrafield'] != '') {
            $sql .= " AND ".$this->conf['extrafield']." = '".$this->conf['extravalue']."'";
        }

        $c = new con($sql);
        $aRes = array();
        while ($r = $c->getResult()){
        	$aRes[]= $r['IDO'];
        }
        $to_insert = array_diff($aValues,$aRes);
        $to_delete = array_diff($aRes,$aValues);

        if(sizeof($to_insert > 0)){
        	foreach($to_insert as $insertValue){
                $acampos = array();
                $avalores = array();

                $acampos[] = $this->conf['union'];
                $avalores[] = "'{$id[2]}'";
                $acampos[] = $this->conf['relunion'];
                $avalores[] = "'{$insertValue}'";

                if (isset($this->conf['extrafield']) && $this->conf['extrafield'] != '') {
                	$acampos[] = $this->conf['extrafield'];
                	$avalores[] = "'{$this->conf['extravalue']}'";
                }

                if (isset($this->conf['reldefaults'])){
                	$aRelDefaults = explode("|",$this->conf["reldefaults"]);
                	foreach($aRelDefaults as $relDefault){
	                    list($acampos[],$avalores[]) = explode('=',$relDefault ) ;
                	}
                }

        		$sql = "insert into {$this->conf["reltab"]} (".implode(",",$acampos).") values (".implode(",",$avalores).")";
        		$c = new con($sql);
        	}
        }

        if(sizeof($to_delete > 0)){
        	foreach($to_delete as $deleteValue){
        		$sql = "delete from {$this->conf["reltab"]} where {$this->conf["relunion"]} = '{$deleteValue}' and {$this->conf["union"]} = '{$id[2]}'";
                $c = new con($sql);
        	}
        }

        $accionEjec = 'insert';
        if($this->trigger !== false && isset($this->triggerParamsEsp) && !empty($this->triggerParamsEsp)){
			if(in_array('insert',$this->getTriggerOn())){
				$_datosForTrigger = array(
					"valueFLD" => $value
				);
				$cad = $this->runTriggerNew($id[2],$accionEjec,$_datosForTrigger);
				if(is_array($cad)){
	            	return $cad;
	            }
			}
		}else
	        if ($this->getTrigger($id[2],$accionEjec)!==false){
	            if(in_array($accionEjec,$this->triggerOn) || in_array('update',$this->triggerOn)){
	                $funcionTrigger = $this->getTrigger($id[2],$accionEjec).';';
	                eval("\$cad = $funcionTrigger");
	                if(is_array($cad)){
	                    return $cad;
	                }
	            }
	        }
        return 'noInsertBecauseFileEsp';
    }


	public function getSQLFLDRaw($t=false){

			return $this->getSQL($t,$this->alias);
	}

	public function getSQL($tab,$alias = true) {

		$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
		$showfield = (isset($this->conf["showfield_sql"]) && $this->conf["showfield_sql"] !== "")? $this->conf["showfield_sql"]:$this->conf['tab'] . "." . $this->conf['showfield'];
		$ret = "(SELECT GROUP_CONCAT(" . $showfield . " SEPARATOR '" . $this->conf['separator'] . "')"
		     . " FROM " . $this->conf['tab']
		     . " LEFT JOIN " . $this->conf['reltab'] . " ON (" . $this->conf['tab'] . "." . $this->conf['tabid'] . "=" . $this->conf['reltab'] . "." . $this->conf['relunion'].")"
		     . " WHERE "
		     . $this->conf['reltab'] . "." . $this->conf['union'] . " = " . $tab . "." . ((isset($this->conf['id']))? $this->conf['id'] : $this->conf['union'] )
		     . " " . ((isset($this->conf['extrafield']))? " AND " . $this->conf['reltab'] . "." . $this->conf['extrafield'] ." = '" . $this->conf['extravalue'] . "'": "" )
		     . " " . (($conds && trim($conds)!="")? " AND " . $conds : "" )
		     . " )";

		if ($alias) {
		    $ret .= " AS '" . $this->getAlias() . "'";
		}
		return $ret;
	}


	/*public function Edit($v) {
		return $this->drawTableValue($v);
	}*/
    public function drawTableValueEdit($value,$clone=false,$disabled=false) {
        $html = '';

        $arr = explode($this->conf['separator'],$this->drawTableValue($value));

        $showfield = (isset($this->conf["showfield_sql"]) && $this->conf["showfield_sql"] !== "")? $this->conf["showfield_sql"]:$this->conf['tab'].".".$this->conf['showfield'];
        $sql = "select ".$showfield." as f, {$this->conf["tabid"]} as tabid from ".$this->conf['tab']." ";

        /*$sql .= "where (".$this->conf['showfield']." in ('".implode("','",$arr)."')) ";
		$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
		if(isset($this->conf['tabshowconds'])){
			if($conds != false){ $conds .= " and "; }
			$conds .= $this->conf['tabshowconds'];
		}
		if ($conds&&trim($conds)!="") $sql.= " OR (".$conds.")";*/

        /*$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
        if ($conds&&trim($conds)!="") $sql.= " where ".$conds;*/
    	$extra = "";
		$conds1 = "";
		if(isset($this->conf['selectLimitOne']) && $this->conf['selectLimitOne']=="1"){
			$extra = "multiselectOnlyOne = 'true'";
			$conds1 .= " (".$this->conf['showfield']." in ('".implode("','",$arr)."')) ";
		}

		$conds2 = "";
		$conds2 = isset($this->conf['tabconds'])? $this->conf['tabconds']:"";
		if(isset($this->conf['tabshowconds'])){
			if($conds2 != ""){ $conds2 .= " and "; }
			$conds2 .= $this->conf['tabshowconds'];
		}

		if($conds1 != ""){
			$sql .= " where ".$conds1;
			if ($conds2&&trim($conds2)!="") $sql.= "  OR (".$conds2.")";
		}else{
			if ($conds2&&trim($conds2)!="") $sql.= " where ".$conds2;
		}

        if(isset($this->conf['showorderby']) && !empty($this->conf['showorderby'])){
            $sql .= " order by ".$this->conf['tab'].".".$this->conf['showorderby'];
        }
        $c = new con($sql);

        $html.= '<select  multiple="multiple '.(($this->isRequired())? 'required':'').'"  size="1"  style="visibility:hidden;"  class="multiselect"  name="'.$this->getSQLFLD().'" '.$extra.'>';
        while ($r=$c->getResult()){
            $html.= '<option value="'.$r['tabid'].'"  '.((in_array($r['f'],$arr))? "selected=\"selected\"":"").'>'.$r['f'].'</option>';
        }
        $html .= '</select><input type="hidden" bind="true"'
              . ' name="' . $this->getSQLFLD()
              . '" id="' . $this->getSQLFLD() . '_' . $this->getCurrentID()
              . '" class="multivals"  value="'.$this->drawTableValue($value)
              . '" />';

        return $html;
    }

    public function insertAfterCreateMain($idMain, $value) {
        $c = new con("select 1;");
        $aValues = explode(",",$value);
        $aValues = array_unique($aValues);

        //Obtenemos las relaciones previamente existentes
        $sql = "select ".$this->conf['relunion']." as IDO , ".$this->conf['relid']." as RELID from ".$this->conf['reltab']." where ".$this->conf['union']." ='".$idMain."'";
        if (isset($this->conf['extrafield']) && $this->conf['extrafield'] != '') {
            $sql .= " AND ".$this->conf['extrafield']." = '".$this->conf['extravalue']."'";
        }
        $c = new con($sql);
        $aRes = array();
        while ($r = $c->getResult()){
            $aRes[]= $r['IDO'];
        }
    	$accionEjec = 'before_insert';
		if($this->trigger !== false && isset($this->triggerParamsEsp) && !empty($this->triggerParamsEsp)){
			if(in_array('insert',$this->getTriggerOn())){
				$_datosForTrigger = array(
					"valueFLD" => $value
				);
				$accionEjec = 'before_insert';
				$cad = $this->runTriggerNew($id[2],$accionEjec,$_datosForTrigger);
				if(is_array($cad)){
					if(is_string($cad[0]) && $cad[0]=="DoNoInsertBecauseTriggerSays"){
						return "noInsertBecauseFileEsp";
					}
				}
			}
		}
        $to_insert = array_diff($aValues,$aRes);
        $to_delete = array_diff($aRes,$aValues);

        if(sizeof($to_insert > 0)){
            foreach($to_insert as $insertValue){
                $acampos = array();
                $avalores = array();

                $acampos[] = $this->conf['union'];
                $avalores[] = "'{$idMain}'";
                $acampos[] = $this->conf['relunion'];
                $avalores[] = "'{$insertValue}'";

            	if (isset($this->conf['extrafield']) && $this->conf['extrafield'] != '') {
                	$acampos[] = $this->conf['extrafield'];
                	$avalores[] = "'{$this->conf['extravalue']}'";
                }

                if (isset($this->conf['reldefaults'])){
                    $aRelDefaults = explode("|",$this->conf["reldefaults"]);
                    foreach($aRelDefaults as $relDefault){
                        list($acampos[],$avalores[]) = explode('=',$relDefault ) ;
                    }
                }

                $sql = "insert into {$this->conf["reltab"]} (".implode(",",$acampos).") values (".implode(",",$avalores).")";
                $c = new con($sql);
            }
        }

        if(sizeof($to_delete > 0)){
            foreach($to_delete as $deleteValue){
                $sql = "delete from {$this->conf["reltab"]} where {$this->conf["relunion"]} = '{$deleteValue}' and {$this->conf["union"]} = '{$idMain}'";
                $c = new con($sql);
            }
        }

        $accionEjec = 'insert';
        if($this->trigger !== false && isset($this->triggerParamsEsp) && !empty($this->triggerParamsEsp)){
			if(in_array('insert',$this->getTriggerOn())){
				$_datosForTrigger = array(
					"valueFLD" => $value
				);
				$cad = $this->runTriggerNew($idM,$accionEjec,$_datosForTrigger);
				if(is_array($cad)){
                    return $cad;
                }
			}
		}else
	        if ($this->getTrigger($idMain,$accionEjec)!==false){
	            if(in_array($accionEjec,$this->triggerOn) || in_array('update',$this->triggerOn)){
	                $funcionTrigger = $this->getTrigger($idMain,$accionEjec).';';
	                eval("\$cad = $funcionTrigger");
	                if(is_array($cad)){
	                    return $cad;
	                }
	            }
	        }
        return 'noInsertBecauseFileEsp';
    }

    public function drawEditJSON() {
        $id = explode('::',$_GET['id']);
        $showfield = (isset($this->conf["showfield_sql"]) && $this->conf["showfield_sql"] !== "")? $this->conf["showfield_sql"]:$this->conf['tab'].".".$this->conf['showfield'];
        $sql = "select group_concat(".$showfield." SEPARATOR '|') as res
        from ".$this->conf['reltab']." left join ".$this->conf['tab']." on (".$this->conf['tab'].".".$this->conf['id']."=".$this->conf['reltab'].".".$this->conf['relunion'].") where
        ".$this->conf['reltab'].".".$this->conf['union']." = '".$id[2]."'";
        $conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
        if ($conds&&trim($conds)!="") $sql.= " and ".$conds;
        if(isset($this->conf['showorderby']) && !empty($this->conf['showorderby'])){
            $sql .= " order by ".$this->conf['tab'].".".$this->conf['showorderby'];
        }

        $a=array();
        $c= new con($sql);
        $r = $c->getResult();
        $arr = explode('|',$r['res']);

        $sql = "select ".$showfield." as f from ".$this->conf['tab']."  ";

        /*$sql .= "where (".$this->conf['showfield']." in ('".implode("','",$arr)."')) ";
		$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
		if(isset($this->conf['tabshowconds'])){
			if($conds != false){ $conds .= " and "; }
			$conds .= $this->conf['tabshowconds'];
		}
		if ($conds&&trim($conds)!="") $sql.= " OR (".$conds.")";*/

        /*$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
        if ($conds&&trim($conds)!="") $sql.= " where ".$conds;*/

    	$conds1 = "";
		if(isset($this->conf['selectLimitOne']) && $this->conf['selectLimitOne']=="1"){
			$aRet['multiselectOnlyOne'] = true;
			$conds1 = " (".$this->conf['showfield']." in ('".implode("','",$arr)."')) ";
		}

		$conds2 = "";
		$conds2 = isset($this->conf['tabconds'])? $this->conf['tabconds']:"";
		if(isset($this->conf['tabshowconds'])){
			if($conds2 != ""){ $conds2 .= " and "; }
			$conds2 .= $this->conf['tabshowconds'];
		}

		if($conds1 != ""){
			$sql .= " where ".$conds1;
			if ($conds2&&trim($conds2)!="") $sql.= "  OR (".$conds2.")";
		}else{
			if ($conds2&&trim($conds2)!="") $sql.= " where ".$conds2;
		}

        if(isset($this->conf['showorderby']) && !empty($this->conf['showorderby'])){
            $sql .= " order by ".$this->conf['tab'].".".$this->conf['showorderby'];
        }
        $c = new con($sql);
        $areturn = array();
        while ($r=$c->getResult()){
            $areturn[$r['f']] = (in_array($r['f'],$arr))? "selected":".";
        }

        $aRet['values'] = $areturn;
        $aRet['name'] = $this->getSQLFLD();
        //$aRet['class'] = "tags";
        $aRet['autocompletetab'] = $this->conf['tab'];
        $aRet['autocompletefield'] = $this->conf['showfield'];
        if(isset($this->conf['req']) && $this->conf['req'] == "1"){
            $aRet['req']=true;
        }
        return json_encode($aRet);
    }


	public function setSearchValue($v) {
		if (preg_match("/value=\"(.*)\"/iU",$v,$ret)) {
			$this->searchValue = $ret[1];
		} else $this->searchValue = $v;
	}


	public function getSearchValue() {
		return $this->searchValue;//'<input type="'.$this->getType().'" name="'.$this->getIndex().'" value="'.$this->searchValue.'" />';
	}

	public function getSQLFLDSearch($alias = false) {

		return ($alias)? $this->getAlias():$this->getIndex();
	}


 	public function getSearchVarType() {
 		return "aHaveSearch";
 	}

	public function getSearchOp() {

		$aSearch = explode($this->conf['separator'],$this->searchValue);
		$res = array("op"=>"like","vals"=>array());

		foreach($aSearch as $s) $res['vals'][] = '\'%'.trim($s).'%\'';

		return $res;
	}

}


?>
