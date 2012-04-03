<?php

	
	define("__CHK",1);
	ob_start();
	session_start();
	ini_set("include_path",".:./libs:".ini_get("include_path")); // Directorios desde los que se incluiran ficheros
	define("CHK_PUBLIC",1);
	
	/* Incluimos fichero autoload */
	require_once("autoload.php");
	
	//Tratamiento de formularios:
	// Booleano que indica si se ha lanzado correctamente un formulario
	
	//Cargamos los módulos con sus permisos	

	
	require_once("modules.php");

	//Si tenemos que lanzar un fichero "trat", lo lanzamos
	if ((isset($_POST)) && (sizeof($_POST)>1) && (defined("__TRAT"))&&(file_exists(__TRAT))) {
		require_once(__TRAT);
	}

	ob_start();
	require_once(__FICH);
	$cont = ob_get_contents();
	ob_end_clean();

	//Escribimos la página:
	require_once(__CABECERA);
	echo $cont;
	require_once(__PIE);
	//pie
	
?>
