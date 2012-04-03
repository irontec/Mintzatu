<?php
	define("__CHK",1);
	ob_start();
	session_start();
	$dirname = dirname(__FILE__);
	ini_set("include_path",$dirname."/libs:".ini_get("include_path")); // Directorios desde los que se incluiran ficheros
	define("CHK_PUBLIC",1);
	
	/* Incluimos fichero autoload */
	require_once("autoload.php");
	
	//Tratamiento de formularios:
	// Booleano que indica si se ha lanzado correctamente un formulario

	if(file_exists("{$dirname}/../before_modules.php")){
		require_once("{$dirname}/../before_modules.php");
	}

	
	//Cargamos los módulos con sus permisos	
	require_once($dirname."/modules.php");

	//Si tenemos que lanzar un fichero "trat", lo lanzamos
	if (((isset($_POST)) && (sizeof($_POST)>0) || (isset($_FILES)) && (sizeof($_FILES)>0)) && (defined("__TRAT"))&&(file_exists(__TRAT))) {
		require_once(__TRAT);
	}

	if(file_exists("{$dirname}/../before_FICH_call.php")){
		require_once("{$dirname}/../before_FICH_call.php");
	}

	ob_start();
	require_once(__FICH);
	$cont = ob_get_contents();
	ob_end_clean();
	
	if(!(isset($aConf[$seccion]["noCabecera"]) && $aConf[$seccion]["noCabecera"])){
		//Mostramos la cabecera
		require_once(__CABECERA);
	}

	if(file_exists("{$dirname}/../before_FICH_show.php")){
		require_once("{$dirname}/../before_FICH_show.php");
	}

	//Escribimos la página:
	echo $cont;

	if(file_exists($dirname."/../after_FICH_show.php")){
		require_once($dirname."/../after_FICH_show.php");
	}

	if(!(isset($aConf[$seccion]["noPie"]) && $aConf[$seccion]["noPie"])){
		//Mostramos el pie
		require_once(__PIE);
	}
	
?>
