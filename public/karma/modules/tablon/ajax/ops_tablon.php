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
	if (isset($_GET['DEBUG'])) {
	    define("DEBUG",1);
	}

	// Cargamos el "cargador" de classes
	require_once("../../../libs/autoload.php");
    spl_autoload_register('__autoload');

	@date_default_timezone_set($_SESSION['K_TIMEZONE']); //SYSTEM DEFAULTS



	if (isset($_SESSION["K_MYSQL_TIMEZONE"])){
			new con("set time_zone = '".$_SESSION["K_MYSQL_TIMEZONE"]."';"); // MEJOR GMT
	}

	// Instanciamos objeto error para crear el trigger de errores a nuestro objeto.
	$oError = new iError();

	switch($_GET['op']) {
		case "jeditable":
			require(dirname(__FILE__)."/ops_jeditable.php");
		break;
		case "tablon":
			require(dirname(__FILE__)."/ops_tablon_propias.php");
		break;
		case "tablon_edit":
			require(dirname(__FILE__)."/ops_tablon_edit.php");
		break;
		case "tablon_mass":
			require(dirname(__FILE__)."/ops_tablon_mass.php");
		break;
		case "tablon_multisearchable":
			require(dirname(__FILE__)."/ops_tablon_multisearchable.php");
		break;
		case "tag_autocomplete":
			require(dirname(__FILE__)."/ops_tablon_autocomplete.php");
		break;
		case "openView":
			require(dirname(__FILE__)."/ops_tablon_openView.php");
		break;
		case "fsfilefs":
			require(dirname(__FILE__)."/ops_tablon_fsfilefs.php");
		break;
		case "popup":
			require(dirname(__FILE__)."/ops_tablon_popup.php");
		break;
		case "mappoint":
            require(dirname(__FILE__)."/ops_tablon_mappoint.php");
        break;
        case "wymeditor":
            require(dirname(__FILE__)."/ops_tablon_wymeditor.php");
        break;
        case "newcmis":
            require(dirname(__FILE__)."/ops_tablon_newcmis.php");
        break;
		default: die("{}");
	}
