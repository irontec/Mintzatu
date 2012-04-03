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
class tablon_FLDmultiselectmulti extends tablon_FLDtags {

	public function getType() {
		return "multiselect";
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
						$avalores[] = "'".$id[2]."'";
						$acampos[] = $this->conf['relunion'];
						$avalores[] = "'".$r['ID']."'";
						if (isset($this->conf['reltabcond'])){
							list($acampos[],$avalores[]) = explode('=',$this->conf['reltabcond'] ) ;
						}
						$sql2 = "insert into ".$this->conf['reltab']." (".implode(',',$acampos).") values (".implode(',',$avalores).") ";
						$c2 = new con($sql2);
						if($c2->error())
							return array("noInsertBecauseFileEsp","error",$c2->getErrorNumber(), $c2->getError());
					}
				}else{
					$sql2 = "insert into ".$this->conf['tab']." (".$this->conf['showfield'].") values ('".mysql_real_escape_string($v)."') ";
					$c2 = new con($sql2);
					$i = $c2->getId();
					$acampos = array();
					$avalores = array();
					$acampos[] = $this->conf['union'];
					$avalores[] = "'".$id[2]."'";
					$acampos[] = $this->conf['relunion'];
					$avalores[] = "'".$i."'";
					if (isset($this->conf['reltabcond'])){
						list($acampos[],$avalores[]) = explode('=',$this->conf['reltabcond'] ) ;
					}

					$sql3 = "insert into ".$this->conf['reltab']." (".implode(',',$acampos).") values (".implode(',',$avalores).") ";

					$c3 = new con($sql3);
					if($c3->error())
							return array("noInsertBecauseFileEsp","error",$c3->getErrorNumber(), $c3->getError());
				}
			}

		}

		if (sizeof($aRes)>0){
			foreach ($aRes as $foo=>$v){
				$sql2 = "delete from ".$this->conf['reltab']." where  ".$this->conf['relid']." = '".$v."'";
				$c2 = new con($sql2);
				if($c2->error())
					return array("noInsertBecauseFileEsp","error",$c2->getErrorNumber(), $c2->getError());
			}

		}
		$accionEjec = 'insert';
		if($this->trigger !== false && isset($this->triggerParamsEsp) && !empty($this->triggerParamsEsp)){
			if(in_array('insert',$this->getTriggerOn())){
				$_datosForTrigger = array(
					"valueFLD" => $value
				);
				$accionEjec = 'insert';
				$cad = $this->runTriggerNew($id[2],$accionEjec,$_datosForTrigger);
				return $cad;
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

	/* Aunque el resto de parámetros además de $value no se usan, hay que ponerlas por ser una clase que extiende a otra (tags) */
	public function drawTableValueEdit($value,$clone=false,$disabled=false) {
		$html = '';
		$aTabsIzq = array();
		$aTabsOn = array();
		$lacadenaL = "";
		$lacadenaI = "";
		if(isset($this->conf['tab_mult'])){
			$aTabsIzq = explode("|",$this->conf['tab_mult']);
			$aTabsOn = explode("|",$this->conf['tabsOn']);
			//$lacadenaI = " left join ".$this->conf['tab']." on (".$this->conf['tab'].".".$this->conf['id']."=".$this->conf['reltab'].".".$this->conf['relunion'].") ";
			$lacadenaL = "";
			for($i=0;$i<sizeof($aTabsIzq);$i++){
				$lacadenaL .= " left join ".$aTabsIzq[$i]." ";
				if(isset($aTabsOn[$i])) $lacadenaL .= " on (".$aTabsOn[$i].") ";
			}
			//$lacadenaT = $lacadenaI.$lacadenaL;
		}
		$lacadenaT = $lacadenaI.$lacadenaL;

		$arr = explode($this->conf['separator'],$this->drawTableValue($value));

		//$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
		$sql = "select ".$this->conf['showfield']." as f from ".$this->conf['tab']." ".$lacadenaT;

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

		/*$sql .= "where (".$this->conf['showfield']." in ('".implode("','",$arr)."')) ";
		$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
		if(isset($this->conf['tabshowconds'])){
			if($conds != false){ $conds .= " and "; }
			$conds .= $this->conf['tabshowconds'];
		}
		if ($conds&&trim($conds)!="") $sql.= " OR (".$conds.")";*/

		if(isset($this->conf['showorderby']) && !empty($this->conf['showorderby'])){
			$sql .= " order by ".$this->conf['tab'].".".$this->conf['showorderby'];
		}
		$c = new con($sql);

		$html.= '<select  multiple="multiple '.(($this->isRequired())? 'required':'').'"  size="1"  style="visibility:hidden;"  class="multiselect"  name="'.$this->getSQLFLD().'" '.$extra.'>';
		while ($r=$c->getResult()){
			//$areturn[$r['f']] = (in_array($r['f'],$arr))? "selected":".";
			$html.= '<option value="'.$r['f'].'"  '.((in_array($r['f'],$arr))? "selected=\"selected\"":"").'>'.$r['f'].'</option>';
		}
		$html .= '</select><input type="hidden" bind="true"'
		      . ' name="' .$this->getSQLFLD()
              . '" id="' . $this->getSQLFLD() . '_' . $this->getCurrentID()
		      . '" class="multivals"  value="' . $this->drawTableValue($value)
		      . '" />';

		return $html;


		//<textarea name="'.$this->getSQLFLD().'"'.(($this->isRequired())? ' class="required"':'').'  class="multiselect" 		autocompletetab="'.$this->conf['tab'].'" 		autocompletefield = "'.$this->conf['showfield'].'">'.$this->drawTableValue($value).'</textarea>';

	}

	public function drawEditJSON() {
		$id = explode('::',$_GET['id']);
		$aTabsIzq = array();
		$aTabsOn = array();
		$lacadenaL = "";
		$lacadenaI = " left join ".$this->conf['tab']." on (".$this->conf['tab'].".".$this->conf['id']."=".$this->conf['reltab'].".".$this->conf['relunion'].") ";

		if(isset($this->conf['tab_mult'])){
			$aTabsIzq = explode("|",$this->conf['tab_mult']);
			$aTabsOn = explode("|",$this->conf['tabsOn']);
			$lacadenaI = " left join ".$this->conf['tab']." on (".$this->conf['tab'].".".$this->conf['id']."=".$this->conf['reltab'].".".$this->conf['relunion'].") ";
			$lacadenaL = "";
			for($i=0;$i<sizeof($aTabsIzq);$i++){
				$lacadenaL .= " left join ".$aTabsIzq[$i]." ";
				if(isset($aTabsOn[$i])) $lacadenaL .= " on (".$aTabsOn[$i].") ";
			}
			//$lacadenaT = $lacadenaI.$lacadenaL;
		}
		$lacadenaT = $lacadenaI.$lacadenaL;
		$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
		/*$sql = "select group_concat(".$this->conf['showfield']." SEPARATOR '|') as res
		from ".$this->conf['reltab']." left join ".$this->conf['tab']." on (".$this->conf['tab'].".".$this->conf['id']."=".$this->conf['reltab'].".".$this->conf['relunion'].") where
		".$this->conf['reltab'].".".$this->conf['union']." = '".$id[2]."'";*/
		$sql = "select group_concat(".$this->conf['showfield']." SEPARATOR '|') as res
		from ".$this->conf['reltab']." ".$lacadenaT." where
		".$this->conf['reltab'].".".$this->conf['union']." = '".$id[2]."'";
		//$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
		if ($conds&&trim($conds)!="") $sql.= " and ".$conds;
		if(isset($this->conf['showorderby']) && !empty($this->conf['showorderby'])){
			$sql .= " order by ".$this->conf['tab'].".".$this->conf['showorderby'];
		}

		$a=array();
		$c= new con($sql);
		$r = $c->getResult();
		$arr = explode('|',$r['res']);

		$sql = "select ".$this->conf['showfield']." as f from ".$this->conf['tab']."  ".$lacadenaL;

		/*$sql .= "where (".$this->conf['showfield']." in ('".implode("','",$arr)."')) ";
		$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
		if(isset($this->conf['tabshowconds'])){
			if($conds != false){ $conds .= " and "; }
			$conds .= $this->conf['tabshowconds'];
		}
		if ($conds&&trim($conds)!="") $sql.= " OR (".$conds.")";*/

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
		/*if ($conds&&trim($conds)!="") $sql.= " where ".$conds;*/
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

	public function getSQL($tab,$alias = true) {

		$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;

		$aTabsIzq = array();
		$aTabsOn = array();
		$lacadenaL = "";
		$lacadenaI = " left join ".$this->conf['tab']." on (".$this->conf['tab'].".".$this->conf['id']."=".$this->conf['reltab'].".".$this->conf['relunion'].") ";

		if(isset($this->conf['tab_mult'])){
			$aTabsIzq = explode("|",$this->conf['tab_mult']);
			$aTabsOn = explode("|",$this->conf['tabsOn']);
			$lacadenaI = " left join ".$this->conf['tab']." on (".$this->conf['tab'].".".$this->conf['id']."=".$this->conf['reltab'].".".$this->conf['relunion'].") ";
			$lacadenaL = "";
			for($i=0;$i<sizeof($aTabsIzq);$i++){
				$lacadenaL .= " left join ".$aTabsIzq[$i]." ";
				if(isset($aTabsOn[$i])) $lacadenaL .= " on (".$aTabsOn[$i].") ";
			}
			//$lacadenaT = $lacadenaI.$lacadenaL;
		}
		$lacadenaT = $lacadenaI.$lacadenaL;
		$ret = "( select group_concat(".$this->conf['tab'].".".$this->conf['showfield']." SEPARATOR '".$this->conf['separator']."')
		from ".$this->conf['reltab']." ".$lacadenaT." where
		".$this->conf['reltab'].".".$this->conf['union']." = ".$tab.".".((isset($this->conf['union2']))? $this->conf['union2']:$this->conf['union'] ) ." ".(($conds&&trim($conds)!="")?  " and ".$conds :"" )." )
		";

		if ($alias) $ret .= " as '".$this->getAlias()."'";
		return $ret;
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
						if($c2->error())
							return array("noInsertBecauseFileEsp","error",$c2->getErrorNumber(), $c2->getError());
					}

				}else{
					if(!(i::detectUTF8($v))){
						$v = utf8_encode($v);
					}
					$sql2 = "insert into ".$this->conf['tab']." (".$this->conf['showfield'].") values ('".mysql_real_escape_string($v)."') ";
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
					if($c3->error())
						return array("noInsertBecauseFileEsp","error",$c3->getErrorNumber(), $c3->getError());
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
				if($c2->error())
					return array("noInsertBecauseFileEsp","error",$c2->getErrorNumber(), $c2->getError());
			}

		}
		$accionEjec='';
		if($this->trigger !== false && isset($this->triggerParamsEsp) && !empty($this->triggerParamsEsp)){
			if(in_array('insert',$this->getTriggerOn()) || in_array('update',$this->getTriggerOn())){
				$_datosForTrigger = array(
					"valueFLD" => $value
				);
				if(in_array('update',$this->triggerOn)) $accionEjec = 'update';
				else $accionEjec = "insert";
				$cad = $this->runTriggerNew($idM,$accionEjec,$_datosForTrigger);
				if(is_array($cad)){
					return $cad;
				}
			}
		}else
			if ($this->getTrigger($idM,$accionEjec)!==false){
				if(in_array('insert',$this->triggerOn) || in_array('update',$this->triggerOn)){
					if(in_array('update',$this->triggerOn)) $accionEjec = 'update';
					else $accionEjec = "insert";
					$funcionTrigger = $this->getTrigger($idM, $accionEjec).';';
					eval("\$cad = $funcionTrigger");
					if(is_array($cad)){
						return $cad;
					}
				}
			}
		return 'noInsertBecauseFileEsp';
	}

}


?>