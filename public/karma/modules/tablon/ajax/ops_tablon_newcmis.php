<?php

    if (!defined("CHK_KARMA")) die("{}");

    switch($_GET['acc']) {
        case "list":
            if (($campos = tablon_AJAXjeditable::decodeId($_GET['id']))=== false) {
                die("Error indefinido");
            }
            list($plantilla,$campo,$id) = $campos;

            if ($plantilla == "newcmis" && $id=="0") {
                $plantilla = $campo;
                $campo = $_GET['fld'];
            }

            $plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
            $pl = new tablon_plantilla($plantilla);

            if (($idFLD = $pl->findField($campo)) === false) {
                die("Error accediendo al campo");
            }

            if (!$pl->fields[$idFLD]->isMutant()) {
                die("El campo asociado no es mutable.");
            }

            if (isset($_GET['dir'])) {
                $showfolder = $_GET['dir'];
            } else {
                $showfolder = '';
            }
            
            try {
                $profile = (isset($_GET['cmis_profile']))? $_GET['cmis_profile']:'';
                $client = cmis_config::factory($profile);

                if ( ($showfolder == '') || ($showfolder == '/') )  {
                    $folder = $client->getObjectByPath("/");
                    $objs = $client->getChildren($folder->id);

                }else {
                    // Quitamos la barra del final, que hay que incluir en elementos tipo directorio para que filetree sea feliz.
                    $showfolder = preg_replace("/\/$/",'',$showfolder);
                    $objs = $client->getChildren($showfolder);
                }

                echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
                // All dirs

                
                foreach ($objs->objectList as $obj) {
                
                    if ($obj->properties['cmis:baseTypeId'] == 'cmis:document') {
                        $name = $obj->properties['cmis:name'];
                        $ext = preg_replace('/^.*\./', '', $name);

                                
                        echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . $obj->properties['cmis:objectId'] ."\">" . $obj->properties['cmis:name'] . "</a></li>";
                        
                    } elseif ($obj->properties['cmis:baseTypeId'] == 'cmis:folder') {
                        // OJO OJO con la "/" al final del attr "rel", obligaroia para que el filetree sea feliz de nuevo. (Se quita al recoger el parametro).
                        echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . $obj->properties['cmis:objectId'] . "/\">" . $obj->properties['cmis:name'] . "</a></li>";
                    }
                }
                echo "</ul>";

                
            } catch (Exception $e) {
                die("Error accediendo al servidor<br />".$e);
            }

        break;
	}


?>
