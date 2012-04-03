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
		
		// Instanciamos objeto error para crear el trigger de errores a nuestro objeto.
		$oError = new iError();
		
		//Cargamos constantes de ficheros requeridos
		include_once("../../../../configuracion/defs.cfg");
		
		$maptemplate = $_GET["load"];
		
		$aFields = parse_ini_file(i::base_path()."/configuracion/tablonfeednews/{$maptemplate}",true);
		
		con::foo();
		
		foreach($_GET as $key=>$val){
			 switch($key){
			 	case "pubdate":
			 		$val = str_replace('/','-',$val);
			 	case "key":
			 	case "title":	
			 	case "description":
			 	case "link":
			 		$cleanval = mysql_real_escape_string($val);
					$aKeys[] = $aFields["::mapping"][$key];
					$aValues[] = "'".$cleanval."'";
			 }
		}

		$sql = "select {$aFields["::mapping"]["key"]} from {$aFields["::main"]["tab"]} where {$aFields["::mapping"]["key"]} = '".mysql_real_escape_string($_GET["key"])."' ";
		$c=new con($sql);
		if ($c->getNumRows()<=0){
			$sql = " insert into {$aFields["::main"]["tab"]} (".implode(',',$aKeys).") values (".implode(',',$aValues).")";
			$i = new con($sql);
		}else{
			$aRes['error'] = "Ya se encuentra en la Base de datos";
		}
		$aRes['key'] = $_GET["key"]; 
		echo json_encode($aRes);
