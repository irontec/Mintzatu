<?php
	define("CHK_KARMA",1);	
	require_once("../karma/libs/autoload.php");
	$args = explode("/",$_GET['args']);
	$modulo = i::clean(array_shift($args));
	
	if (!file_exists(dirname(__FILE__)."/../karma/modules/".$modulo)) die("No existe el módulo especificado [".$modulo."].");
	if (!file_exists($modulo)) {
		umask(0000);
		if (!mkdir($modulo, 0755)) die("Imposible crear directorio cache para el módulo especificado [".$modulo."].");
	} else {
		if (!is_dir($modulo)) die("Existe el elemento [".$modulo."] pero es un fichero.");
	}	
	chdir($modulo);

	eval($modulo.'::generateCache("'.implode("/",$args).'");');
	
?>