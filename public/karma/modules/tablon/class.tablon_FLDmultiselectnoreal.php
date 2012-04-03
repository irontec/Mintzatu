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
class tablon_FLDmultiselectnoreal extends tablon_FLDtags {
    protected $flagForSearch = false;

	public function getType() {
		return "multiselectnoreal";
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
		//$sql = "select ".$this->conf['relunion']." as IDO , ".$this->conf['relid']." as RELID from ".$this->conf['reltab']." where ".$this->conf['union']." ='".$id[2]."'";
		$sql = "select ".$this->conf['main_fld']." as IDMAIN from ".$this->conf['maintab']." where ".$this->conf['mainid']." = '".$id[2]."'";
		$con = new con($sql);
		$mainr = $con->getResult();
		$idmain = $mainr['IDMAIN'];
		if(isset($this->conf['union_fld_prefix'])){
			$idmain = $this->conf['union_fld_prefix'].$idmain;
		}
		if(isset($this->conf['union_fld_postfix'])){
			$idmain = $idmain.$this->conf['union_fld_postfix'];
		}
		if(isset($this->conf['union_fld_prefix'])){
			$idmain = $this->conf['union_fld_prefix'].$idmain;
		}
		if(isset($this->conf['union_fld_postfix'])){
			$idmain = $idmain.$this->conf['union_fld_postfix'];
		}
		$sql = "select ".$this->conf['relunion_fld']." as IDO , ".$this->conf['relid']." as RELID from ".$this->conf['reltab']." where ".$this->conf['union_fld']." ='".$idmain."'";
		$aRes = array();
		$c = new con($sql);
		while ($r = $c->getResult()){
			$aRes[$r['IDO']]= $r['RELID'];
		}
		if (sizeof($aValues)>0){
			foreach ($aValues as $v) {
				if (trim($v)==""||trim($v)=="Select options") continue;
				$v = mysql_real_escape_string(trim($v));
				$sqlMas = "";
				if(isset($this->conf['lefttabs'])){
					$leftTab=explode('|',$this->conf['lefttabs']);
					$leftCond=explode('|',$this->conf['leftconds']);
					if(sizeof($leftTab)>0 && sizeof($leftCond)>0 && sizeof($leftTab)==sizeof($leftCond)){
						for($i=0;$i<sizeof($leftTab);$i++){
							if($i!=0){
								$sqlMas.=" ";
							}
							$sqlMas .= " left join ".$leftTab[$i]." on(".$leftCond[$i].") ";
						}
					}
				}
				$sql = "select ".$this->conf['id']." as ID,".$this->conf['fld']." as NAME from ".$this->conf['tab'].$sqlMas." where ".$this->conf['showfield']." = '".$v."' limit 1";
				$c = new con($sql);
				if ($c->getNumRows()>0){
					$r = $c->getResult();
					$theName = $r['NAME'];
						if(isset($this->conf['relunion_fld_prefix'])){
							$theName = $this->conf['relunion_fld_prefix'].$theName;
						}

					//echo $theName." <br />";

					if(isset($aRes[$theName])){
						unset($aRes[$theName]);
						continue;
					}else{
						$acampos = array();
						$avalores = array();
						$acampos[] = $this->conf['union_fld'];
						$avalores[] = "'".$idmain."'";
						$acampos[] = $this->conf['relunion_fld'];

						if(isset($this->conf['relunion_fld_postfix'])){
							$theName = $theName.$this->conf['relunion_fld_postfix'];
						}
						$avalores[] = "'".$theName."'";
						if (isset($this->conf['reltabcond'])){
							list($acampos[],$avalores[]) = explode('=',$this->conf['reltabcond'] ) ;
						}
						$sql2 = "insert into ".$this->conf['reltab']." (".implode(',',$acampos).") values (".implode(',',$avalores).") ";
						$c2 = new con($sql2);
					}

				}
			}

		}

		if (sizeof($aRes)>0){
			foreach ($aRes as $foo=>$v){
				$sql2 = "delete from ".$this->conf['reltab']." where  ".$this->conf['relid']." = '".$v."'";
				$c2 = new con($sql2);
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

	/* Aunque el resto de parámetros además de $value no se usan, hay que ponerlas por ser una clase que extiende a otra (tags) */
	public function drawTableValueEdit($value,$clone=false,$disabled=false) {


		$html = '';
		$sqlMas = "";
		if(isset($this->conf['lefttabs'])){
			$leftTab=explode('|',$this->conf['lefttabs']);
			$leftCond=explode('|',$this->conf['leftconds']);
			if(sizeof($leftTab)>0 && sizeof($leftCond)>0 && sizeof($leftTab)==sizeof($leftCond)){
				for($i=0;$i<sizeof($leftTab);$i++){
					if($i!=0){
						$sqlMas.=" ";
					}
					$sqlMas .= " left join ".$leftTab[$i]." on(".$leftCond[$i].") ";
				}
			}
		}

		$arr = explode($this->conf['separator'],$this->drawTableValue($value));

		$sql = "select ".$this->conf['showfield']." as f from ".$this->conf['tab'].$sqlMas." ";

		$extra = "";
		$conds1 = "";
		if (isset($this->conf['selectLimitOne']) && $this->conf['selectLimitOne']=="1" && $this->flagForSearch === false){
			$extra = "multiselectOnlyOne = 'true'";
			$conds1 .= " (".$this->conf['showfield']." in ('".implode("','",$arr)."')) ";
		}
		$conds2 = "";
		$conds2 = isset($this->conf['tabconds'])? $this->conf['tabconds']:"";
		if (isset($this->conf['tabshowconds'])  && $this->flagForSearch === false){
			if($conds2 != ""){ $conds2 .= " and "; }
			$conds2 .= $this->conf['tabshowconds'];
		}

		if ($conds1 != ""){
			$sql .= " where ".$conds1;
			if ($conds2&&trim($conds2)!="") $sql.= "  OR (".$conds2.")";
		} else {
			if ($conds2&&trim($conds2)!="") $sql.= " where ".$conds2;
		}

		if (isset($this->conf['showorderby']) && !empty($this->conf['showorderby'])) {
			$sql .= " order by ".$this->conf['tab'].".".$this->conf['showorderby'];
		}
		$c = new con($sql);
		$html.= '<select  multiple="multiple '.(($this->isRequired())? 'required':'').'"  size="1"  style="display:none;"  class="multiselect"  name="'.$this->getSQLFLD().'" '.$extra.'>';
		while ($r=$c->getResult()) {
		    $html.= '<option value="'.$r['f'].'"  '.((in_array($r['f'],$arr))? "selected=\"selected\"":"").'>'.$r['f'].'</option>';
		}
		$html .= '</select><input type="hidden" bind="true"'
		      . ' name="'.$this->getSQLFLD()
              . '" id="' . $this->getSQLFLD() . '_' . $this->getCurrentID()
		      . '" class="multivals"  value="'.$this->drawTableValue($value)
		      . '" >';

		$this->flagForSearch = false;

		return $html;

	}

	public function drawEditJSON() {
		$id = explode('::',$_GET['id']);
		$sql = "select ".$this->conf['main_fld']." as IDMAIN from ".$this->conf['maintab']." where ".$this->conf['mainid']." = '".$id[2]."'";
		$con = new con($sql);
		$mainr = $con->getResult();
		$idmain = $mainr['IDMAIN'];

		$theName = $this->conf['fld'];
		$prename = "";
		if(isset($this->conf['relunion_fld_prefix'])){
			//$theName = $this->conf['union_fld_prefix'].$theName;
			$prename = $this->conf['relunion_fld_prefix'];
		}
		$postname = "";
		if(isset($this->conf['relunion_fld_postfix'])){
			//$theName = $theName.$this->conf['union_fld_postfix'];
			$postname = $this->conf['relunion_fld_postfix'];
		}
		$preidmain = "";
		if(isset($this->conf['union_fld_prefix'])){
			//$idmain = $this->conf['relunion_fld_prefix'].$idmain;
			$preidmain = $this->conf['union_fld_prefix'];
		}
		$postidmain = "";
		if(isset($this->conf['union_fld_postfix'])){
			//$idmain = $idmain.$this->conf['relunion_fld_postfix'];
			$postidmain = $this->conf['union_fld_postfix'];
		}

		$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
		//if(isset($this->conf['tabshowconds'])) $conds .= " and ".$this->conf['tabshowconds'];
		if(isset($this->conf['showfieldconcat']) && $this->conf['showfieldconcat'] == "1"){
			$sqlshowfile = $this->conf['showfield'];
		}else{
			$sqlshowfile = $this->conf['tab'].".".$this->conf['showfield'];
		}

		$sqlMas = "";
		if(isset($this->conf['lefttabs'])){
			$leftTab=explode('|',$this->conf['lefttabs']);
			$leftCond=explode('|',$this->conf['leftconds']);
			if(sizeof($leftTab)>0 && sizeof($leftCond)>0 && sizeof($leftTab)==sizeof($leftCond)){
				for($i=0;$i<sizeof($leftTab);$i++){
					if($i!=0){
						$sqlMas.=" ";
					}
					$sqlMas .= " left join ".$leftTab[$i]." on(".$leftCond[$i].") ";
				}
			}
		}

		$sql = "select
			group_concat(".$sqlshowfile." SEPARATOR '|') as res
		from ".$this->conf['reltab']."
			left join ".$this->conf['tab']."
					on (concat('".$prename."',".$theName.",'".$postname."')=".$this->conf['reltab'].".".$this->conf['relunion_fld'].")
			".$sqlMas."
		where
			".$this->conf['reltab'].".".$this->conf['union_fld']." = concat('".$preidmain."',".$idmain.",'".$postidmain."') ";
		if ($conds&&trim($conds)!="") $sql.= "
			and ".$conds;
		if(isset($this->conf['showorderby']) && !empty($this->conf['showorderby'])){
			$sql .= "
		order by ".$this->conf['tab'].".".$this->conf['showorderby'];
		}
		$a=array();
		$c= new con($sql);
		$r = $c->getResult();
		$arr = explode('|',$r['res']);

		$sql = "select ".$this->conf['showfield']." as f from ".$this->conf['tab']."  ";
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
		$aRet['autocompletetab'] = $this->conf['tab'];
		$aRet['autocompletefield'] = $this->conf['showfield'];
		if(isset($this->conf['req']) && $this->conf['req'] == "1"){
			$aRet['req']=true;
		}
		return json_encode($aRet);
	}

	public function getSQL($tab,$alias = true) {
		$idmain = $this->conf['main_fld'];


		$theName = $this->conf['tab'].".".$this->conf['fld'];
		if(isset($this->conf['relunion_fld_prefix'])){
			$theName = $this->conf['relunion_fld_prefix'].$theName;
		}
		if(isset($this->conf['relunion_fld_postfix'])){
			$theName = $theName.$this->conf['relunion_fld_postfix'];
		}

		$rpre=(isset($this->conf['relunion_fld_prefix'])?$this->conf['relunion_fld_prefix']:"");
		$rpost=(isset($this->conf['relunion_fld_postfix'])?$this->conf['relunion_fld_postfix']:"");

		$upre=(isset($this->conf['union_fld_prefix'])?$this->conf['union_fld_prefix']:"");
		$upost=(isset($this->conf['union_fld_postfix'])?$this->conf['union_fld_postfix']:"");


		$conds = isset($this->conf['tabconds'])? $this->conf['tabconds']:false;
		if(isset($this->conf['showfieldconcat']) && $this->conf['showfieldconcat'] == "1"){
			$sqlshowfile = $this->conf['showfield'];
		}else{
			$sqlshowfile = $this->conf['tab'].".".$this->conf['showfield'];
		}

		$sqlMas = "";
		if(isset($this->conf['lefttabs'])){
			$leftTab=explode('|',$this->conf['lefttabs']);
			$leftCond=explode('|',$this->conf['leftconds']);
			if(sizeof($leftTab)>0 && sizeof($leftCond)>0 && sizeof($leftTab)==sizeof($leftCond)){
				for($i=0;$i<sizeof($leftTab);$i++){
					if($i!=0){
						$sqlMas.=" ";
					}
					$sqlMas .= " left join ".$leftTab[$i]." on(".$leftCond[$i].") ";
				}
			}
		}

		$ret = "( select group_concat(".$sqlshowfile." SEPARATOR '".$this->conf['separator']."')
		from ".$this->conf['reltab']." left join ".$this->conf['tab']." on (";
		$ret .= "concat('".$rpre."',".$this->conf['tab'].".".$this->conf['fld'].",'".$rpost."')";
		$ret .= "=".$this->conf['reltab'].".".$this->conf['relunion_fld'].") ".$sqlMas." where
		".$this->conf['reltab'].".".$this->conf['union_fld']." = ";
		$ret .= "concat('".$upre."',".$tab.".".$idmain.",'".$upost."') ".(($conds&&trim($conds)!="")?  " and ".$conds :"" )." )";

		if ($alias) $ret .= " as '".$this->getAlias()."'";
		return $ret;
	}


	public function insertAfterCreateMain($idMain, $value) {
		$c = new con("select 1;");
		$aValues = explode($this->conf['separator'],$value);
		$aValues = array_unique($aValues);
		$id = $idMain;
		$sql = "select ".$this->conf['main_fld']." as IDMAIN from ".$this->conf['maintab']." where ".$this->conf['mainid']." = '".$id."'";
		$con = new con($sql);
		$mainr = $con->getResult();
		$idmain = $mainr['IDMAIN'];
		if(isset($this->conf['union_fld_prefix'])){
			$idmain = $this->conf['union_fld_prefix'].$idmain;
		}
		if(isset($this->conf['union_fld_postfix'])){
			$idmain = $idmain.$this->conf['union_fld_postfix'];
		}
		if(isset($this->conf['union_fld_prefix'])){
			$idmain = $this->conf['union_fld_prefix'].$idmain;
		}
		if(isset($this->conf['union_fld_postfix'])){
			$idmain = $idmain.$this->conf['union_fld_postfix'];
		}
		$sql = "select ".$this->conf['relunion_fld']." as IDO , ".$this->conf['relid']." as RELID from ".$this->conf['reltab']." where ".$this->conf['union_fld']." ='".$idmain."'";
		$aRes = array();
		$c = new con($sql);
		while ($r = $c->getResult()){
			$aRes[$r['IDO']]= $r['RELID'];
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
		if (sizeof($aValues)>0){
			foreach ($aValues as $v) {
				if (trim($v)==""||trim($v)=="Select options") continue;
				$v = mysql_real_escape_string(trim($v));
				$sql = "select ".$this->conf['id']." as ID,".$this->conf['fld']." as NAME from ".$this->conf['tab']." where ".$this->conf['showfield']." = '".$v."' limit 1";
				$c = new con($sql);
				if ($c->getNumRows()>0){
					$r = $c->getResult();
					if(isset($aRes[$r['NAME']])){
						unset($aRes[$r['NAME']]);
						continue;
					}else{
						$acampos = array();
						$avalores = array();
						$acampos[] = $this->conf['union_fld'];
						$avalores[] = "'".$idmain."'";
						$acampos[] = $this->conf['relunion_fld'];
						$theName = $r['NAME'];
						if(isset($this->conf['relunion_fld_prefix'])){
							$theName = $this->conf['relunion_fld_prefix'].$theName;
						}
						if(isset($this->conf['relunion_fld_postfix'])){
							$theName = $theName.$this->conf['relunion_fld_postfix'];
						}
						$avalores[] = "'".$theName."'";
						if (isset($this->conf['reltabcond'])){
							list($acampos[],$avalores[]) = explode('=',$this->conf['reltabcond'] ) ;
						}
						$sql2 = "insert into ".$this->conf['reltab']." (".implode(',',$acampos).") values (".implode(',',$avalores).") ";
						$c2 = new con($sql2);
					}

				}
			}

		}

		if (sizeof($aRes)>0){
			foreach ($aRes as $foo=>$v){
				$sql2 = "delete from ".$this->conf['reltab']." where  ".$this->conf['relid']." = '".$v."'";
				$c2 = new con($sql2);
			}

		}
		$accionEjec = 'insert';
		if($this->trigger !== false && isset($this->triggerParamsEsp) && !empty($this->triggerParamsEsp)){
			if(in_array('insert',$this->getTriggerOn())){
				$_datosForTrigger = array(
					"valueFLD" => $value
				);
				$cad = $this->runTriggerNew($id,$accionEjec,$_datosForTrigger);
				if(is_array($cad)){
                    return $cad;
                }
			}
		}else
			if ($this->getTrigger($id,$accionEjec)!==false){
				if(in_array($accionEjec,$this->triggerOn) || in_array('update',$this->triggerOn)){
					$funcionTrigger = $this->getTrigger($id,$accionEjec).';';
					eval("\$cad = $funcionTrigger");
					if(is_array($cad)){
						return $cad;
					}
				}
			}
		return 'noInsertBecauseFileEsp';
	}

	public function setEditForSearch($val)
	{
        $this->flagForSearch = $val;
	}
}


?>
