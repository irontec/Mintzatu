#!/usr/bin/php
<?php
	if (getenv('PHP_CGI_FILE') != "move") exit(1);
	$plt_path = dirname(__FILE__)."/../../../../configuracion/tablon/".basename(getenv('KRM_PLT'));
	if (!file_exists($plt_path)) {
		echo "Error encontrando la plantilla";
		exit(2);
	}

	$plt = parse_ini_file($plt_path,true);


	if (!isset($plt[getenv('KRM_FLD')]['filesystem'])) {
		echo "Error campo no existente o no válido";
		exit(3);
	}

	$dst_Path = dirname(__FILE__)."/../../../../".$plt[getenv('KRM_FLD')]['filesystem']."/";


	if (!file_exists(getenv('KRM_TMP_FILE')))  {
		echo "Error fichero destino no Existe";
		exit(4);
	}
	if (!is_readable(getenv('KRM_TMP_FILE'))) {
		echo "Error fichero destino no accesible";
		exit(5);
	}

	if (!is_dir($dst_Path.basename(getenv('KRM_PLT')))) mkdir($dst_Path.basename(getenv('KRM_PLT')),0755);
	chdir($dst_Path.basename(getenv('KRM_PLT')));


	if (getenv('KRM_ID')<10) {
		if (!file_exists("0")) mkdir("0",0755);
		chdir("0");
	} else {
		for($i=0;$i<strlen(getenv('KRM_ID'))-1;$i++) {
			clearstatcache(); // Sino se limpia la cache... al ejecutar este desde consola, y crear una carpeta con 2 numeros consecutivos iguales...( 112 113...) PETA!!! tocate los cojones!! contigo no bicho!!
			$krmId = getenv('KRM_ID');
			if (!is_dir($krmId{$i})) mkdir($krmId{$i},0755);
			chdir($krmId{$i});
		 }
	}

	if (getenv('KRM_EXTENSION')) {
		$destFile = getenv('KRM_ID') . "." . getenv('KRM_EXTENSION');
	} else {
		$destFile = getenv('KRM_ID');
	}


	//if (getenv('KRM_ACTION') == "mv") {
		//rename(getenv('KRM_TMP_FILE'),$destFile);
	//} else {
	// No se podía hacer move ya que utilizaba al usuario propietario
	// Ahora hace lo que debería, porque no queda en /tmp/ el archivo
		copy(getenv('KRM_TMP_FILE'),$destFile);
	//}

	umask(0);
	chmod($destFile,0644);
	exit(0);


?>
