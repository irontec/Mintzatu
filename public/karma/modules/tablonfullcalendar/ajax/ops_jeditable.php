<?php
	if (!defined("CHK_KARMA")) die("{}");
	switch($_GET['acc']) {
		case "load":
			if (($campos = tablon_AJAXjeditable::decodeId($_GET['id']))=== false) die("{}");	
			list($plantilla,$campo,$id) = $campos;
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
			$pl = new tablon_plantilla($plantilla);
			if (($idFLD = $pl->findField($campo)) === false) die("{}");
			if (!method_exists($pl->fields[$idFLD],"drawEditJSON")) {
				// Si jEditable pide campo, y no se tiene en JSON, devolvemos el de tablonEdit.
				// Deshabilitado de momento...				
				/*if (method_exists($pl->fields[$idFLD],"drawTableValueEdit")) {
					$pl->loadSingleField($idFLD,$id);
					echo $pl->fields[$idFLD]->drawTableValueEdit();
					exit();
				}*/
				die("{}");
			}
			$pl->loadSingleField($idFLD,$id);
			echo $pl->fields[$idFLD]->drawEditJSON();
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
					die(json_encode(array("error"=>0,"value"=>$value,"refreshAfter"=>true)));
				else
					die(json_encode(array("error"=>0,"value"=>$value)));
			} else {
				die(json_encode(array("error"=>$value[0],"errorStr"=>$value[1])));
			}
		break;
	}
?>
