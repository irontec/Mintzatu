<?php
/*
 * Clase para campo tipo TAGS
 * Sirve para mostrar un textarea donde se pueden ir añadiendo tags separados por SEPARATOR.
 * En caso de no existir un tag se añade a una tabla de tags.
 * Relaciones de n-m, asi que se utilizan 3 tablas en total.
 *
 *
 * ::::PLT::::
 * alias: nombre de campo a mostrar en la web
 * reltab: tabla de relaciones
 * relid: id de la tabla de relaciones
 * union: campo de la tabla de relaciones que referencia a los ids de la primera tabla o elemento actual (id_tabla_1)
 * relunion: campo de la tabla de relaciones que referencia a los ids de la segunda tabla (id_tabla_2)
 * tab: nombre de la tabla relacionada (tabla_2)
 * tabconds: condición necesaria en la tabla2 para poder seleccionar el elemento (p.e. tabla2.deleted = '0')
 * id: id de la tabla 2
 * showfield: campo a mostrar de la segunda tabla
 * separator: separador utilizado para diferenciar entre tags. Por defecto ','
 * clean: campo en el que almacenar el showfield limpio para poder utilizar como id en las url's
 *
 * @author Lander Ontoria <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */
class tablon_FLDtags extends tablon_FLD {


	public function getType() {
		return "textareaTags";
	}


	public function getMysqlValue($value) {

		$c = new con("select 1;");
		$aValues = explode($this->conf['separator'],$value);
		$aValues = array_unique($aValues);
		$id = explode('::',$_GET['id']);
		if(!is_array($id) || !isset($id[2]) || $id[2] == 0 || $id[2] == false){
			return 'noInsertBecauseFileEsp';
		}
		$sql = "select ".$this->conf['relunion']." as IDO , ".$this->conf['relid']." as RELID from ".$this->conf['reltab']." where ".$this->conf['union']." ='".$id[2]."'";
		$c = new con($sql);
		$aRes = array();
		while ($r = $c->getResult()) $aRes[$r['IDO']]= $r['RELID'];
		//var_dump($aRes);
		if (sizeof($aValues)>0){
			foreach ($aValues as $v) {
				if (isset($this->conf['caser'])){
					switch($this->conf['caser']){
						case "allfirstcapital":
							$v = mb_strtolower(mb_ucwords($v));
						break;
						case "alltolowercase":
							$v = mb_strtolower($v);
						break;
					}
				}
				if (trim($v)=="") continue;
				$v = mysql_real_escape_string(trim($v));
				$sql = "select ".$this->conf['id']." as ID from ".$this->conf['tab']." where ".$this->conf['showfield']." = '".$v."' limit 1";
				$c = new con($sql);
				if ($c->getNumRows()>0){
					$r = $c->getResult();
					if (isset($aRes[$r['ID']]) ) {
						unset($aRes[$r['ID']]);
						continue;
					}else{
						//$sql2 = "delete from ".$this->conf['reltab']." where  ".$this->conf['relid']." = '".$aRes[$r['ID']]."'";
						$sql2 = "insert into ".$this->conf['reltab']." (".$this->conf['union'].",".$this->conf['relunion'].") values ('".$id[2]."','".$r['ID']."') ";
						$c2 = new con($sql2);
					}

				}else{
					if(!(i::detectUTF8($v))){
						$v = utf8_encode($v);
					}

					$aFields = array();
					$fValues = array();

					$aFields[] = $this->conf['showfield'];
					$fValues[] = "'".mysql_real_escape_string($v)."'";

					if($clean = $this->toClean()){
						$aFields[] = $clean;
						//$cleanValue = $this->doClean($clean,$v);
						$aClean = $this->toCleanUnique();
						if($aClean!==false && is_array($aClean)){
							if(!isset($aTmpClean) || !is_array($aTmpClean)){
								$aTmpClean = array();
							}
							$cleanValue = $this->doClean($clean,$v,false,false,$aClean);
							$tmpCont = 0;
							while(in_array($cleanValue,$aTmpClean)){
								if($tmpCont>50) die();
								$a = explode('_',$cleanValue);
								$z = $a[(sizeof($a)-1)];
								if (is_numeric($z)){
									$x = "_".$z;
									$xx ="_".$tmpCont;
									$cleanValue = str_replace($x,$xx,$cleanValue);
									$tmpCont++;
								}else{
									$cleanValue = $cleanValue."_".($tmpCont);
								}
							}
							$aTmpClean[] = $cleanValue;
						}else{
							$cleanValue = $this->doClean($clean,$v);
						}
						if ($cleanValue === false) {
							return array("99","Error with clean field [$clean => ".$this->fields[$i]->getSQLFLD()."]");
						}
						if(empty($cleanValue) || $cleanValue == "''" || $cleanValue == ''){
							$cleanValue = $this->valueOnEmpty($cleanValue);
						}
						$fValues[] = "'".$cleanValue."'";
					}

					$sql2 = "insert into ".$this->conf['tab']." (".implode(",",$aFields).") values (".implode(",",$fValues).") ";

					$c2 = new con($sql2);
					$i = $c2->getId();
					$sql3 = "insert into ".$this->conf['reltab']." (".$this->conf['union'].",".$this->conf['relunion'].") values ('".$id[2]."','".$i."') ";

					$c3 = new con($sql3);
				}
			}

		}
		if (sizeof($aRes)>0){
			foreach ($aRes as $foo=>$v){
				if(!(i::detectUTF8($v))){
					$v = utf8_encode($v);
				}
				$sql2 = "delete from ".$this->conf['reltab']." where  ".$this->conf['relid']." = '".$v."'";
					$c2 = new con($sql2);
			}

		}
		return 'noInsertBecauseFileEsp';
	}

	public function doClean($c,$v,$id=false,$idc=false,$cleanUniqueFlds=false){
		$cont=0;
		$v = i::clean($v);
		while ($this->existe($c,$v,$id,$idc,$cleanUniqueFlds)) {
			$sql = "select ".$c." as url from ".$this->conf["tab"]." where ".$c." = '".$v."' ";
			$con = new con($sql);
			if ($con->error()) return false;
			$r=$con->getResult();
			$a = explode('_',$r['url']);
			$z = $a[(sizeof($a)-1)];
			if (is_numeric($z)){
				$x = "_".$z;
				$xx ="_".$cont;
				$v = str_replace($x,$xx,$v);
			}else{
				$v = $v."_".($cont);
			}
			$cont++;
		}
		return $v;
	}

	protected function existe($c,$v,$id=false,$idc=false,$cleanUniqueFlds=false) {
		if($cleanUniqueFlds!==false && is_array($cleanUniqueFlds)){
			$cond = array();
			$cond2 = array();
			for($i=0;$i<count($cleanUniqueFlds);$i++){
				$cond[] = $cleanUniqueFlds[$i]."='".$v."'";
				if ( $id!==false && $idc!==false && $cleanUniqueFlds[$i]!=$c){
					$cond2[] = $cleanUniqueFlds[$i]."='".$v."'";
				}
			}
			if ( $id!==false && $idc!==false ){
				$sql = "select ".$c.",".$id." from ".$this->conf["tab"]." where
				((".implode(" or ",$cond).") and ".$idc." != '".$id."') or
				((".implode(" or ",$cond2).") and ".$idc." = '".$id."')";
			}else{
				$sql = "select ".$c.",".$id." from ".$this->tab." where (".implode(" or ",$cond).") ";
			}
		}else{
			$sql = "select ".$c." from ".$this->conf["tab"]." where ".$c." = '".$v."' ";
			if ( $id!==false && $idc!==false ) $sql.= " and ".$idc." != '".$id."'";
		}
		$con = new con($sql);
		return ($con->getNumRows()!==0);
		/*$sql = "select ".$c." from ".$this->conf["tab"]." where ".$c." = '".$v."' ";
		if ( $id!==false && $idc!==false ) $sql.= " and ".$idc." != '".$id."'";
		$con = new con($sql);
		return ($con->getNumRows()!==0);*/
	}


	public function getSQLFLDRaw($t=false){

			return $this->getSQL($t,$this->alias);
	}
	public function getSQL($tab,$alias = true) {
		// Si se quiere que haya alguna condición extra en el select )
		$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;

		if(isset($this->conf['showfieldTransForShow'])){
			/* Nueva opción para cuando se quiere mostrar los tags de diferente manera cuando el campo no se está editando (un concat por ejemplo...)
			 * En atributo "showfieldTransForShow" indicaríamos la sentencia del select a mostrar, que  contendrá un string %replaceval% para
			 * sustituirlo por el valor real de "showfield" */
			$toShow = preg_replace("/%replaceval%/",$this->conf['tab'].".".$this->conf['showfield'],$this->conf['showfieldTransForShow']);
			$ret = "(SELECT GROUP_CONCAT(DISTINCT " . $toShow . " SEPARATOR '" . $this->conf['separator'] . "')"
			     . " FROM " . $this->conf['reltab']
			     . " LEFT JOIN " . $this->conf['tab'] . " ON (".$this->conf['tab'].".".$this->conf['id']."=".$this->conf['reltab'].".".$this->conf['relunion'].")"
			     . " WHERE "
			     . $this->conf['reltab'] . "." . $this->conf['union'] . " = " . $tab . "." . ((isset($this->conf['union2']))? $this->conf['union2']:$this->conf['union'] )
			     . " " . (($conds&&trim($conds)!="")?  " AND " . $conds : "" )
			     .")";
		}else{
			$ret = "(SELECT GROUP_CONCAT(DISTINCT " . $this->conf['tab'] . "." . $this->conf['showfield'] . " SEPARATOR '" . $this->conf['separator']."')"
			     . " FROM " . $this->conf['reltab']
			     . " LEFT JOIN " . $this->conf['tab'] . " ON (" . $this->conf['tab'] . "." . $this->conf['id'] . "=" . $this->conf['reltab'] . "." . $this->conf['relunion'].")"
			     . " WHERE "
			     . $this->conf['reltab'].".".$this->conf['union']." = ".$tab.".".((isset($this->conf['union2']))? $this->conf['union2']:$this->conf['union'] )
			     . " ".(($conds&&trim($conds)!="")?  " AND ".$conds :"" )
			     . ")";
		}
		if ($alias) $ret .= " AS '".$this->getAlias()."'";
		return $ret;
	}


	/*public function Edit($v) {
		return $this->drawTableValue($v);
	}*/
	public function drawTableValueEdit($value,$clone=false,$disabled=false) {
		return '<textarea name="' . $this->getSQLFLD()
		     . '" id="' . $this->getSQLFLD() . '_' . $this->getCurrentID()
             . '" class="tags' . (($this->isRequired())? ' required':'')
             . '" autocompletetab="' . $this->conf['tab']
             . '" autocompletefield = "' . $this->conf['showfield'].'">'
             . $this->drawTableValue($value)
             . '</textarea>';

	}

	public function insertAfterCreateMain($idMain, $value) {
		$c = new con("select 1;");
		$aValues = explode($this->conf['separator'],$value);
		$aValues = array_unique($aValues);
		//$id = explode('::',$_GET['id']);
		$idM = $idMain;
		$sql = "select ".$this->conf['relunion']." as IDO , ".$this->conf['relid']." as RELID from ".$this->conf['reltab']." where ".$this->conf['union']." ='".$idM."'";
		$c = new con($sql);
		$aRes = array();
		while ($r = $c->getResult()) $aRes[$r['IDO']]= $r['RELID'];
		//var_dump($aRes);
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
		if (sizeof($aValues)>0){
			foreach ($aValues as $v) {
				if (trim($v)==""||trim($v)=="Select options") continue;
				$v = mysql_real_escape_string(trim($v));
				$sql = "select ".$this->conf['id']." as ID from ".$this->conf['tab']." where ".$this->conf['showfield']." = '".$v."' limit 1";
				$c = new con($sql);
				if ($c->getNumRows()>0){
					$r = $c->getResult();
					if (isset($aRes[$r['ID']]) ) {
						unset($aRes[$r['ID']]);
						continue;
					}else{
						$acampos = array();
						$avalores = array();
						$acampos[] = $this->conf['union'];
						$avalores[] = "'".$idM."'";
						$acampos[] = $this->conf['relunion'];
						$avalores[] = "'".$r['ID']."'";
						if (isset($this->conf['reltabcond'])){
							list($acampos[],$avalores[]) = explode('=',$this->conf['reltabcond'] ) ;
						}
						$sql2 = "insert into ".$this->conf['reltab']." (".implode(',',$acampos).") values (".implode(',',$avalores).") ";
						$c2 = new con($sql2);
					}

				}else{
					if(!(i::detectUTF8($v))){
						$v = utf8_encode($v);
					}


					$aFields = array();
					$fValues = array();
					$aFields[] = $this->conf['showfield'];
					$fValues[] = mysql_real_escape_string($v);
					if(isset($this->conf['clean'])){
						$aFields[] = $this->conf['clean'];
						$fValues[] = i::clean(mysql_real_escape_string($v));
					}
					$ac=array();

					foreach ($fValues as $foo ) $ac[] = "'".$foo."'";

					$sql2 = "insert into ".$this->conf['tab']." (".implode(",",$aFields).") values (" .implode(",",$ac).") ";

					$c2 = new con($sql2);
					$i = $c2->getId();
					$acampos = array();
					$avalores = array();
					$acampos[] = $this->conf['union'];
					$avalores[] = "'".$idM."'";
					$acampos[] = $this->conf['relunion'];
					$avalores[] = "'".$i."'";
					if (isset($this->conf['reltabcond'])){
						list($acampos[],$avalores[]) = explode('=',$this->conf['reltabcond'] ) ;
					}

					$sql3 = "insert into ".$this->conf['reltab']." (".implode(',',$acampos).") values (".implode(',',$avalores).") ";

					$c3 = new con($sql3);
				}
			}

		}

		if (sizeof($aRes)>0){
			foreach ($aRes as $foo=>$v){
				if(!(i::detectUTF8($v))){
					$v = utf8_encode($v);
				}
				$sql2 = "delete from ".$this->conf['reltab']." where  ".$this->conf['relid']." = '".$v."'";
					$c2 = new con($sql2);
			}

		}
		$accionEjec = "";
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
			if ($this->getTrigger($idM,$accionEjec)!==false){
				if(in_array('insert',$this->triggerOn) || in_array('update',$this->triggerOn)){
					if(in_array('insert',$this->triggerOn)){
						$accionEjec = 'insert';
					}else{
						$accionEjec = 'update';
					}
					$funcionTrigger = $this->getTrigger($idM,$accionEjec).';';
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
		$sql = "select group_concat(distinct  ".$this->conf['tab'].".".$this->conf['showfield']." SEPARATOR '".$this->conf['separator']."') as res
		from ".$this->conf['reltab']." left join ".$this->conf['tab']." on (".$this->conf['tab'].".".$this->conf['id']."=".$this->conf['reltab'].".".$this->conf['relunion'].") where
		".$this->conf['reltab'].".".$this->conf['union']." = '".$id[2]."'";
		$a=array();
		$c= new con($sql);
		$r = $c->getResult();
		$aRet['values'] = $r['res'];
		$aRet['name'] = $this->getSQLFLD();
		$aRet['class'] = "tags";
		$aRet['autocompletetab'] = $this->conf['tab'];
		$aRet['autocompletetabconds'] = $this->conf['tabconds'];
		$aRet['autocompletefield'] = $this->conf['showfield'];
		$aRet['separator'] = $this->conf['separator'];
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


	public function getTab(){
		return $this->conf['tab'];
	}
}


?>
