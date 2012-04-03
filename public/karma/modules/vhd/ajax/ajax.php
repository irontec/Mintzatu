<?php



	    if ( (isset($_SERVER['HTTP_USER_AGENT'])) and (strpos($_SERVER['HTTP_USER_AGENT'],"Flash")!== false) && (isset($_GET['sid']))) {
                session_id(preg_replace("/[^0-9a-zA-Z]+/","",$_GET['sid']));
                session_start();
        } else {
                session_start();
        }

        ob_start();

   define("CHK_KARMA",1); // Constante para comprobar que todos los ficheros son cargados desde index
   if (isset($_GET['DEBUG'])) define("DEBUG",1);
   // Cargamos el "cargador" de classes
   require_once("../../../libs/autoload.php");
   // Instanciamos objeto error para crear el trigger de errores a nuestro objeto.

    $remoteIp = $_SERVER['REMOTE_ADDR'];

    $iErrorLevel = 0;
    if(!is_numeric($devErrorLevel = getEnv('DEV_IERROR_LEVEL'))){
        $devErrorLevel = 3;
    }
    if($devIps = getEnv('DEV_IPS'))
    {
        $aDevIps = explode('|',$devIps);
        if(in_array($remoteIp,$aDevIps)){
            $iErrorLevel = $devErrorLevel;
        }
    } else {
        if($remoteIp == '127.0.0.1'){
            $iErrorLevel = $devErrorLevel;
        }
    }


   $oError = new iError($iErrorLevel);
   //Cargamos constantes de ficheros requeridos
   //include_once("configuracion/defs.cfg");

    $kMenu = new krm_menu(false);
    $dateManager = new datemanager($kMenu);
    $dateManager->set_timezone();
    $l = new k_literal(KarmaRegistry::getInstance()->get('lang'));
/*    if (($loginClass = $kMenu->getLoginClass())!== false ) {
        $oLogin = new $loginClass($kMenu);
        $oLogin->doLogin($kMenu);
    } else $oLogin = false;*/

    $oLogin = false;

    $kMenu->parseMenu(false);


	$fpMAIN = false;
	if (isset($kMenu->selectedConf['main']['fullPage'])){
   	    $fpMAIN = $kMenu->selectedConf['main']['fullPage'];
	}

   if (($className = $kMenu->getSelClass())!="") {
        $o = new $className($kMenu->selectedConf);
        $currentClassName = $o->getCurrentClassName();

        if (($currentClassName!==false) && ($currentClassName!=$className)) {
      		$o = new $currentClassName($kMenu->selectedConf);
    	}

		// Ejecutar métodos en $o definidos en los externalModules de krmMenu :)
		$kMenu->register_autoClass($o);
	}



    if ((isset($o)) && (is_object($o))) {
        $o->draw();
    }

    ?>