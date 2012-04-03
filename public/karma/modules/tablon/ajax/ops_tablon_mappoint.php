<?php
usleep(2);

    if (!defined("CHK_KARMA")) die("{}");


    switch($_GET['op']) {
        case "mappoint":
            list($plantilla, $req_field, $id) = explode("::", $_GET['id'],3);
            if ($plantilla=="new") {
                list(,$plantilla, $id, $req_field) = explode("::", $_GET['id'],4);
            }
            $plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
            $pl = new tablon_plantilla($plantilla);
            $idFLD = $pl->findField($req_field);
            $ret = $pl->fields[$idFLD]->storeData();
            die(json_encode(array("ret"=>$ret)));
            break;

    }
