<?php
	session_name("karmaPrivate");
	session_start();
	session_cache_limiter("private");
	header("Expires: 0");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false); // HTTP/1.1
	header("Pragma: no-cache");// HTTP/1.0
	/* fin de header anti cache */

	define("CHK_KARMA",1); // Constante para comprobar que todos los ficheros son cargados desde index
	if (isset($_GET['DEBUG'])) define("DEBUG",1);

	// Cargamos el "cargador" de classes	
	require_once("../../../libs/autoload.php");
	
	@date_default_timezone_set($_SESSION['K_TIMEZONE']); //SYSTEM DEFAULTS


	switch($_GET['op']) {
		case "doList":
			hdKarma_dirTree::drawList($_GET['root']);
		break;
		case "new":
			echo hdKarma_dirTree::newDir($_GET['n'],$_GET['idpadre']);
		break;
		case "doOptions":
			echo hdKarma_dirTree::drawOptions();
		break;
	
	
	}