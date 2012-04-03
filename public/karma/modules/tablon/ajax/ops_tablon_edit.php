<?php

	if (!defined("CHK_KARMA")) die("{}");

    $kRegistry = KarmaRegistry::getInstance();
    $l = new k_literal($kRegistry->get('lang')? $kRegistry->get('lang'):(isset($_SESSION['user_lang'])? $_SESSION['user_lang']:'en'));

	switch($_GET['acc']) {
		case "checkMD5":
			die(json_encode(array("ret"=>md5($_GET['value'])==$_GET['md5'])));
		break;
		case "save":
			if (isset($_GET['preview'])) {
				function abort() {
					// Nos aseguramos el Rollback
					$c = new con("rollback");
				}
				// Con Callbacks, sería sin duda muchísimo más elegante...
				register_shutdown_function('abort');
				ob_start();
				$c = new con("begin");
			}

			if (($campos = tablon_AJAXjeditable::decodeId($_GET['id']))=== false) die("{}");
			list($plantilla,$campo,$id) = $campos;
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
			$pl = new tablon_plantilla($plantilla);
			$text = $l->l("No existe el campo");
			if (($idFLD = $pl->findField($campo)) === false) die("{error:2,errorStr:'".$text."'}");
			$constType = $pl->fields[$idFLD]->getConstantTypeAjaxUpload();
			$aValues = &$$constType;
			$fldName = $pl->fields[$idFLD]->getSQLFLD(); /*LANDER*/

			/**
			 * Aquí se modifica el valor del
			 * 		$_GET['value'],
			 * 		$_POST['value'] ó
			 * 		$_FILES['value']...
			 * asi que al loro
			 */
			if (isset($aValues[$fldName])) {
				$aValues['value'] = $aValues[$fldName];
				/*
				Cuando una ñapa funciona ....
				*/
			}
			$conds = array();
			foreach($_GET as $idx=>$value) {
                if (preg_match("/^COND__(.*)/",$idx,$fldNameH)) {
					$conds[$fldNameH[1]] = $value;
				}

				if (preg_match("/^CONDT__(.*)/",$idx,$fldNameH)) {
				    $conds['triggerCond_'.$fldNameH[1]] = $value;
				}

			    $idFLDH = $pl->findField($fldNameH[1]);

				if (method_exists($pl->fields[$idFLDH],"forceUpdate")) {
					$pl->fields[$idFLDH]->forceUpdate(true);
				}
			}

			$refrescar = $pl->ifFieldRefres($idFLD,$id,'update');
			if($refrescar === false){
				$refrescar = $pl->ifRefreshwhen('update');
			}
			if(!isset($aValues['value']))
			{
			    $aValues['value'] = '';
			}
			if(count($conds) == 0){
				$value = $pl->saveSingleField($idFLD,$aValues['value'],$id);

				$refrescar = $pl->ifFieldRefres($idFLD,$id,'update');
				if($refrescar === false){
					$refrescar = $pl->ifRefreshwhen('update');
				}
				foreach($_GET as $idx=>$vlr) {
					if (preg_match("/^COND__(.*)/",$idx,$fldNameH)){
						$idFLDH = $pl->findField($fldNameH[1]);
						if (method_exists($pl->fields[$idFLDH],"forceUpdate")) {
							$pl->fields[$idFLDH]->forceUpdate(true);
						}
						$value2 = $pl->saveSingleField($idFLDH,$vlr,$id);
					}
				}
			}
			else {
				$value = $pl->saveSingleField($idFLD,$aValues['value'],$id,false,$conds);
			}

			if (isset($_GET['preview'])) {
				$params = $pl->fields[$idFLD]->getPreviewParams();
				foreach ($params as $k => $value) {

					if ( (preg_match("/^%(.*)%$/",$value,$nCamp)) && (($idF = $pl->findField($nCamp[1]))!==false) ) {

						// El campo esta entre % %, hay que recuperarlo de la base de datos
						$con = new con("select " .$pl->fields[$idF]->getSQLFLD(). " as f from ".$pl->getTab()." where ".$pl->getID()." = '".$id."'");
						if ($con->getNumRows() == 1) {
							$r = $con->getResult();
							$value = $r['f'];
						}
					}
					if (preg_match("/GET::(.*)/",$k,$res)) {
						$_GET[$res[1]] = $value;
					} else {
						$$k = $value;
					}
				}

				chdir("../../../..");
				error_reporting(0);
				$d = dirname($_SERVER['PHP_SELF'])."/../../../../";
				define("BASE_URL_PREVIEW",$d);


				require $pl->fields[$idFLD]->getURLPreview();
				exit();
			}

			$valorretorno = $value;
			if(is_array($value) && isset($value['principal']) && isset($value['subfields'])){
				$valorretorno = $value['principal'];
				$valorretornoAux = $value['subfields'];
			}

			if ((!is_array($value)) || ((isset($value[0])) && empty($value[0]))) {
			    $text = $l->l("guardado");
				if($refrescar === true){
					die(json_encode(array("error"=>0,"value"=>$valorretorno,"md5"=>md5($valorretorno),"msg"=>$text,"refreshAfter"=>true)));
				}else{
					die(json_encode(array("error"=>0,"value"=>$valorretorno,"valueAux"=>$valorretornoAux,"md5"=>md5($valorretorno),"msg"=>$text)));
				}
			} else {
				if($refrescar === true){
					die(json_encode(array("error"=>$valorretorno[0],"errorStr"=>$valorretorno[1],"refreshAfter"=>true)));
				}else{

					die(json_encode(array("error"=>$valorretorno[0],"errorStr"=>$valorretorno[1])));
				}
			}
		break;
		case "undoEdit":
			if (($campos = tablon_AJAXjeditable::decodeId($_GET['id']))=== false) die("{}");
			list($plantilla,$campo,$id) = $campos;
			$plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
			$pl = new tablon_plantilla($plantilla);
			$text = $l->l("No existe el campo");
			if (($idFLD = $pl->findField($campo)) === false) die("{error:2,errorStr:'".$text."'}");
			$value = $pl->loadSingleField($idFLD,$id);
			if ($value===true) {
			    $text = $l->l("valor restaurado.");
				die(json_encode(array("error"=>0,"value"=>$pl->fields[$idFLD]->getValue(),"md5"=>md5($pl->fields[$idFLD]->getValue()),"msg"=>$text)));
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
        case "deleteImage":

            if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'])) === false) die("{}");
            list($plantilla,$campo,$id) = $campos;
            $plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
            $pl = new tablon_plantilla($plantilla);
            $text = $l->l("No existe el campo");
            if (($idFLD = $pl->findField($campo)) === false) die("{error:2,errorStr:'".$text."'}");
            $value = $pl->saveSingleField($idFLD,'',$id);
            $text  =$l->l("dato eliminado");
            die(json_encode(array("error"=>0,"value"=>'',"md5"=>md5(''),"msg"=>$text)));
        break;


	}


?>
