<?php
usleep(2);

	if (!defined("CHK_KARMA")) die("{}");
	switch($_GET['acc']) {
	    /*
	     * TODO: Modificar esto para que use el ZipArchive...
	     *       creo que está hecho en ops_tablon_propias de tablón
	     */
		case "newzip":
			$aFiles = array();
			foreach ($_FILES as $x=>$info){
				if ($info['type'] == "application/x-zip-compressed"){
					$zfile = $info['tmp_name'];
					exec('unzip '.$zfile.' -d /tmp/zipfiles');
					if ($g = opendir('/tmp/zipfiles')) {
					    while (false !== ($archivo = readdir($g))) {
					        if ($archivo!="."&&$archivo!="..") $aFiles[] = array('tmp_name'=>$archivo,'name'=>$archivo);
					    }
					    closedir($g);
					}
				}

			}
			$aRS = array();
			foreach ($aFiles as $tfile){
				$file = array();
				$file[$x] = $tfile;
				if (!is_file($tfile['tmp_name'])) continue;

				$pltEdit = false;
				if(($campos = tablon_AJAXjeditable::decodeId($_GET['id'],5))=== false){
					if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'],3))=== false){
						die("{error:2,errorStr:'Id erróneo.}");
					}
				}else{
					if($campos[2]=='show'){
						$pltEdit = true;
					}elseif(($campos = tablon_AJAXjeditable::decodeId($_GET['id'],3))=== false){
						die("{error:2,errorStr:'Id erróneo.}");
					}
				}
				$plantilla = tablon_AJAXjeditable::setPlantillaPath($campos[1]);
				$pl = new tablon_plantilla($plantilla);
				$fields = array();
				$conds = array();

				for($i=0;$i<$pl->getNumFields();$i++) {
					$fl = $pl->fields[$i];
					if ($pl->onnew) {
						if(!in_array($pl->fields[$i]->getSQLFLD(),$pl->onnew)) continue;
					}

					$constType = $fl->getConstantTypeAjaxUpload();
					$constType = ($constType == "POST")? "GET":$constType;
					$fldName = "FLD__".$fl->getSQLFLD();
					if ($constType=="_FILES"){
						$aValues = &$file;
					}else{
						$aValues = &$$constType;
					}
					if (method_exists($fl,"preInsertCheckValue")) {
						if (($ret = $fl->preInsertCheckValue($aValues[$fldName]))!==true) {
							continue;
						}
					}
					$fields[$fl->getSQLFLD()] = ($constType!="_FILES")? $fl->getMysqlValue(($aValues[$fldName])):$fl->getMysqlValue($aValues[$fldName]);
					for($j=0;$j<$fl->sizeofsubFields;$j++) {
						$ret = $pl->saveSingleField($i,$aValues[$fldName],false,$j);
						if (is_array($ret)) {
							$fields[$ret[0]] = $ret[1];
						}

					}
				}
				foreach($_GET as $idx=>$value) {
					if (preg_match("/^COND__(.*)/",$idx,$fldName)){ $conds[$fldName[1]] = $value; /*var_dump($fldName[1],$value);*/}
				}

				$ret = $pl->newRow($fields,$conds);

				$retFields = array();

				for ($i=0;$i<$pl->getNumFields();$i++) {

					if ($pl->fields[$i]->getConstantTypeAjaxUpload()=="_FILES"){
						//var_dump($retFields);
						//echo "<br />***".$pl->fields[$i]->sizeofsubFields."**";
						for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
							//echo "<br />***".$pl->fields[$i]->subFields[$j]->getRealType();
							if($pl->fields[$i]->subFields[$j]->getRealType() == "IMG_NAME") {
								//echo $pl->fields[$i]->subFields[$j]->getValue();
								$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($pl->fields[$i]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
							}
							if($pl->fields[$i]->subFields[$j]->getRealType() == "FILE_NAME") {
								//echo $pl->fields[$i]->subFields[$j]->getValue();
								$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($pl->fields[$i]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
							}
															//$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->subFields[$j]->getSQLFLD().'::'.$ret] = urldecode($pl->fields[$i]->subFields[$j]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
						}
					}else{


							$r = rawurldecode($pl->fields[$i]->drawTableValue( $aValues['FLD__'.$pl->fields[$i]->getSQLFLD()] ,$ret ,$pl ));
							//echo "\n".$r;
							//var_dump($aValues);
							//echo $r;
							//echo "<br />";
							$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = $r;
					}


					for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
						$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->subFields[$j]->getSQLFLD().'::'.$ret] = urldecode($pl->fields[$i]->subFields[$j]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
					}


				}


				$arr=array();

				foreach ($retFields as $id=>$vl) $arr[$id] = ($vl);

				$aRS[] = array("error"=>0,"idTR"=>basename($pl->getFile()).'::'.$ret,"id"=>$ret,"values"=>$arr);

			}

            i::rmfr('/tmp/zipfiles');

            die(json_encode($aRS));
			/**SUBIDOR ZIP*/
		  break;

	  case "delete":
			if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'],2))=== false) die("{}");
			list($plantilla,$id) = $campos;
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
			$pl = new tablon_plantilla($plantilla);
			$ret = $pl->doDelete($id);
			if (!is_array($ret)) {
				if($ret === "donotdelete"){
					die(json_encode(array("error"=>0,"donotdelete"=>1)));
				}if($ret === "donotdelete_inuse"){
					die(json_encode(array("error"=>0,"donotdelete"=>1,"inuse"=>1)));
				}elseif($pl->getDeletedFLD()===false){
					die(json_encode(array("error"=>0,"doundelete"=>0)));
				}else{
					die(json_encode(array("error"=>0,"doundelete"=>1)));
				}
			} else {
				die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));
			}
		break;
		case "undelete":
			if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'],2))=== false) die("{}");
			list($plantilla,$id) = $campos;
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
			$pl = new tablon_plantilla($plantilla);
			$ret = $pl->doUnDelete($id);
			if (!is_array($ret)) {
				die(json_encode(array("error"=>0)));
			} else {
				die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));
			}
		break;
		case "newfields":
			/*** Cambios para reutilizar el módulo de nuevo desde otros modulos que hereda de tablon pero que necesitan algún cambio ******/
			$siDsdTablon = false;
			if(isset($_GET['nodesdeTablon']) && !empty($_GET['nodesdeTablon'])){
				switch($_GET['nodesdeTablon']){
					case 'calendar':
							/* Módulo de calendario */
							$plantilla = tablon_AJAXjeditable::setPlantillaPath($_GET['plCalendar']);
							$fechaCalendar = $_GET['idCalendar'];
							$fldFechaCalendar = $_GET['dPrincCalendar'];
						break;
					default:
						$siDsdTablon = true;
						break;
			 	}
			}else{
				$siDsdTablon = true;
			}
			if($siDsdTablon === true){
				/* Si no se necesita nada especial, módulo tablón como siempre */
				if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'],3))=== false) die("{error:2,errorStr:'Id erróneo.}");
				$plantilla = tablon_AJAXjeditable::setPlantillaPath($campos[1]);
				if($campos[2] != '0'){
					$noEdit = explode(',',$campos[2]);
				}else{
					$noEdit = array();
				}
			}
			$pl = new tablon_plantilla($plantilla);

			$fields = array();
			/*LANDER TABLONONNEW*/
			$onnewValues=false;

			if ($pl->onnew) $onnewValues = $pl->onnew;

			if (isset($_GET['tablononnew'])){
				$tmponnewValues = explode(',',$_GET['tablononnew']);
				if (sizeof($tmponnewValues)>0) $onnewValues = $tmponnewValues;
			}

			for ($i=0;$i<$pl->getNumFields();$i++) {
				if ($onnewValues) {
					if(!in_array($pl->fields[$i]->getSQLFLD(),$onnewValues)) continue;
				}
				if ($pl->fields[$i]->isclone()) {
					$field = array();
					$field['alias'] = $pl->fields[$i]->getAlias();
					if(in_array($pl->fields[$i]->getSQLFLD(),$noEdit)){
						$field['noEdit'] = true;
					}else{
						$field['noEdit'] = false;
					}
					$field['name'] = $pl->fields[$i]->getSQLFLD()."_clone";
					$field['req'] = $pl->fields[$i]->isRequired();
					$field['clone'] = true;
					if(method_exists($pl->fields[$i],"getDefault")){
						$defaultVal = $pl->fields[$i]->getDefault();
					}else{
						$defaultVal = "";
					}
					$field['data'] = $pl->fields[$i]->drawTableValueEdit($defaultVal,'clone');
					$fields[] = $field;
				}
				if ($pl->fields[$i]->getType()===false) continue;
				$field = array();

				if(isset($fldFechaCalendar) && $pl->fields[$i]->getIndex() == $fldFechaCalendar){
					/*Campo especial para módulo calendario que marca la fecha seleccionada y que no se dibuja...*/
					$field['alias'] = $pl->fields[$i]->getAlias();
					$field['noEdit'] = false;
					$field['name'] = $pl->fields[$i]->getSQLFLD();
					$field['ftype'] = "text";
					$field['req'] = false;
					$field['data'] = '<input type="hidden" name="'.$pl->fields[$i]->getSQLFLD().'" value="'.implode("/",explode("-",$fechaCalendar)).'" />';
					$field['title'] = "Nuev".$pl->getGenero()." ".$pl->getEntidad()." para el ".implode("/",explode("-",$fechaCalendar));
					$fields[] = $field;
					continue;
				}
				$field['alias'] = $pl->fields[$i]->getAlias().(($pl->fields[$i]->iscloneInfo())? ' <small>'.$pl->fields[$i]->iscloneInfo().'</small>':'');
				if(in_array($pl->fields[$i]->getSQLFLD(),$noEdit)){
					$field['noEdit'] = true;
				}else{
					$field['noEdit'] = false;
				}
				$field['name'] = $pl->fields[$i]->getSQLFLD();
				$field['ftype'] = $pl->fields[$i]->getType();
				$field['req'] = $pl->fields[$i]->isRequired();
				if(method_exists($pl->fields[$i],"getDefault")){
					$defaultVal = $pl->fields[$i]->getDefault();
				}else{
					$defaultVal = "";
				}
				$field['data'] = $pl->fields[$i]->drawTableValueEdit($defaultVal);
				$fields[] = $field;
			}
			die(json_encode(array("error"=>0,"fields"=>$fields)));
		break;
		case "new":
			/*** Cambios para reutilizar el módulo de nuevo desde otros modulos que hereda de tablon pero que necesitan algún cambio ******/
			$siDsdTablon = false;
			if(isset($_GET['nodesdeTablon']) && !empty($_GET['nodesdeTablon'])){
				switch($_GET['nodesdeTablon']){
					case 'calendar':
						/* Módulo de calendario */
							$plantilla = tablon_AJAXjeditable::setPlantillaPath($_GET['plCalendar']);
							$fechaCalendar = $_GET['idCalendar'];
							$fldFechaCalendar = $_GET['dPrincCalendar'];
						break;
					default:
						$siDsdTablon = true;
						break;
			 	}
			}else{
				$siDsdTablon = true;
			}
			if($siDsdTablon === true){
				/* Si no se necesita nada especial, módulo tablón como siempre */
				$pltEdit = false;
				if(($campos = tablon_AJAXjeditable::decodeId($_GET['id'],5))=== false){
					if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'],3))=== false){
						die("{error:2,errorStr:'Id erróneo.}");
					}
				}else{
					if($campos[2]=='show'){
						$pltEdit = true;
					}elseif(($campos = tablon_AJAXjeditable::decodeId($_GET['id'],3))=== false){
						die("{error:2,errorStr:'Id erróneo.}");
					}
				}
				$plantilla = tablon_AJAXjeditable::setPlantillaPath($campos[1]);
			}
			$pl = new tablon_plantilla($plantilla);
			$fields = array();
			$conds = array();
				/*LANDER TABLONONNEW*/
			$onnewValues=false;

			if ($pl->onnew) $onnewValues = $pl->onnew;

			if (isset($_GET['tablononnew'])){
				$tmponnewValues = explode(',',$_GET['tablononnew']);
				if (sizeof($tmponnewValues)>0) $onnewValues = $tmponnewValues;
			}


			for($i=0;$i<$pl->getNumFields();$i++) {
				$fl = $pl->fields[$i];
				if ($onnewValues) {
					if(!in_array($pl->fields[$i]->getSQLFLD(),$onnewValues)) continue;
				}
				$constType = $fl->getConstantTypeAjaxUpload();
                                $constType = ($constType == "_POST")? "_GET":$constType;

				$fldName = "FLD__".$fl->getSQLFLD();

				$aValues = &$$constType;

				if(isset($fldFechaCalendar) && $fldFechaCalendar == $fl->getSQLFLD()){
					/*Campo especial para módulo calendario que marca la fecha seleccionada y que no se devuelve como los demás...*/
					$fields[$fl->getSQLFLD()] = $fl->getMysqlValue($aValues['idCalendar']);
					continue;
				}

				if($pl->fields[$i]->isRequired() && (!isset($aValues[$fldName]) || empty($aValues[$fldName]))){
					if(isset($aValues["NoReqDepend__".$fl->getSQLFLD()]) && $aValues["NoReqDepend__".$fl->getSQLFLD()] == "seguir"){
						// Si es un campo dependiente de otro, y en este caso aunque el plt marque como requerido, no es necesaria.
						continue;
					}else
						die(json_encode(array("error"=>"Req","errorStr"=>"Valor requerido para ".$constType.$fl->getSQLFLD())));
				}
				if (method_exists($fl,"preInsertCheckValue")) {
					if (($ret = $fl->preInsertCheckValue($aValues[$fldName]))!==true) {
						if ((!is_array($ret)) || ((isset($ret[0])) && ($ret[0]==0))) {
							continue;
						} else {
							die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));
						}
					}
				}

				$fields[$fl->getSQLFLD()] = ($constType!="_FILES")? $fl->getMysqlValue(($aValues[$fldName])):$fl->getMysqlValue($aValues[$fldName]);
				if($fields[$fl->getSQLFLD()] == "noInsertBecauseFileEsp"){
					$fields[$fl->getSQLFLD()] = $aValues[$fldName];
				}
				for($j=0;$j<$fl->sizeofsubFields;$j++) {
					$ret = $pl->saveSingleField($i,$aValues[$fldName],false,$j);
					if (is_array($ret)) {
						$fields[$ret[0]] = $ret[1];
					}

				}
			}
			foreach($_GET as $idx=>$value) {
				if (preg_match("/^COND__(.*)/",$idx,$fldName)){
					$conds[$fldName[1]] = stripslashes($value); /*var_dump($fldName[1],$value); LANDER*/
										//var_dump(stripslashes($value));
				}
			}

			$ret = $pl->newRow($fields,$conds);

			if (!is_array($ret)) {
				if($pltEdit){
					$retFields = array();
					$plantillaS = tablon_AJAXjeditable::setPlantillaPath($campos[3]);
					$plShow = new tablon_plantilla($plantillaS);
					for ($i=0;$i<$plShow->getNumFields();$i++) {
						$sql = $plShow->loadSingleField($i,$ret);
						$retFields[basename($plShow->getFile()).'::'.$plShow->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($plShow->fields[$i]->drawTableValue($plShow->fields[$i]->getValue()));
						for ($j=0;$j<$plShow->fields[$i]->sizeofsubFields;$j++) {
							$retFields[basename($plShow->getFile()).'::'.$plShow->fields[$i]->subFields[$j]->getSQLFLD().'::'.$ret] = urldecode($plShow->fields[$i]->subFields[$j]->drawTableValue($plShow->fields[$i]->subFields[$j]->getValue()));
						}
					}
					$arr=array();
					foreach ($retFields as $id=>$vl){
						if(!i::detectUTF8($vl))
							$vl = utf8_encode($vl);
						$arr[$id] = rawurldecode($vl);
					}
					if(is_array($aRefresh) && !empty($aRefresh) && in_array('insert',$aRefresh)){
						die(json_encode(array("error"=>0,"idTR"=>basename($plShow->getFile()).'::'.$ret,"id"=>$ret,"values"=>$arr,"refreshAfter"=>true)));
					}
	 				die(json_encode(array("error"=>0,"idTR"=>basename($plShow->getFile()).'::'.$ret,"id"=>$ret,"values"=>$arr)));
				}else{
					$retFields = array();
					$refrescar = $refrescarE = false;
					$aRefresh = $pl->getRefreshwhen();
					for ($i=0;$i<$pl->getNumFields();$i++) {
						if ($pl->fields[$i]->getConstantTypeAjaxUpload()=="_FILES"){
							for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
								if($pl->fields[$i]->subFields[$j]->getRealType() == "IMG_NAME") {
									$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($pl->fields[$i]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
								}
								if($pl->fields[$i]->subFields[$j]->getRealType() == "FILE_NAME") {
									$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($pl->fields[$i]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
								}
							}
						}else{$constType = &$pl->fields[$i]->getConstantTypeAjaxUpload();
							$aValues = &$$constType;
							$r = rawurldecode($pl->fields[$i]->drawTableValue( $aValues['FLD__'.$pl->fields[$i]->getSQLFLD()] ,$ret ,$pl ));
							$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = $r;

						}for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
							$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->subFields[$j]->getSQLFLD().'::'.$ret] = urldecode($pl->fields[$i]->subFields[$j]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
						}
						if($pl->ifFieldRefres($i,$id,'insert') === true){
							if($pl->ifRefreshwhen('insert') === true){
								$refrescar = true;
							}
						}
						if($pl->ifFieldRefres($i,$id,'error') === true){
							if($pl->ifRefreshwhen('error') === true){
								$refrescarE = true;
							}
						}
					}
					$arr=array();
					foreach ($retFields as $id=>$vl){
						if(!i::detectUTF8($vl))
							$vl = utf8_encode($vl);
						$arr[$id] = ($vl);
					}

					if(is_array($aRefresh) && !empty($aRefresh) && in_array('insert',$aRefresh)){
						$refrescar = true;
					}
					if($refrescar === true)
						die(json_encode(array("error"=>0,"idTR"=>basename($pl->getFile()).'::'.$ret,"id"=>$ret,"values"=>$arr,"refreshAfter"=>true)));
					else
						die(json_encode(array("error"=>0,"idTR"=>basename($pl->getFile()).'::'.$ret,"id"=>$ret,"values"=>$arr)));
				}
			} else {
				$aRefresh = $pl->getRefreshwhen();
				if(is_array($aRefresh) && !empty($aRefresh) && in_array('error',$aRefresh))
					die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1],"refreshAfter"=>true)));
				else
					die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));

			}
		break;

	}











?>
