<?php
	$f = basename($_GET['f']);
	$default = "";
	
	$ruta = "./cache/".$f;

	if (!file_exists($ruta)) {
		if (file_exists($f)) {
			require_once("autoload.php");
			$im = new i_image($f);
			
			if (!$im->setNewDim(20,20)) {
				$ruta = $default;
			} else {
				$im->prepare();
				$im->imResize($ruta);	
			}
		}
	}
	
	header("Content-type: image/png");
	echo readfile($ruta);

?>