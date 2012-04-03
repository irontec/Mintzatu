<?php
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
	define("SERVER_NAME",$_SERVER['HTTP_X_FORWARDED_HOST']);
} else {
	define("SERVER_NAME",$_SERVER['HTTP_HOST']);
	//.(($_SERVER['SERVER_PORT']!="80")? ":".$_SERVER['SERVER_PORT']:""));
}

	require_once(dirname(__FILE__)."/../../../../../../clases/class.i.php");
	require_once(dirname(__FILE__)."/../../../../../../clases/class.i_image.php");

	$ret = array();
	$ret['error'] = false;
	$cats = array();
	$imgs = array();
	//$cache = dirname(__FILE__)."/../../../../../../../cache/tinyCache/";

        $legacyPath = '/../../../../../../../tinyCache/';

        if (is_dir($legacyPath)) {
           $cache = $legacyPath;
        } else {
            $cache = '/../../../../../../../cache/tinyCache/';
        }

        $cache = dirname(__FILE__).$cache;




        $aPasa = array('.','..');
	$imgok= array('image/jpg','image/gif','image/png');

	$value = i::clean($_GET['val']);

	switch($_GET['w']){
		case "all":
			if ($gestor = opendir($cache)) {
				while (false !== ($a= readdir($gestor))) {
					if(in_array($a,$aPasa)) continue;
					if (!is_dir($cache.$a)) continue;
	        		$cats[] = $a;
	    		}
	    		closedir($gestor);
	    		if (sizeof($cats)<=0){
	    			mkdir($cache."general", 0777);
	    			chmod($cache."general", 0777);
	    			$cats[] = "general";
	    		}
			}else{
				$ret['error'] = "imposible abrir el directorio ".$cache;
			}
		case "images":
			if (!isset($_GET['c'])) $c = $cats[0]; else $c = $_GET['c'];
			if ($gestor = opendir($cache.$c)) {
				while (false !== ($a= readdir($gestor))) {
					if(in_array($a,$aPasa)) continue;
					if (!is_file($cache.$c.'/'.$a)||!in_array(i::mime_content_type($cache.$c.'/'.$a),$imgok)) continue;
	        		$imgs[] = $a;

	    		}
	    		closedir($gestor);
			}
		break;
		case "newCat":
			if (trim($value)!=""){
				if ($gestor = opendir($cache)) {
					while (false !== ($a= readdir($gestor))) {
						if(in_array($a,$aPasa)) continue;
						if (!is_dir($cache.$a)) continue;
		        		$cats[] = $a;
		    		}
		    		closedir($gestor);
		    		if (!in_array($value,$cats)){
		    			mkdir($cache.$value, 0777);
		    			chmod($cache.$value, 0777);
		    			$ret['result'] = "Categoría insertada con éxito.";
		    		}else{
		    			$ret['error'] = "Entrada repetida";
		    		}
				}else{
					$ret['error'] = "imposible abrir el directorio ".$cache;
				}
			}else{
				$ret['error'] = 'Debes introducir un nombre';
			}
		break;
		case "delCat":
			if (is_dir($cache.$value)){
			    i::rmrf($cache . $value);
    			$ret['result'] = "Categoría borrada con éxito.";
			}else{
				$ret['error'] = 'Error';
			}
		break;

	}



	$ret['categories'] = $cats;
	$ret['images'] = $imgs;
	$ret['path'] = i::base_url()."../../../../../../../cache/tinyCache/".$c."/";
//	$ret['svname'] = "http://".SERVER_NAME;
	$ret['svname'] = "";

	echo json_encode($ret);
	exit();

?>
