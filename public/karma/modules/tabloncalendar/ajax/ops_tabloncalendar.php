<?php
	session_name("karmaPrivate");
	session_start();
	session_cache_limiter("private");
	header("Expires: 0");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false); // HTTP/1.1
	header("Pragma: no-cache");// HTTP/1.0
	/* fin de header anti cache */

	define("CHK_KARMA",1); // Constante para comprobar que todos los ficheros son cargados desde index
	if (isset($_GET['DEBUG'])) define("DEBUG",1);

	require_once("../../../libs/autoload.php");

	// Instanciamos objeto error para crear el trigger de errores a nuestro objeto.
	$oError = new iError();

	//Cargamos constantes de ficheros requeridos
	include_once("../../../../configuracion/defs.cfg");
	switch($_POST['op']) {
		case "get_event_days":
			$thePlts = explode("|",$_POST['pl']);
			$tmp = array();
			$tmp = explode("|",$_POST['dPrinc']);
			$aa = array();
			$theFlds = array();
			foreach($tmp as $idx=>$vlr){
				$aa = explode("::",$vlr);
				$theFlds[] = $aa[1];
			}
			$hayError = false;
			$retorno = array();
			for($i=0;$i<sizeof($thePlts);$i++){
				$plt = $thePlts[$i];
				$plantilla = tablon_AJAXjeditable::setPlantillaPath($plt);
				$pl = new tablon_plantilla($plantilla);
				$fld = $pl->findField($theFlds[$i]);
				$objDate = $pl->fields[$fld];
				$fldDate = $objDate->getIndex();
				$deleted = "";
				if($deleted = $pl->getDeletedFLD()) $cond = " where ".$deleted." = '0'";
				$sql = "select distinct(date_format(".$fldDate.",'%Y-%m-%d')) as campoMaestro from ".$pl->getTab()." ".$cond;
				if(isset($_POST['condId']) && isset($_POST['condFld']) && !empty($_POST['condId']) && !empty($_POST['condFld'])){
					$sql .= " where ".trim($_POST['condFld'])." = '".trim($_POST['condId'])."'";
				}
				$con = new con($sql);

				if($con->error() || $hayError){
					$hayError = true;
					continue;
				}
				while($r = $con->getResult()){
					$fecha[$r['campoMaestro']] = $r['campoMaestro'];
					$ret = array();
					list($ret['year'], $ret['month'], $ret['day']) = explode("-",$fecha[$r['campoMaestro']]);
					$retorno[] = $ret;
				}
			}
			if($hayError)
				die(json_encode(array("error"=>$con->getErrorNumber(),"errorStr"=>$con->getError())));
			else
				die(json_encode(array("error"=>0,"results"=>$retorno)));
			break;
		case "ver_eventos":
			$thePlts = explode("|",$_POST['pl']);
			//$theFlds = explode("|",$_POST['dPrinc']);
			$tmp = array();
			$tmp = explode("|",$_POST['dPrinc']);
			$aa = array();
			$theFlds = array();
			foreach($tmp as $idx=>$vlr){
				$aa = explode("::",$vlr);
				$theFlds[] = $aa[1];
			}
			$aFlds = $aCabs = $retorno = array();
			for($i=0;$i<sizeof($thePlts);$i++){
				$plt = $thePlts[$i];
				$plantilla = tablon_AJAXjeditable::setPlantillaPath($plt);
				$pl = new tablon_plantilla($plantilla);
				$fldID = $pl->getID();
				$fld = $pl->findField($theFlds[$i]);
				$objDate = $pl->fields[$fld];
				$fldDate = $objDate->getIndex();
$aFldsReal = array();
				for($i=0;$i<$pl->getNumFields();$i++){
					$fld = $pl->fields[$i];
					$fldI = $pl->getTab().".".$fld->getIndex();
	if (method_exists($fld, 'getShowQuery')){				
		list($fldI, $lefts, $grpby) = $fld->getShowQuery();
		
	}
					if($fldI != $fldID && $fldI != $fldDate){
						$aObjsFlds[] = $fld;
						$aFlds[] = $fldI;
						$aFldsReal[] = $fld->getIndex();
						$aCabs[] = $pl->fields[$i]->getAlias();
					}
				}
				$deleted = $cond = "";
				if($deleted = $pl->getDeletedFLD()) $cond = "and ".$deleted;
				if (sizeof($_POST['conds']) > 0) {
					foreach($_POST['conds'] as $_miniCond) {
						if ( (isset($_miniCond['name'])) && (isset($_miniCond['value'])) ) {
							$cond .= " and ".$pl->getTab().".".$_miniCond['name']." = '".$_miniCond['value']."' ";
						}
					}

				}

				$strFlds = "";
				if(sizeof($aFlds)>0) $strFlds = ", ".implode(",",$aFlds);
				
				$sql = "select ".$pl->getTab().".".$fldID.", ".$pl->getTab().".".$fldDate." ".$strFlds." from ".$pl->getTab()." ".$pl->getTab()." ".((isset($lefts))? $lefts:"")." where ".$fldDate." = '".$_POST['id']."' ".$cond." ".((isset($grpby))? $grpby:"")." ";

				$con = new con($sql);
				if($con->error()){
					die(json_encode(array("error"=>$con->getErrorNumber(),"errorStr"=>$con->getError())));
				}
				while($r = $con->getResult()){
					$ret = array();
					$ret['id'] = $r[$fldID];
					$ret['date'] = $r[$fldDate];
					$ret['plt'] = $plt;
					for($i=0;$i<sizeof($aFlds);$i++){
						//drawTableValue($value)
						$valueFlds = $aObjsFlds[$i]->drawTableValue($r[str_replace("`","",$aFldsReal[$i])]);
						$ret[$aFldsReal[$i]] = $valueFlds;
					}
					$retorno[] = $ret;
				}
			}
			die(json_encode(array("error"=>0,"results"=>$retorno,"cabeceras"=>$aCabs,"titular"=>"Eventos para el ".$_POST['id'])));
			break;
		case 'delete_event':
			list($thePlt, $idDel) = explode("::",$_POST['id']);
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($thePlt);
			$pl = new tablon_plantilla($plantilla);

			$fldID = $pl->getID();
			$deleted = "";
			if($deleted = $pl->getDeletedFLD()){
				$sql = "update ".$pl->getTab()." set ".$pl->getDeletedFLD()." = '1' where ".$fldID." = '".$idDel."' and ".$pl->getDeletedFLD()." = '0'";
			}else{
				$sql = "delete from ".$pl->getTab()." where ".$fldID." = '".$idDel."'";
			}
			$con = new con($sql);
			if($con->error()){
				die(json_encode(array("error"=>$con->getErrorNumber(),"errorStr"=>$con->getError())));
			}else{
				die(json_encode(array("error"=>0,"results"=>"borrado")));
			}
			break;
		case 'vaciar_dia':
			/*list($thePlts, $theFlds) = explode("::",$_POST['dPrinc']);
			for($i=0;$i<sizeof($thePlts);$i++){
				$plt = $thePlts;
				$plantilla = tablon_AJAXjeditable::setPlantillaPath($plt);
				$pl = new tablon_plantilla($plantilla);
				$fld = $pl->findField($theFlds);
				$objDate = $pl->fields[$fld];
				$fldDate = $objDate->getIndex();

				$deleted = "";
				if($deleted = $pl->getDeletedFLD()){
					$sql = "update ".$pl->getTab()." set ".$pl->getDeletedFLD()." = '1' where ".$fldDate." = '".$_POST['id']."' and ".$pl->getDeletedFLD()." = '0'";
				}else{
					$sql = "delete from ".$pl->getTab()." where ".$fldDate." = '".$_POST['id']."'";
				}
				$con = new con($sql);
				if($con->error()){
					die(json_encode(array("error"=>$con->getErrorNumber(),"errorStr"=>$con->getError())));
				}
			}*/
			$plt = $_POST['pl'];
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($plt);
			$pl = new tablon_plantilla($plantilla);
			$fld = $pl->findField($_POST['dPrinc']);
			$objDate = $pl->fields[$fld];
			$fldDate = $objDate->getIndex();

			$deleted = "";
			if($deleted = $pl->getDeletedFLD()){
				$sql = "update ".$pl->getTab()." set ".$pl->getDeletedFLD()." = '1' where ".$fldDate." = '".$_POST['id']."' and ".$pl->getDeletedFLD()." = '0'";
			}else{
				$sql = "delete from ".$pl->getTab()." where ".$fldDate." = '".$_POST['id']."'";
			}
			if(isset($_POST['condId']) && isset($_POST['condFld']) && !empty($_POST['condId']) && !empty($_POST['condFld'])){
				$sql .= " and ".trim($_POST['condFld'])." = '".trim($_POST['condId'])."'";
			}
			$con = new con($sql);
			if($con->error()){
				die(json_encode(array("error"=>$con->getErrorNumber(),"errorStr"=>$con->getError())));
			}
			die(json_encode(array("error"=>0,"results"=>"borrado")));
			break;

		case 'new_event':
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($_POST['pl']);
			$fecha = $_POST['id'];
			$fldpost = $_POST['dPrinc'];
			$pl = new tablon_plantilla($plantilla);
			$fields = array();
			for ($i=0;$i<$pl->getNumFields();$i++) {
				$field = array();
				if ($pl->fields[$i]->getType()===false) continue;
				else{
					if($pl->fields[$i]->getIndex() == $fldpost){
						$field['alias'] = $pl->fields[$i]->getAlias();
						$field['name'] = $pl->fields[$i]->getSQLFLD();
						$field['ftype'] = "text";
						$field['req'] = false;
						$field['data'] = '<input type="hidden" name="'.$pl->fields[$i]->getSQLFLD().'" value="'.implode("/",explode("-",$fecha)).'" />';
						$field['noEdit'] = false;
						$field['title'] = implode("/",explode("-",$fecha));
					}else{
						$field['alias'] = $pl->fields[$i]->getAlias().(($pl->fields[$i]->iscloneInfo())? ' <small>'.$pl->fields[$i]->iscloneInfo().'</small>':'');
						$field['name'] = $pl->fields[$i]->getSQLFLD();
						$field['ftype'] = $pl->fields[$i]->getType();
						$field['req'] = $pl->fields[$i]->isRequired();
						$field['data'] = $pl->fields[$i]->drawTableValueEdit("");
						$field['noEdit'] = false;
					}
					$fields[] = $field;
				}
			}
			die(json_encode(array("error"=>0,"fields"=>$fields)));
			break;
		case 'save_new_event':
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($_POST['pl']);
			$fecha = $_POST['id'];
			$fldFecha = $_POST['dPinc'];
			$pl = new tablon_plantilla($plantilla);
			$fields = array();
			for($i=0;$i<$pl->getNumFields();$i++) {
				$fl = $pl->fields[$i];
				$constType = $fl->getConstantTypeAjaxUpload();
				$fldName = "FLD__".$fl->getSQLFLD();
				//$aValues = &$$constType;
				$aValues = $_POST;
				if($fl->getSQLFLD() == $fldFecha){
					$fields[$fl->getSQLFLD()] = $fl->getMysqlValue($aValues['id']);
					continue;
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
			}

			foreach($_POST as $idx=>$value) {
				if (preg_match("/^COND__(.*)/",$idx,$fldName)){
					$conds[$fldName[1]] = stripslashes($value);
				}
			}

			$ret = $pl->newRow($fields,$conds);
			if (!is_array($ret)) {
				die(json_encode(array("error"=>0,"resultado"=>"insertado")));
			}else{
				die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));
			}
			die();
			break;
		case 'redrawcalendar':
			echo tabloncalendar::redraw();
			die();
			break;
		case 'newonclick':
			$siDsdTablon = false;
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($_POST['plCalendar']);
			$fechaCalendar = $_POST['idCalendar'];
			$fldFechaCalendar = $_POST['dPrincCalendar'];

			$pl = new tablon_plantilla($plantilla);
			$fields = array();
			$fields[$fldFechaCalendar] = "'".$fechaCalendar."'";
			$conds = array();
			foreach($_POST as $idx=>$value) {
				if (preg_match("/^COND__(.*)/",$idx,$fldName)){
					$conds[$fldName[1]] = stripslashes($value); /*var_dump($fldName[1],$value); LANDER*/
										//var_dump(stripslashes($value));
				}
			}
			$ret = $pl->newRow($fields,$conds);

			if (!is_array($ret)) {
				die(json_encode(array("error"=>0,"fields" => true)));
			} else {
				die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));

			}
			break;
	}



?>
