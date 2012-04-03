<?php

	if (!defined("CHK_KARMA")) die("{}");

	define("WYMEDITOR_CONF", dirname(__FILE__). '/../../../../configuracion/wym_karma_conf.cfg');
	
	switch($_GET['acc']) {
		case "configuracion":
		
		$parser = new tablon_FLDxhtmlparser();
		

		if (!$parser->loadFile(WYMEDITOR_CONF)) {
			die(json_encode(array("error"=>$parser->getError())));
		}
		
		if (!$parser->parseFile()) {
			die(json_encode(array("error"=>$parser->getError())));
		}
		
		
		die($parser->getJSON());
				
			
 		break;



	}


?>
