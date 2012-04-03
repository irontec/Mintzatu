<?php
usleep(2);
if (!defined("CHK_KARMA")) die("{}");

switch($_GET['acc']) {
    case "newzip":
        $tmpDir = sys_get_temp_dir() . '/zipfiles';
        mkdir($tmpDir);

        $aFiles = array();
        foreach ($_FILES as $x => $info) {
            switch (i::mime_content_type($info['tmp_name'])) {
                case 'application/x-zip-compressed':
                case 'application/download':
                case 'application/zip':
                    $aFiles += i::unzip($info, $tmpDir);
                    break;
                case 'application/x-rar-compressed':
                case 'application/x-rar':
                case 'application/rar':
                    $aFiles += i::unrar($info, $tmpDir);
                    break;
                case 'application/x-gzip-compressed':
                case 'application/x-gzip':
                    $aFiles += i::untargz($info, $tmpDir);
                    break;
            }
        }

        $pltEdit = false;
        if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'], 5)) === false) {
            if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'], 3)) === false) {
                die("{error:2,errorStr:'Id erróneo.}");
            }
        } else {
            if ($campos[2]!='show') {
                die("{error:2,errorStr:'Id erróneo.}");
            }
        }
        $plantilla = tablon_AJAXjeditable::setPlantillaPath($campos[1]);
        $pl = new tablon_plantilla($plantilla);

        $aRS = array();
        foreach ($aFiles as $tfile) {
            $file = array();
            $file[$x] = $tfile;

            if (!is_file($tfile['tmp_name'])) continue;

            $fields = array();
            $conds = array();

            for ($i=0; $i<$pl->getNumFields(); $i++) {
                $currentField = $pl->fields[$i];

                //Si no hay onnew y el campo no está en el array, pasamos al siguiente
                if ($pl->onnew) {
                    if (!in_array($currentField->getSQLFLD(), $pl->onnew)) continue;
                }

                $constType = $currentField->getConstantTypeAjaxUpload();
                $constType = ($constType == "POST")? "GET" : $constType;

                $currentFieldName = "FLD__" . $currentField->getSQLFLD();

                if ($constType == "_FILES") {
                    $aValues = $file;
                    // Si es un campo de tipo tablon_FLDfilefs necesitamos engañarle para que crea que está recibiendo los datos en el supeglobal $_FILES
                    if (is_a($currentField, tablon_FLDfilefs)) {
                        $_FILES = $file;
                    }
                } else {
                    $aValues = &$$constType;
                }

                if ($aValues[$currentFieldName] === "undefined") {
                    $aValues[$currentFieldName] = "";
                }

                if (method_exists($currentField,"preInsertCheckValue")) {
                    if (($ret = $currentField->preInsertCheckValue($aValues[$currentFieldName])) !== true) {
                        continue;
                    }
                }

                $fields[$currentField->getSQLFLD()] = $currentField->getMysqlValue($aValues[$currentFieldName]);

                for ($j=0; $j < $currentField->sizeofsubFields; $j++) {
                    $ret = $pl->saveSingleField($i, $aValues[$currentFieldName], false, $j);
                    if (is_array($ret)) {
                        $fields[$ret[0]] = $ret[1];
                    }
                }
            }

            foreach ($_GET as $idx=>$value) {
                if (preg_match("/^COND__(.*)/", $idx, $fldNameH)) {
                    $conds[$fldNameH[1]] = $value;
                }
                if (preg_match("/^CONDT__(.*)/", $idx, $fldNameH)) {
                    $conds['triggerCond_'.$fldNameH[1]] = $value;
                }
            }

            $ret = $pl->newRow($fields, $conds);

            $retFields = array();
            for ($i=0;$i<$pl->getNumFields();$i++) {
                $currentField = $pl->fields[$i];

                if ($currentField->getConstantTypeAjaxUpload()=="_FILES") {
                    for ($j=0; $j < $currentField->sizeofsubFields; $j++) {
                        if ($currentField->subFields[$j]->getRealType() == "IMG_NAME" || $currentField->subFields[$j]->getRealType() == "FILE_NAME") {
                            $retFields[$pl->getBaseFile().'::'.$currentField->getSQLFLD().'::'.$ret] = rawurlencode($currentField->drawTableValue($currentField->subFields[$j]->getValue()));
                        }
                    }
                } else {
                    $r = rawurldecode(
                        $currentField->drawTableValue(
                            $aValues['FLD__' . $currentField->getSQLFLD()],
                            $ret,
                            $pl
                        )
                    );
                    $retFields[$pl->getBaseFile().'::'.$currentField->getSQLFLD().'::'.$ret] = $r;
                }

                for ($j=0; $j < $currentField->sizeofsubFields; $j++) {
                    $retFields[$pl->getBaseFile().'::'.$currentField->subFields[$j]->getSQLFLD().'::'.$ret] = urldecode($currentField->subFields[$j]->drawTableValue($currentField->subFields[$j]->getValue()));
                }
            }
            $aRS[] = array(
                "error" => 0,
                "idTR" => $pl->getBaseFile() . '::' . $ret,
                "id" => $ret,
                "values" => $retFields,
                "defaultFLD" => $pl->getBaseFile() . '::' . $pl->getDefaultFLD() . "::" . $ret
            );
        }
        i::rmrf($tmpDir);
        die(json_encode($aRS));
        /** Fin newzip **/
    break;
    case "delete":
        if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'],2))=== false) die("{}");
        list($plantilla, $id) = $campos;
        $plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
        $pl = new tablon_plantilla($plantilla);
        $conds = array();
        foreach ($_GET as $idx=>$value) {
            if (preg_match("/^COND__(.*)/", $idx, $fldNameH)) {
                $conds[$fldNameH[1]] = stripslashes($value);
            }
            if (preg_match("/^CONDT__(.*)/", $idx, $fldNameH)) {
                $conds['triggerCond_'.$fldNameH[1]] = stripslashes($value);
            }
        }
        $ret = $pl->doDelete($id,1, $conds);
        if (!is_array($ret)) {
            if ($ret === "donotdelete") {
                die(json_encode(array("error"=>0,"donotdelete"=>1)));
            }if ($ret === "donotdelete_inuse") {
                die(json_encode(array("error"=>0,"donotdelete"=>1,"inuse"=>1)));
            }elseif ($pl->getDeletedFLD()===false) {
                die(json_encode(array("error"=>0,"doundelete"=>0)));
            } else {
                die(json_encode(array("error"=>0,"doundelete"=>1)));
            }
        } else {
            die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));
        }
    break;
    case "undelete":
        if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'],2))=== false) die("{}");
        list($plantilla, $id) = $campos;
        $plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
        $pl = new tablon_plantilla($plantilla);
        $conds = array();
        foreach ($_GET as $idx=>$value) {
            if (preg_match("/^COND__(.*)/", $idx, $fldNameH)) {
                $conds[$fldNameH[1]] = stripslashes($value);
            }
            if (preg_match("/^CONDT__(.*)/", $idx, $fldNameH)) {
                $conds['triggerCond_'.$fldNameH[1]] = stripslashes($value);
            }
        }
        $ret = $pl->doUnDelete($id, $conds);
        if (!is_array($ret)) {
            die(json_encode(array("error"=>0)));
        } else {
            die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));
        }
        break;
    case "newfields":
        /*** Cambios para reutilizar el módulo de nuevo desde otros modulos que hereda de tablon pero que necesitan algún cambio ******/
        $siDsdTablon = false;
        if (isset($_GET['nodesdeTablon']) && !empty($_GET['nodesdeTablon'])) {
            switch($_GET['nodesdeTablon']) {
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
        } else {
            $siDsdTablon = true;
        }
        if ($siDsdTablon === true) {
            /* Si no se necesita nada especial, módulo tablón como siempre */
            if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'],3))=== false) die("{error:2,errorStr:'Id erróneo.}");
            $plantilla = tablon_AJAXjeditable::setPlantillaPath($campos[1]);
            if ($campos[2] != '0') {
                $noEdit = explode(',', $campos[2]);
            } else {
                $noEdit = array();
            }
        }

        $pl = new tablon_plantilla($plantilla);
        $oFields = array();

        if ($tmpopl = $pl->getOplt()) {

                    unset($pl);
                    $opl = tablon_AJAXjeditable::setPlantillaPath($tmpopl);
                    $pl = new tablon_plantilla($opl);
                    $fields = array();
                    /*LANDER TABLONONNEW*/
                    $onnewValues=false;

                    if ($pl->onnew) $onnewValues = $pl->onnew;



                    if (isset($_GET['tablononnew'])) {
                        $tmponnewValues = explode(',', $_GET['tablononnew']);
                        if (sizeof($tmponnewValues)>0) $onnewValues = $tmponnewValues;
                    }

                    for ($i=0;$i<$pl->getNumFields();$i++) {
                        if ($onnewValues) {
                            if (!in_array($pl->fields[$i]->getSQLFLD(), $onnewValues)) continue;
                        }
                        if ($pl->fields[$i]->isclone()) {
                            $field = array();
                            $field['alias'] = $pl->fields[$i]->getAlias();
                            if (in_array($pl->fields[$i]->getSQLFLD(), $noEdit)) {
                                $field['noEdit'] = true;
                            } else {
                                $field['noEdit'] = false;
                            }
                            $field['name'] = $pl->fields[$i]->getSQLFLD()."_clone";

                            $field['fplt'] = $pl->fields[$i]->getfPlt();

                            $field['req'] = $pl->fields[$i]->isRequired();
                            $field['clone'] = true;
                            if (method_exists($pl->fields[$i], "getDefault")) {
                                $defaultVal = $pl->fields[$i]->getDefault();
                            } else {
                                $defaultVal = "";
                            }
                            $field['data'] = $pl->fields[$i]->drawTableValueEdit($defaultVal, 'clone');
                            $fields[] = $field;
                        }
                        if ($pl->fields[$i]->getType()===false) continue;
                        $field = array();

                        if (isset($fldFechaCalendar) && $pl->fields[$i]->getIndex() == $fldFechaCalendar) {
                            /*Campo especial para módulo calendario que marca la fecha seleccionada y que no se dibuja...*/
                            $field['alias'] = $pl->fields[$i]->getAlias();
                            $field['noEdit'] = false;
                            $field['name'] = $pl->fields[$i]->getSQLFLD();

                            $field['fplt'] = $pl->fields[$i]->getfPlt();

                            $field['ftype'] = "text";
                            $field['req'] = false;
                            $field['data'] = '<input type="hidden" name="'.$pl->fields[$i]->getSQLFLD().'" value="'.implode("/",explode("-", $fechaCalendar)).'" />';
                            $field['title'] = "Nuev".$pl->getGenero()." ".$pl->getEntidad()." para el ".implode("/",explode("-", $fechaCalendar));
                            $fields[] = $field;
                            continue;
                        }
                        $field['alias'] = $pl->fields[$i]->getAlias().(($pl->fields[$i]->iscloneInfo())? ' <small>'.$pl->fields[$i]->iscloneInfo().'</small>':'');
                        if (in_array($pl->fields[$i]->getSQLFLD(), $noEdit)) {
                            $field['noEdit'] = true;
                        } else {
                            $field['noEdit'] = false;
                        }
                        $field['name'] = $pl->fields[$i]->getSQLFLD();

                        $field['fplt'] = $pl->fields[$i]->getfPlt();

                        $field['ftype'] = $pl->fields[$i]->getType();
                        $field['req'] = $pl->fields[$i]->isRequired();

                        if (method_exists($pl->fields[$i], "getDefault")) {
                            $defaultVal = $pl->fields[$i]->getDefault();
                        } else {
                            $defaultVal = "";
                        }
                        $field['data'] = $pl->fields[$i]->drawTableValueEdit($defaultVal);
                        $fields[] = $field;
                    }

            $oFields = $fields;
            //var_dump($oFields); exit();
        }

        $pl = new tablon_plantilla($plantilla);

        $fields = array();
        /*LANDER TABLONONNEW*/
        $onnewValues=false;

        if ($pl->onnew) $onnewValues = $pl->onnew;

        if (isset($_GET['tablononnew'])) {
            $tmponnewValues = explode(',', $_GET['tablononnew']);
            if (sizeof($tmponnewValues)>0) $onnewValues = $tmponnewValues;
        }

        for ($i=0;$i<$pl->getNumFields();$i++) {
            if ($onnewValues) {
                if (!in_array($pl->fields[$i]->getSQLFLD(), $onnewValues)) continue;
            }
            if ($pl->fields[$i]->isclone()) {
                $field = array();
                $field['alias'] = $pl->fields[$i]->getAlias();
                if (in_array($pl->fields[$i]->getSQLFLD(), $noEdit)) {
                    $field['noEdit'] = true;
                } else {
                    $field['noEdit'] = false;
                }
                $field['name'] = $pl->fields[$i]->getSQLFLD()."_clone";

                $field['fplt'] = $pl->fields[$i]->getfPlt();

                $field['req'] = $pl->fields[$i]->isRequired();
                $field['clone'] = true;
                if (method_exists($pl->fields[$i],"getDefault")) {
                    $defaultVal = $pl->fields[$i]->getDefault();
                } else {
                    $defaultVal = "";
                }
                $field['data'] = $pl->fields[$i]->drawTableValueEdit($defaultVal,'clone');
                $fields[] = $field;
            }
            if ($pl->fields[$i]->getType()===false) continue;
            $field = array();

            if (isset($fldFechaCalendar) && $pl->fields[$i]->getIndex() == $fldFechaCalendar) {
                /*Campo especial para módulo calendario que marca la fecha seleccionada y que no se dibuja...*/
                $field['alias'] = $pl->fields[$i]->getAlias();
                $field['noEdit'] = false;
                $field['name'] = $pl->fields[$i]->getSQLFLD();

                $field['fplt'] = $pl->fields[$i]->getfPlt();

                $field['ftype'] = "text";
                $field['req'] = false;
                $field['data'] = '<input type="hidden" name="'.$pl->fields[$i]->getSQLFLD().'" value="'.implode("/",explode("-", $fechaCalendar)).'" />';
                $field['title'] = "Nuev".$pl->getGenero()." ".$pl->getEntidad()." para el ".implode("/",explode("-", $fechaCalendar));
                $fields[] = $field;
                continue;
            }
            $field['alias'] = $pl->fields[$i]->getAlias().(($pl->fields[$i]->iscloneInfo())? ' <small>'.$pl->fields[$i]->iscloneInfo().'</small>':'');
            if (in_array($pl->fields[$i]->getSQLFLD(), $noEdit)) {
                $field['noEdit'] = true;
            } else {
                $field['noEdit'] = false;
            }
            $field['name'] = $pl->fields[$i]->getSQLFLD();

            $field['fplt'] = $pl->fields[$i]->getfPlt();

            $field['ftype'] = $pl->fields[$i]->getType();
            $field['req'] = $pl->fields[$i]->isRequired();
            if (method_exists($pl->fields[$i],"getDefault")) {
                $defaultVal = $pl->fields[$i]->getDefault();
            } else {
                $defaultVal = "";
            }
            $field['data'] = $pl->fields[$i]->drawTableValueEdit($defaultVal);
            $field['textoAyuda'] = $pl->fields[$i]->getDescriptionTextForField();
            $fields[] = $field;
        }
        die(json_encode(array("error"=>0,"fields"=>$fields,"ofields"=>$oFields,'opl'=>$tmpopl)));
        break;

    case "new":
        /*** Cambios para reutilizar el módulo de nuevo desde otros modulos que hereda de tablon pero que necesitan algún cambio ******/
        $siDsdTablon = false;
        if (isset($_GET['nodesdeTablon']) && !empty($_GET['nodesdeTablon'])) {
            switch($_GET['nodesdeTablon']) {
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
        } else {
            $siDsdTablon = true;
        }
        if ($siDsdTablon === true) {
            /* Si no se necesita nada especial, módulo tablón como siempre */
            $pltEdit = false;
            if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'], 5)) === false) {
                if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'], 3)) === false) {
                    die("{error:2,errorStr:'Id erróneo.}");
                }
            } else {
                if ($campos[2]=='show') {
                    $pltEdit = true;
                } else if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'], 3)) === false) {
                    die("{error:2,errorStr:'Id erróneo.}");
                }
            }
            $plantilla = tablon_AJAXjeditable::setPlantillaPath($campos[1]);
            $noEdit = explode(',', $campos[2]);
        }
        $pl = new tablon_plantilla($plantilla);
        $fields = array();
        $conds = array();

        /*LANDER TABLONONNEW*/
        $onnewValues=false;

        if ($pl->onnew) $onnewValues = $pl->onnew;

        if (isset($_GET['tablononnew'])) {
            $tmponnewValues = explode(',', $_GET['tablononnew']);
            if (sizeof($tmponnewValues)>0) $onnewValues = $tmponnewValues;
        }

        for ($i=0;$i<$pl->getNumFields();$i++) {
            if (in_array($pl->fields[$i]->getSQLFLD(), $noEdit)) {
                continue;
            }

            $fl = $pl->fields[$i];
            if ($onnewValues) {
                if (!in_array($pl->fields[$i]->getSQLFLD(), $onnewValues)) continue;
            }

            $constType = $fl->getConstantTypeAjaxUpload();
            $constType = ($constType == "_POST")? "_GET":$constType;
            $aValues = &$$constType;

            $fldName = "FLD__".$fl->getSQLFLD();
            if (isset($fldFechaCalendar) && $fldFechaCalendar == $fl->getSQLFLD()) {
                /*Campo especial para módulo calendario que marca la fecha seleccionada y que no se devuelve como los demás...*/
                $fields[$fl->getSQLFLD()] = $fl->getMysqlValue($aValues['idCalendar']);
                continue;
            }

            if ($pl->fields[$i]->isRequired() && (!isset($aValues[$fldName]) || is_null($aValues[$fldName]) || $aValues[$fldName] == "")) {
                if (isset($aValues["NoReqDepend__".$fl->getSQLFLD()]) && $aValues["NoReqDepend__".$fl->getSQLFLD()] == "seguir") {
                    // Si es un campo dependiente de otro, y en este caso aunque el plt marque como requerido, no es necesaria.
                    continue;
                }else
                    die(json_encode(array("error"=>"Req","errorStr"=>"Valor requerido para ".$constType.$fl->getSQLFLD())));
            }

            if ((!isset($aValues[$fldName]) || $aValues[$fldName] === '')) {
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
            $fields[$fl->getSQLFLD()] = $fl->getMysqlValue($aValues[$fldName]);
            if ($fields[$fl->getSQLFLD()] == "noInsertBecauseFileEsp") {
                $fields[$fl->getSQLFLD()] = $aValues[$fldName];
            }

            for ($j=0;$j<$fl->sizeofsubFields;$j++) {
                $ret = $pl->saveSingleField($i, $aValues[$fldName], false, $j);
                if (is_array($ret)) {
                    $fields[$ret[0]] = $ret[1];
                }

            }
        }


        foreach ($_GET as $idx=>$value) {
            if (preg_match("/^COND__(.*)/", $idx, $fldNameH)) {
                if ($pl->isFixSlashes() && $pl->isFixSlashes() == $fldNameH[1]) {
                    $conds[$fldNameH[1]] = "'".stripslashes($value)."'";
                } else {
                    $conds[$fldNameH[1]] = "".stripslashes($value).""; /*var_dump($fldName[1], $value); LANDER*/
                }                //var_dump(stripslashes($value));
            }
            if (preg_match("/^CONDT__(.*)/", $idx, $fldNameH)) {
                $conds['triggerCond_'.$fldNameH[1]] = stripslashes($value);
            }
        }


        $ret = $pl->newRow($fields, $conds);
        if (!is_array($ret)) {
            if ($pltEdit) {
                $retFields = array();
                $plantillaS = tablon_AJAXjeditable::setPlantillaPath($campos[3]);
                $plShow = new tablon_plantilla($plantillaS);
                for ($i=0;$i<$plShow->getNumFields();$i++) {
                    $sql = $plShow->loadSingleField($i, $ret);
                    $retFields[basename($plShow->getFile()).'::'.$plShow->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($plShow->fields[$i]->drawTableValue($plShow->fields[$i]->getValue()));
                    for ($j=0;$j<$plShow->fields[$i]->sizeofsubFields;$j++) {
                        $retFields[basename($plShow->getFile()).'::'.$plShow->fields[$i]->subFields[$j]->getSQLFLD().'::'.$ret] = urldecode($plShow->fields[$i]->subFields[$j]->drawTableValue($plShow->fields[$i]->subFields[$j]->getValue()));
                    }
                }
                $arr=array();
                foreach ($retFields as $id=>$vl) {
                    if (!i::detectUTF8($vl))
                        $vl = utf8_encode($vl);
                    $arr[$id] = rawurldecode($vl);
                }
                if (is_array($aRefresh) && !empty($aRefresh) && in_array('insert', $aRefresh)) {
                    die(json_encode(array("error"=>0,"idTR"=>basename($plShow->getFile()).'::'.$ret,"id"=>$ret,"values"=>$arr,"refreshAfter"=>true,"defaultFLD"=>basename($pl->getFile()).'::'.$pl->getDefaultFLD()."::".$ret)));
                }
                 die(json_encode(array("error"=>0,"idTR"=>basename($plShow->getFile()).'::'.$ret,"id"=>$ret,"values"=>$arr,"defaultFLD"=>basename($pl->getFile()).'::'.$pl->getDefaultFLD()."::".$ret)));
            } else {
                $retFields = array();
                $refrescar = $refrescarE = false;
                $aRefresh = $pl->getRefreshwhen();
                $aNotify = $pl->getNotifyWhenInsert();
                $notifyStr = ($aNotify == false)? false:(($pl->getNotifyWhenInsertStr())? $pl->getNotifyWhenInsertStr():'');

                for ($i=0;$i<$pl->getNumFields();$i++) {
                    if ($pl->fields[$i]->getConstantTypeAjaxUpload()=="_FILES") {
                        for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
                            if ($pl->fields[$i]->subFields[$j]->getRealType() == "IMG_NAME") {
                                $retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($pl->fields[$i]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
                            }
                            if ($pl->fields[$i]->subFields[$j]->getRealType() == "FILE_NAME") {
                                $retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($pl->fields[$i]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
                            }
                        }
                    } else {
                        $constType = &$pl->fields[$i]->getConstantTypeAjaxUpload();
                        $aValues = &$$constType;

                        $vlr_para_drawTabValue = (!isset($aValues['FLD__'.$pl->fields[$i]->getSQLFLD()]))? NULL:$aValues['FLD__'.$pl->fields[$i]->getSQLFLD()];
                        if ($vlr_para_drawTabValue == null && $pl->fields[$i] instanceof tablon_FLDsafetextarea) {
                            /*
                             * miniñapa (urgente) porque todavía no mandamos post en new!
                             */
                            $constType = '_GET';
                            $aValues = &$$constType;
                            $vlr_para_drawTabValue = (!isset($aValues['FLD__'.$pl->fields[$i]->getSQLFLD()]))? NULL:$aValues['FLD__'.$pl->fields[$i]->getSQLFLD()];
                        }

                        $r = rawurldecode($pl->fields[$i]->drawTableValue($vlr_para_drawTabValue , $ret , $pl ));
                        $retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = $r;

                        if ($aNotify!== false) {
                            if (in_array($pl->fields[$i]->getSQLFLD(), $aNotify)) {
                                if ($pl->getNotifyWhenInsertStr()) $notifyStr = str_replace("%".$pl->fields[$i]->getSQLFLD()."%", $r, $notifyStr);
                                else $notifyStr .= $pl->fields[$i]->getTitle() .": ".$r."<br />";
                            }
                        }
                    }

                    for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
                        $retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->subFields[$j]->getSQLFLD().'::'.$ret] = urldecode($pl->fields[$i]->subFields[$j]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
                    }

                    if (!isset($id)) $id = NULL; //Evitar Warning... no se que hace...
                    if ($pl->ifFieldRefres($i, $id,'insert') === true) {
                        if ($pl->ifRefreshwhen('insert') === true) {
                            $refrescar = true;
                        }
                    }

                    if ($pl->ifFieldRefres($i, $id,'error') === true) {
                        if ($pl->ifRefreshwhen('error') === true) {
                            $refrescarE = true;
                        }
                    }
                }

                $arr=array();
                foreach ($retFields as $id=>$vl) {
                    if (!i::detectUTF8($vl))
                        $vl = utf8_encode($vl);
                    $arr[$id] = ($vl);
                }

                if (is_array($aRefresh) && !empty($aRefresh) && in_array('insert', $aRefresh)) {
                    $refrescar = true;
                }

                die(
                        json_encode(
                                array(
                                    "error"=>0,
                                    "idTR"=>basename($pl->getFile()).'::'.$ret,
                                    "id"=>$ret,
                                    "values"=>$arr,
                                    "notifywheninsert"=>$notifyStr,
                                    "refreshAfter"=>$refrescar,
                                    "defaultFLD"=>basename($pl->getFile()).'::'.$pl->getDefaultFLD()."::".$ret
                                )
                        )
                );
            }
        } else {
            $aRefresh = $pl->getRefreshwhen();
            if (is_array($aRefresh) && !empty($aRefresh) && in_array('error', $aRefresh))
                die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1],"refreshAfter"=>true)));
            else
                die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));

        }
        break;
    case "copyfields":
        if (isset($_GET['actRow']) && $_GET['actRow'] == true) {
            if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'],4))=== false) {
                if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'],3))=== false) {
                    die("{}");
                } else {
                    list($acc, $pltC, $idc) = $campos;
                }
            } else {
                list($acc, $pltC, $idc, $copycond) = $campos;
            }

            $plantilla = tablon_AJAXjeditable::setPlantillaPath($pltC);
            $pl = new tablon_plantilla($plantilla);
            $idcond = $pl->getID()." = '".$idc."'";
            $sql = $pl->getAllFieldsWithCondSQL($idcond);
            $idcond = $copycond;

        } else {
            if (($campos = tablon_AJAXjeditable::decodeId($_GET['id'],4))=== false) die("{}");
            list($acc, $pltC, $idcond, $copycond) = $campos;
            $plantilla = tablon_AJAXjeditable::setPlantillaPath($pltC);
            $pl = new tablon_plantilla($plantilla);
            $sql = $pl->getAllFieldsWithCondSQL($idcond, $copycond);
        }
            $con = new con($sql);
            if ($con->error()) {
                die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));
            } else {
                $nocopy = explode(",", $pl->nocopy);
                $oncopy = false;
                $condsNR = array();
                $aIdCond = explode("=", $idcond);
                if (is_array($aIdCond)) {
                    $condsNR[$aIdCond[0]] = $aIdCond[1];
                    if ($pl->oncopy) {
                        $oncopy = explode(",", $pl->oncopy);
                        for ($j=0;$j<sizeof($oncopy);$j++) {
                            list($oncopyI[], $oncopyV[]) = explode("=", $oncopy[$j]);
                        }
                    }
                }

                if ($con->getNumRows()>0) {
                    while ($fuente = $con->getResult()) {
                        $fields = array();
                        for ($i=0;$i<sizeof($pl->fields);$i++) {
                            $fl = $pl->fields[$i];
                            if (in_array($fl->getSQLFLD(), $nocopy)) {
                                continue;
                            }
                            if ($oncopy) {
                                if (in_array($pl->fields[$i]->getSQLFLD(), $oncopyI)) {
                                    $clave = array_search($pl->fields[$i]->getSQLFLD(), $oncopyI);
                                    if ($oncopyV[$clave] == NULL) {
                                        $fields[$fl->getSQLFLD()] = NULL;
                                    } else {
                                        $fields[$fl->getSQLFLD()] = $fl->getMysqlValue($oncopyV[$clave]);
                                    }
                                    continue;
                                }
                            }
                            $fields[$fl->getSQLFLD()] = $fl->getMysqlValue($fuente[$fl->getSQLFLD()]);
                        }
                        $ret = $pl->newRow($fields, $condsNR);
                        if (!is_array($ret)) {
                            if ($pltEdit) {
                                $retFields = array();
                                $plantillaS = tablon_AJAXjeditable::setPlantillaPath($campos[3]);
                                $plShow = new tablon_plantilla($plantillaS);
                                for ($i=0;$i<$plShow->getNumFields();$i++) {
                                    $sql = $plShow->loadSingleField($i, $ret);
                                    $retFields[basename($plShow->getFile()).'::'.$plShow->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($plShow->fields[$i]->drawTableValue($plShow->fields[$i]->getValue()));
                                    for ($j=0;$j<$plShow->fields[$i]->sizeofsubFields;$j++) {
                                        $retFields[basename($plShow->getFile()).'::'.$plShow->fields[$i]->subFields[$j]->getSQLFLD().'::'.$ret] = urldecode($plShow->fields[$i]->subFields[$j]->drawTableValue($plShow->fields[$i]->subFields[$j]->getValue()));
                                    }
                                }
                                $arr=array();
                                foreach ($retFields as $id=>$vl) {
                                    if (!i::detectUTF8($vl))
                                        $vl = utf8_encode($vl);
                                    $arr[$id] = rawurldecode($vl);
                                }
                                if (is_array($aRefresh) && !empty($aRefresh) && in_array('insert', $aRefresh)) {
                                    die(json_encode(array("error"=>0,"idTR"=>basename($plShow->getFile()).'::'.$ret,"id"=>$ret,"values"=>$arr,"refreshAfter"=>true,"defaultFLD"=>basename($pl->getFile()).'::'.$pl->getDefaultFLD()."::".$ret)));
                                }
                                 die(json_encode(array("error"=>0,"idTR"=>basename($plShow->getFile()).'::'.$ret,"id"=>$ret,"values"=>$arr,"defaultFLD"=>basename($pl->getFile()).'::'.$pl->getDefaultFLD()."::".$ret)));
                            } else {
                                $retFields = array();
                                $refrescar = $refrescarE = false;
                                $aRefresh = $pl->getRefreshwhen();
                                for ($i=0;$i<$pl->getNumFields();$i++) {
                                    if ($pl->fields[$i]->getConstantTypeAjaxUpload()=="_FILES") {
                                        for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
                                            if ($pl->fields[$i]->subFields[$j]->getRealType() == "IMG_NAME") {
                                                $retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($pl->fields[$i]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
                                            }
                                            if ($pl->fields[$i]->subFields[$j]->getRealType() == "FILE_NAME") {
                                                $retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($pl->fields[$i]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
                                            }
                                        }
                                    } else {$constType = &$pl->fields[$i]->getConstantTypeAjaxUpload();
                                        $aValues = &$$constType;
                                        $r = rawurldecode($pl->fields[$i]->drawTableValue( $aValues['FLD__'.$pl->fields[$i]->getSQLFLD()] , $ret , $pl ));
                                        $retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = $r;

                                    }for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
                                        $retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->subFields[$j]->getSQLFLD().'::'.$ret] = urldecode($pl->fields[$i]->subFields[$j]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
                                    }
                                    if ($pl->ifFieldRefres($i, $id,'insert') === true) {
                                        if ($pl->ifRefreshwhen('insert') === true) {
                                            $refrescar = true;
                                        }
                                    }
                                    if ($pl->ifFieldRefres($i, $id,'error') === true) {
                                        if ($pl->ifRefreshwhen('error') === true) {
                                            $refrescarE = true;
                                        }
                                    }
                                }
                                $arr=array();
                                foreach ($retFields as $id=>$vl) {
                                    if (!i::detectUTF8($vl))
                                        $vl = utf8_encode($vl);
                                    $arr[$id] = ($vl);
                                }

                                if (is_array($aRefresh) && !empty($aRefresh) && in_array('insert', $aRefresh)) {
                                    $refrescar = true;
                                }
                                if ($refrescar === true)
                                    die(json_encode(array("error"=>0,"idTR"=>basename($pl->getFile()).'::'.$ret,"id"=>$ret,"id"=>$ret, $pl->getBaseFile(),"values"=>$arr,"refreshAfter"=>true,"defaultFLD"=>basename($pl->getFile()).'::'.$pl->getDefaultFLD()."::".$ret)));
                                else
                                    die(json_encode(array("error"=>0,"idTR"=>basename($pl->getFile()).'::'.$ret,"id"=>$ret,"values"=>$arr,"defaultFLD"=>basename($pl->getFile()).'::'.$pl->getDefaultFLD()."::".$ret)));
                            }
                        } else {
                            $aRefresh = $pl->getRefreshwhen();
                            if (is_array($aRefresh) && !empty($aRefresh) && in_array('error', $aRefresh))
                                die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1],"refreshAfter"=>true)));
                            else
                                die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));

                        }
                    }
                } else {
                    die(json_encode(array("error"=>"1","errorStr"=>"No se encontro fuente para copiar")));
                }
            }
        //}
        break;



    case "dosomething":
        list($plantilla, $req_field, $id) = explode("::", $_GET['id'],3);
        $plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
        $pl = new tablon_plantilla($plantilla);
        $princ = $pl->aFields[$req_field];
        $aReplaces = array(
            '$id'=> $id,
            '$plt'=> '"' . $plantilla . '"');
        if (isset($princ['fields'])) {
            $flds = explode("|", $princ['fields']);
            foreach ($flds as $f) {
                list($name,) = explode(":", $f);
                $aReplaces['$' . $name] = (isset($_GET[$name]))? $_GET[$name] : NULL;
            }
        }


        $func = str_replace(array_keys($aReplaces),$aReplaces, $princ['exec']).";";

        eval("\$ret = ". $func);
        if (is_array($ret)) {
            die(json_encode($ret));
        }
        die(json_encode(array("ret"=>$ret)));
        break;

}
