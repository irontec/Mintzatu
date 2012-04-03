<?php
usleep(2);
if (!defined("CHK_KARMA")) die("{}");
	switch($_GET['acc']) {
		case 'requireddata':
			$a=$_GET['value'];
			list($fplantilla,$fplantillamass,$massid,$relid,$condid,$extraDataplt,$noeditf)=explode('::',$a,7);

			if(empty($extraDataplt) && empty($noeditf)){
				die(json_encode(true));
			}
			if($extraDataplt == '0'){
				die(json_encode(array("error"=>0,"fields"=>false)));
			}

			$plantilla = tablon_AJAXjeditable::setPlantillaPath($fplantilla);
			$plantillamass = tablon_AJAXjeditable::setPlantillaPath($fplantillamass);
			$pl=parse_ini_file($plantilla,true);
			$plmass=parse_ini_file($plantillamass,true);

			if(!isset($pl['::main']['relidmain']) || empty($pl['::main']['relidmain']) || $pl['::main']['relidmain']===false){
				$main_id = $pl['::main']['id'];
			}else{
				$main_id = $pl['::main']['relidmain'];
			}
			if(!isset($pl['::main']['relidmass']) || empty($pl['::main']['relidmass']) || $pl['::main']['relidmass']===false){
				$mass_id = $plmass['::main']['id'];
			}else{
				$mass_id = $pl['::main']['relidmass'];
			}
			$plantillaExtra = tablon_AJAXjeditable::setPlantillaPath($extraDataplt);
			$obPl = new tablon_plantilla($plantillaExtra);

			if($noeditf!='0'){
				$noEdit = explode(',',$noeditf);
			}else{
				$noEdit = array();
			}
			$noEditFF = array();
			$fields = array();
			$contConstraint = 0;
			for($i=0;$i<$obPl->getNumFields();$i++){
			    $conf = $obPl->fields[$i]->getTagConf();
				if($obPl->fields[$i]->isRequired() || ($obPl->fields[$i]->getIndex() == $mass_id) || ($obPl->fields[$i]->getIndex() == $main_id) || isset($conf['showonnew'])){
					$field = array();
					$field['alias'] = $obPl->fields[$i]->getAlias().(($obPl->fields[$i]->iscloneInfo())? ' <small>'.$obPl->fields[$i]->iscloneInfo().'</small>':'');
					if(in_array($obPl->fields[$i]->getSQLFLD(),$noEdit)){
						$field['noEdit'] = true;
						if($obPl->fields[$i]->getSQLFLD()!=$main_id && $obPl->fields[$i]->getSQLFLD()!=$mass_id)
							$noEditFF[] = $obPl->fields[$i]->getSQLFLD();
					}else{
						$field['noEdit'] = false;
					}
					$field['name'] = $obPl->fields[$i]->getSQLFLD();
					$field['ftype'] = $obPl->fields[$i]->getType();
					$field['req'] = $obPl->fields[$i]->isRequired();
					if(method_exists($obPl->fields[$i],"getDefault")){
						$defaultVal = $obPl->fields[$i]->getDefault();
					}else{
						$defaultVal = "";
					}
					if($obPl->fields[$i]->getIndex() == $mass_id){
						$field['data'] = $obPl->fields[$i]->drawTableValueEdit($massid);
						$contConstraint++;
					}elseif($obPl->fields[$i]->getIndex() == $main_id){
						$field['data'] = $obPl->fields[$i]->drawTableValueEdit($condid);
						$contConstraint++;
					}else{
						$field['data'] = $obPl->fields[$i]->drawTableValueEdit($defaultVal);
					}
					$fields[] = $field;
				}
			}
			$noEdit_ff = implode(",",$noEditFF);
			if(empty($fields) || (sizeof($fields)==2 && $contConstraint==2)){
				die(json_encode(array("error"=>0,"fields"=>false)));
			}else{
				$idplantilla = "new::".$extraDataplt."::".$noEdit_ff;
				die(json_encode(array("error"=>0,"fields"=>$fields,"idPlt"=>$idplantilla)));
			}
			break;
		case "insert":
			$a=$_GET['value'];
			list($fplantilla,$fplantillamass,$massid,$relid,$condid,$extraDataplt,$noeditf)=explode('::',$a,7);
			/*if ((list($fplantilla,$fplantillamass,$massid,$relid,$condid,$extraDataplt,$noeditf) = tablon_AJAXjeditable::decodeId($a,7))=== false){
				die(json_encode(array("error"=>2,"errorStr"=>'Id erróneo.')));
			}*/
			$massextradata = false;
			if(!empty($extraDataplt) || !empty($noeditf)){
				$massextradata = true;
				if($extraDataplt == '0'){
					die(json_encode(array("error"=>2,"fields"=>"id erroneo")));
				}
			}

			$plantilla = tablon_AJAXjeditable::setPlantillaPath($fplantilla);
			$plantillamass = tablon_AJAXjeditable::setPlantillaPath($fplantillamass);
			$pl=parse_ini_file($plantilla,true);
			$plmass=parse_ini_file($plantillamass,true);

			if(!isset($pl['::main']['relidmain']) || empty($pl['::main']['relidmain']) || $pl['::main']['relidmain']===false){
				$main_id = $pl['::main']['id'];
			}else{
				$main_id = $pl['::main']['relidmain'];
			}
			if(!isset($pl['::main']['relidmass']) || empty($pl['::main']['relidmass']) || $pl['::main']['relidmass']===false){
				$mass_id = $plmass['::main']['id'];
			}else{
				$mass_id = $pl['::main']['relidmass'];
			}
			$sql = " insert into ".$pl['::main']['reltab']." (".$mass_id.",".$main_id.") ";
			$sql .= " values ('".$massid."','".$condid."') ";
			$c = new con($sql);
			if($massextradata===true){
				$plantillaExtra = tablon_AJAXjeditable::setPlantillaPath($extraDataplt);
				$obPl = new tablon_plantilla($plantillaExtra);
				$required = false;
				for($i=0;$i<$obPl->getNumFields();$i++){
					if($obPl->fields[$i]->isRequired() && ($obPl->fields[$i]->getIndex() != $mass_id) && ($obPl->fields[$i]->getIndex() != $main_id)){
						$required = true;
					}
				}
				$vuelta = $fplantilla."::".$fplantillamass."::".$massid."::".$c->getLastId()."::".$condid."::".$extraDataplt."::".$noeditf;
				die(json_encode(array("ret"=> $vuelta,"req"=>$required)));
			}else{
				$vuelta = $fplantilla."::".$fplantillamass."::".$massid."::".$c->getLastId()."::".$condid;
				die(json_encode(array("ret"=> $vuelta)));
			}

		break;
		case "delete":
			$a=$_GET['value'];
			list($fplantilla,$fplantillamass,$massid,$relid,$condid,$extraDataplt,$noeditf)=explode('::',$a,7);
			$massextradata = false;
			if(!empty($extraDataplt) || !empty($noeditf)){
				$massextradata = true;
				if($extraDataplt == '0'){
					die(json_encode(array("error"=>2,"fields"=>"id erroneo")));
				}
			}
			/*if ((list($fplantilla,$fplantillamass,$massid,$relid,$condid,$extraDataplt,$noeditf) = tablon_AJAXjeditable::decodeId($a,7))=== false){
				die(json_encode(array("error"=>2,"errorStr"=>'Id erróneo.')));
			}*/
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($fplantilla);
			$plantillamass = tablon_AJAXjeditable::setPlantillaPath($fplantillamass);
			$pl=parse_ini_file($plantilla,true);
			$plmass=parse_ini_file($plantillamass,true);
			$sql = " delete from ".$pl['::main']['reltab']." ";
			$sql .= " where  ".$pl['::main']['relid']." = '".$relid."' ";
			$c = new con($sql);

			if($massextradata===true){
				$vuelta = $fplantilla."::".$fplantillamass."::".$massid."::nulo::".$condid."::".$extraDataplt."::".$noeditf;
			}else{
				$vuelta = $fplantilla."::".$fplantillamass."::".$massid."::nulo::".$condid;
			}
			die(json_encode(array("ret"=> $vuelta)));

		break;
		case "save":
			if (($campos = tablon_AJAXjeditable::decodeId($_GET['id']))=== false) die("{}");
			list($plantilla,$campo,$id) = $campos;
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
			$pl = new tablon_plantilla($plantilla);
			if (($idFLD = $pl->findField($campo)) === false) die("{error:2,errorStr:'No existe el campo'}");

			$constType = &$pl->fields[$idFLD]->getConstantTypeAjaxUpload();

			$aValues = &$$constType;
			$value = $pl->saveSingleField($idFLD,$aValues['value'],$id);
			$refrescar = $pl->ifFieldRefres($idFLD,$id,'update');
			if($refrescar === false){
				$refrescar = $pl->ifRefreshwhen('update');
			}
			if ((!is_array($value)) || ((isset($value[0])) && ($value[0]==0))) {
				if($refrescar === true)
					die(json_encode(array("error"=>0,"value"=>$value,"md5"=>md5($value),"msg"=>"guardado","refreshAfter"=>true)));
				else
					die(json_encode(array("error"=>0,"value"=>$value,"md5"=>md5($value),"msg"=>"guardado")));
			} else {
				die(json_encode(array("error"=>$value[0],"errorStr"=>$value[1])));
			}
		break;
		case "undoEdit":
			if (($campos = tablon_AJAXjeditable::decodeId($_GET['id']))=== false) die("{}");
			list($plantilla,$campo,$id) = $campos;
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
			$pl = new tablon_plantilla($plantilla);
			if (($idFLD = $pl->findField($campo)) === false) die("{error:2,errorStr:'No existe el campo'}");
			$value = $pl->loadSingleField($idFLD,$id);

			if ($value===true) {
				die(json_encode(array("error"=>0,"value"=>$pl->fields[$idFLD]->getValue(),"md5"=>md5($pl->fields[$idFLD]->getValue()),"msg"=>"valor restaurado.")));
			} else {
				die(json_encode(array("error"=>$value[0],"errorStr"=>$value[1])));
			}
		break;
		case "hiddenCond":
			$ret = array("hideFields"=>array(),"showFields"=>array());

			if (($campos = tablon_AJAXjeditable::decodeId($_GET['id']))=== false) die("{}");
			list($plantilla,$campo,$id) = $campos;

			$plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
			$pl = new tablon_plantilla($plantilla);
			if (($idFLD = $pl->findField($campo)) !== false) {
				$pl->loadSingleField($idFLD,$id);
				$ret['hideFields'] = $pl->fields[$idFLD]->getHiddenFieldsCond();
			}
			die(json_encode($ret));

 		break;



	}


?>
