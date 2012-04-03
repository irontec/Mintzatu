<?php

	if (!defined("CHK_KARMA")) die("{}");

	switch($_GET['acc']) {
		case "list":
			if (($campos = tablon_AJAXjeditable::decodeId($_GET['id']))=== false) {
			    die("Error indefinido");
			}
			list($plantilla,$campo,$id) = $campos;

			if ($plantilla == "new" && $id=="0") {
				$plantilla = $campo;
				$campo = $_GET['fld'];
			}

			$plantilla = tablon_AJAXjeditable::setPlantillaPath($plantilla);
			$pl = new tablon_plantilla($plantilla);

			if (($idFLD = $pl->findField($campo)) === false) {
			    die("{error:2,errorStr:'No existe el campo'}");
			}
			if (!$pl->fields[$idFLD]->isSourceFileSystem()) {
			    die("Error accediendo al campo");
			}

    		// TODO SECURITY UPDATES!!!!
			$root = $pl->fields[$idFLD]->source_path;
            // mini-sec!
            $root = preg_replace("/^[^\/]+/",'',$root);

			$dir = urldecode($_GET['dir']);
			$real_path = utf8_encode($root . $dir);

			if( file_exists($real_path) ) {

				$files = scandir($real_path);
        		natcasesort($files);
				if( count($files) > 2 ) { /* The 2 accounts for . and .. */

                echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
                // All dirs
                foreach( $files as $file ) {
                        if( file_exists($real_path . $file) && $file != '.' && $file != '..' && is_dir($real_path . $file) ) {
                                echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities(utf8_decode($dir . $file)) . "/\">" . htmlentities(utf8_decode($file)) . "</a></li>";
                        }
                }
                // All files
                foreach( $files as $file ) {
                        if( file_exists($real_path . $file) && $file != '.' && $file != '..' && !is_dir($real_path . $file) ) {
                                $ext = preg_replace('/^.*\./', '', $file);
                                echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities(utf8_encode($dir . $file)) . ";" . basename($file). ";". filesize($real_path.$file) . ";". i::mime_content_type($real_path.$file) ."\">" . htmlentities($file) . "</a></li>";
                        }
                }
                echo "</ul>";
        }
}


 		break;



	}


?>
