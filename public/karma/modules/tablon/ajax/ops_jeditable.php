<?php
    if (!defined("CHK_KARMA")) die("{}");
    switch($_GET['acc']) {
        case "load":
            if (false === ($campos = tablon_AJAXjeditable::decodeId($_GET['id']))) {
                die("{}");
            }

            list($plantilla,$campo,$id) = $campos;
            $plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);

            $pl = new tablon_plantilla($plantilla);

            if (($idFLD = $pl->findField($campo)) === false) die("{}");

            if (!method_exists($pl->fields[$idFLD], "drawEditJSON")) {
                // Si jEditable pide campo, y no se tiene en JSON, devolvemos el de tablonEdit.
                // Deshabilitado de momento...
                /*if (method_exists($pl->fields[$idFLD],"drawTableValueEdit")) {
                    $pl->loadSingleField($idFLD,$id);
                    echo $pl->fields[$idFLD]->drawTableValueEdit();
                    exit();
                }*/
                die("{}");
            }

            $pl->loadSingleField($idFLD, $id);
            $pl->fields[$idFLD]->setCurrentID($id);
            echo $pl->fields[$idFLD]->drawEditJSON();
            break;

        case "save":
            $aIds = array();
            if ((isset($_GET['patron'])) && (is_array($_GET['id'])) ) {
                foreach($_GET['id'] as $id) $aIds[] = $_GET['patron'].$id;
            } else {
                $aIds = array($_GET['id']);
            }
            $retValue = array();
            $errorValues = array();
            $first = true;


            foreach ($aIds as $curId) {
                if (false === ($campos = tablon_AJAXjeditable::decodeId($curId))) {
                    die("{}");
                }

                list($plantilla,$campo,$id) = $campos;

                if ($first) {
                        $plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
                        $pl = new tablon_plantilla($plantilla);
                        if (false === ($idFLD = $pl->findField($campo))) {
                            die("{error:2,errorStr:'No existe el campo'}");
                        }
                        $constType = $pl->fields[$idFLD]->getConstantTypeAjaxUpload();
                        $aValues = $$constType;
                        $conds = array();
                        foreach ($_GET as $idx => $value) {
                            if (preg_match("/^COND__(.*)/", $idx, $fldNameH)) {
                                $conds[$fldNameH[1]] = stripslashes($value);
                            }
                            if (preg_match("/^CONDT__(.*)/", $idx, $fldNameH)) {
                                $conds['triggerCond_'.$fldNameH[1]] = stripslashes($value);
                            }
                        }
                        $first = false;
                }


            if (count($conds) == 0) {
                    $value = $pl->saveSingleField($idFLD, $aValues['value'], $id);
                } else {
                    $value = $pl->saveSingleField($idFLD, $aValues['value'], $id, false, $conds);
                }


                if ((!is_array($value)) || ((isset($value[0])) && ($value[0]==0))) {
                    $retValue[$id] = $value;
                } else {
                    $errorValues[$id] = $value;
                }

                $refrescar = $pl->ifFieldRefres($idFLD, $id, 'update');
                if ($refrescar === false) {
                    $refrescar = $pl->ifRefreshwhen('update');
                }
            }

            if (sizeof($retValue) > 0) {

                $changedIDS = array_keys($retValue);
                if (sizeof($retValue) == 1) {
                    $retValue = array_shift($retValue);
                }
                die(json_encode(
                    array(
                        "error" => 0,
                        "value" => $retValue,
                        "changedIDs" => $changedIDS,
                        "refreshAfter" => $refrescar
                    )
                ));
            } else {
                if (sizeof($errorValues) == 1) {
                    $fErrorValues = array_shift($errorValues);
                } else {
                    $fErrorValues = array(array(), array());
                    foreach ($errorValues as $idx => $eA) {
                        $fErrorValues[0][] = $eA[0];
                        $fErrorValues[1][] = $eA[1];
                    }
                    $fErrorValues[0][] = implode(" · ", $fErrorValues[0]);
                    $fErrorValues[1][] = implode(" · ", $fErrorValues[1]);
                }
                die(json_encode(
                    array(
                        "error" => $fErrorValues[0],
                        "errorStr" => $fErrorValues[1]
                    )
                ));
            }

            break;
    }
