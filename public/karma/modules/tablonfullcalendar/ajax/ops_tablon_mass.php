<?php
usleep(2);
	if (!defined("CHK_KARMA")) die("{}");
	switch($_GET['acc']) {
		case "insert":
			$a=$_GET['value'];
			list($fplantilla,$fplantillamass,$massid,$relid,$condid)=explode('::',$a,5);
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

			//$sql = " insert into ".$pl['::main']['reltab']." (".$plmass['::main']['id'].",".$pl['::main']['id'].") ";
			$sql = " insert into ".$pl['::main']['reltab']." (".$mass_id.",".$main_id.") ";
			$sql .= " values ('".$massid."','".$condid."') ";
			$c = new con($sql);
			$vuelta = $fplantilla."::".$fplantillamass."::".$massid."::".$c->getLastId()."::".$condid;
			die(json_encode(array("ret"=> $vuelta)));
		break;
		case "delete":
			$a=$_GET['value'];
			list($fplantilla,$fplantillamass,$massid,$relid,$condid)=explode('::',$a,5);
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($fplantilla);
			$plantillamass = tablon_AJAXjeditable::setPlantillaPath($fplantillamass);
			$pl=parse_ini_file($plantilla,true);
			$plmass=parse_ini_file($plantillamass,true);
			$sql = " delete from ".$pl['::main']['reltab']." ";
			$sql .= " where  ".$pl['::main']['relid']." = '".$relid."' ";
			$c = new con($sql);
			$vuelta = $fplantilla."::".$fplantillamass."::".$massid."::nulo::".$condid;
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
	//					for ($i=0;$i<$this->sizeofFields;$i++) {
//			if ($this->fields[$i]->isRequired()) $contReqs++;
				$ret['hideFields'] = $pl->fields[$idFLD]->getHiddenFieldsCond();
		//		$ret['showFields'] = $pl->fields[$idFLD]->getShownFieldsCond();
			}
			die(json_encode($ret));

 		break;



	}


?>
