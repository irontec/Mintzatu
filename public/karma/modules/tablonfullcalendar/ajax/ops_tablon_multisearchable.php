<?php
	usleep(2);
	if (!defined("CHK_KARMA")) die("{}");
	switch($_GET['acc']) {
		case "rel":

			$plantilla = tablon_AJAXjeditable::setPlantillaPath($_GET['target_plt']);
			$pl = new tablon_plantilla($plantilla);
			
			$fields = array();
			
			$constType = "MultiFLDs";			
			$MultiFLDs = array();
			$MultiFLDs[$_GET['target_selected_id']] = $_GET['target_selected_value'];
			$MultiFLDs[$_GET['target_searchable_id']] = $_GET['target_searchable_value'];
		
			for($i=0;$i<$pl->getNumFields();$i++) {
				$fl = $pl->fields[$i];
				$fldName = $fl->getSQLFLD();
				$aValues = &$$constType;
				
				$fields[$fl->getSQLFLD()] = $fl->getMysqlValue($aValues[$fldName]);
			}
			
			
			$ret = $pl->newRow($fields);
			$result = array();
			/*
			* Al loro con la vida... si algún día se pone una taget_plt con borrado lógico, quizás al añadir aqui y obtener un 1062 -duplicado- quizás lo suyo sería intentar un undelete en la plantilla no?
			* Lo dejo aquí, pero no lo implemento :) -- jabi
			*/
			if (is_array($ret)) {
				if ($ret[0] == 1062) { // Duplicate key
					$result['error'] = true;
					$result['strError'] = "duplicate";
					$result['state'] = true;
				} else {
					$result['error'] = true;
					$result['strError'] = $ret[1]." (".$ret[0].")";
					$result['state'] = false;
				}
			} else {
				$result['error'] = false;
				$result['value'] = $ret;
				$result['state'] = true;
			}
			die(json_encode($result));	
		break;
		case "unrel":
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($_GET['target_plt']);
			$pl = new tablon_plantilla($plantilla);
			$ret = $pl->doDelete(preg_replace("/[^0-9]+/","",$_GET['value']));

			if (!is_array($ret)) {
				die(json_encode(array("error"=>0)));
			} else {
				die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));
			}
		break;
	}
		

?>
