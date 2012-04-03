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
	define("CHK_PUBLIC",1); // Hay que incluir public chk para incluir samService
	if (isset($_GET['DEBUG'])) define("DEBUG",1);

	// Cargamos el "cargador" de classes	
	require_once("autoload.php");
	
	// Instanciamos objeto error para crear el trigger de errores a nuestro objeto.
	$oError = new iError();
	
	//Cargamos constantes de ficheros requeridos
	include_once("../../../../configuracion/defs.cfg");
	
	switch($_GET['op']) {
		case "get_entity_permissions":  //Devolver permisos de la carpeta
			$oPerm = new sam_permisos($_GET['tab'],$_GET['fld']);
			$oPerm->setId($_GET['id']);
			$permisos = $oPerm->getPermisos();
			
			//$soapClient = new SoapClient($_SERVER["SAM_WSDL"],array('trace' => 1, 'cache_wsdl' => 0));
			$soapClient = new samService(false);
			if (count($permisos > 0)){
				for($i=0; $i < count($permisos); $i++){
					switch($permisos[$i]["tipo"]){
						case "nodo": $func = 'getDatosJerarquia';break;
						case "nivel": $func = 'getDatosNivel';break;
						case "gNodos": $func = 'getDatosGrupoJerarquias';break;
						case "gPersonas": $func = 'getDatosGrupoPersonas';break;
						default: die(json_encode(array('error'=>1)));
					}
					$datosSoap = $soapClient->$func($_SESSION["id_ws_karma"],$permisos[$i]["id_permiso_sam"]);
					//$permisos[$i]["nombre"] = $datosSoap->iden;
					$permisos[$i]["nombre"] = $datosSoap['iden'];
				}
			}
			echo json_encode(array("error"=>0,"permisos"=>$permisos));
		break;
		case "get_permissions":
		  //Devolver lista de todos los permisos disponibles
			//$soapClient = new SoapClient($_SERVER["SAM_WSDL"],array('trace' => 1, 'cache_wsdl' => 0));
			$soapClient = new samService(false);
			$user = $soapClient->validar_autenticado($_SESSION['id_ws_karma']);

			switch ($_GET["type"]){
				case "nodo":
					$permisos = $soapClient->getArbolNodos($_SESSION["id_ws_karma"]);
					$padre = true;
					break;
				case "nivel":
					$permisos = $soapClient->getListadoNiveles($_SESSION["id_ws_karma"]);
					break;
				case "gNodos":
					$permisos = $soapClient->getListadoGruposJerarquia($_SESSION["id_ws_karma"]);
					break;
				case "gPersonas":
					$permisos = $soapClient->getListadoGruposPersonas($_SESSION["id_ws_karma"]);
					break;
				default: die(json_encode(array("error"=>1)));
			}
			
			die(json_encode(array("error"=>0,"permisos"=>$permisos)));
		break;
		case "add_permission":
			$oPerm = new sam_permisos($_GET['tab'],$_GET['fld']);
			$oPerm->setId($_GET['id']);
			$oPerm->setType($_GET['type']);
			$oPerm->setHijos($_GET['hijos']);
			$oPerm->setPadres($_GET['padres']);
			$oPerm->setNegado($_GET['negado']);
			$oPerm->setPermisoSam($_GET['id_permiso_sam']);
			if ($oPerm->savePermiso()) {
				die(json_encode(array("error"=>0)));	
			} else {
				die(json_encode(array("error"=>1)));	
			}
		break;
		case "delete_permision":
			$oPerm = new sam_permisos($_GET['tab'],$_GET['fld']);
			if ($oPerm->deletePerm($_GET['id_permiso'])) {
				die(json_encode(array("error"=>0)));	
			} else {
				die(json_encode(array("error"=>1)));	
			}
		break;
		
	}

		
	
?>
