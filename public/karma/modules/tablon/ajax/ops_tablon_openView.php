<?php

	if (!defined("CHK_KARMA")) die("{}");
	
			$i = explode('::',$_GET['id']);
			
			$plantilla =$i[0];
			$id =$i[1]; 
			$campos = explode(',',$_GET['campos']);
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
			$pl = new tablon_plantilla($plantilla);
			$aRes=array();
			$aRes['error'] = 0;
			$aRes['result'] = array();
			foreach ($campos as $campo){
				$idFLD = $pl->findField($campo);
				$pl->loadSingleField($idFLD,$id,true);
				$aRes['result'][] = (($r =$pl->fields[$idFLD]->drawTableValue($pl->fields[$idFLD]->getValue()))? $r:"") ;
			}
			
			
			echo json_encode($aRes);
			exit();

?>