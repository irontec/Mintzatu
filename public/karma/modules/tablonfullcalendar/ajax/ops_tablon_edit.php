<?php
usleep(2);
	if (!defined("CHK_KARMA")) die("{}");
	switch($_GET['acc']) {
		case "checkMD5":
			die(json_encode(array("ret"=>md5($_GET['value'])==$_GET['md5'])));
		break;
		case "save":
			if (($campos = tablon_AJAXjeditable::decodeId($_GET['id']))=== false) die("{}");	
			list($plantilla,$campo,$id) = $campos;
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
			$pl = new tablon_plantilla($plantilla);
			if (($idFLD = $pl->findField($campo)) === false) die("{error:2,errorStr:'No existe el campo'}");			
			$constType = &$pl->fields[$idFLD]->getConstantTypeAjaxUpload();
			$aValues = &$$constType;
			$fldName = $pl->fields[$idFLD]->getSQLFLD(); /*LANDER*/
			if (isset($aValues[$fldName])) {
				$aValues['value'] = $aValues[$fldName]; 
				/*
	Cuando una ñapa funciona .... 

* */
			}
			$value = $pl->saveSingleField($idFLD,$aValues['value'],$id);
			$refrescar = $pl->ifFieldRefres($idFLD,$id,'update');
			if($refrescar === false){
				$refrescar = $pl->ifRefreshwhen('update');
			} 
			
			if ((!is_array($value)) || ((isset($value[0])) && ($value[0]==0))) {
				if($refrescar === true){
					die(json_encode(array("error"=>0,"value"=>$value,"md5"=>md5($value),"msg"=>"guardado","refreshAfter"=>true)));
				}else{
					die(json_encode(array("error"=>0,"value"=>$value,"md5"=>md5($value),"msg"=>"guardado")));
				}
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
