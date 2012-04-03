<?php
/**
 * Fichero para la carga automática de clases
 * 
 * 
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

	function __autoload($class) {
		$f = dirname(__FILE__)."/../clases/class.".$class.".php";
		if (file_exists($f)) require($f);
		else {
		if (defined("CHK_KARMA")) {
				$f = dirname(__FILE__)."/../karma/clases/class.".$class.".php";
				if (file_exists($f)) require($f);
				else {
					if (($pos = strpos($class,"_",0))!==false) {
						$classPrimaria = substr($class,0,$pos);					
					} else $classPrimaria = $class;
					$f = dirname(__FILE__)."/../karma/modules/".$classPrimaria."/class.".$class.".php";
					
					if (file_exists($f)) require($f); 	
				}
			}
			if (defined("CHK_PUBLIC")) {
				$f = dirname(__FILE__)."/../custom_clases/class.".$class.".php";
				if (file_exists($f)) require($f);
				else {
					$f = dirname(__FILE__)."/../custom_clases/class.".$class.".php";
					if (file_exists($f)) require($f); 	
				}
			}
			
		}
	}

?>
