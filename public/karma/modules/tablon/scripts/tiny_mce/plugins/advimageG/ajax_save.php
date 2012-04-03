<?php
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
	define("SERVER_NAME",$_SERVER['HTTP_X_FORWARDED_HOST']);
} else {
	define("SERVER_NAME",$_SERVER['HTTP_HOST']);
	//.(($_SERVER['SERVER_PORT']!="80")? ":".$_SERVER['SERVER_PORT']:""));
}
	require_once("config.php");
	require_once(dirname(__FILE__)."/clases/class.i.php");


	$ret = array();
	$ret['error'] = false;
	$cats = array();
	$imgs = array();
	$cache = dirname(__FILE__)."/".CACHE."";
	$aPasa = array('.','..');
	$imgok= array('image/jpg','image/jpeg','image/gif','image/png');






		$error = false;
		$msg = "";
		$fileElementName = 'fileToUpload';
		if(!empty($_FILES[$fileElementName]['error'])){
			switch($_FILES[$fileElementName]['error']){
				case '1':	$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';	break;
				case '2':	$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';	break;
				case '3':	$error = 'The uploaded file was only partially uploaded';	break;
				case '4':	$error = 'No file was uploaded.';	break;
				case '6':	$error = 'Missing a temporary folder';	break;
				case '7':	$error = 'Failed to write file to disk'; break;
				case '8':	$error = 'File upload stopped by extension';	break;
				case '999':	default:	$error = 'No error code avaiable';
			}
		}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none'){
			$error = 'No file was uploaded.a.';
		}else{
			$mime = i::mime_content_type($_FILES[$fileElementName]['tmp_name']);
			if (in_array($mime,$imgok)){

				$aname = explode('.',$_FILES[$fileElementName]['name']);
				$s = $aname[sizeof($aname)-1];
				$name = i::clean(str_replace(".".$s,'',$_FILES[$fileElementName]['name'])).".".$s;
				$rfile = $_FILES[$fileElementName]['tmp_name'];
				$c = $_GET['c'];
				if (is_dir($cache.$c)){
					if ( is_file($cache.$c."/".$name) ){

						$error = 'Existe un fichero con el mismo nombre';
					}else{
                        copy($rfile, $cache.$c."/".$name);
						chmod($cache.$c."/".$name, 0777);
						$ret['result'] = "Imagen guardada con éxito.";
						$ret['route'] = i::base_url()."".CACHE."".$c."/".$name;


					}

				}else{

					$error = 'Error';
				}
			}else{

				$error = 'El fichero no es valido';
			}
			@unlink($_FILES[$fileElementName]);
		}
	$ret['error'] = $error;



	echo json_encode($ret);
	exit();

?>