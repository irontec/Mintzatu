<?php
/**
 * Fichero de clase para AJAX::jeditable
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */



class tablon_AJAXjeditable {
	static function decodeId($id,$cmps = 3,$sep="::") {
		$aCmps = explode($sep,$id,$cmps);
		if (sizeof($aCmps)!=$cmps) return false;
		return $aCmps;
	}
	static function setPlantillaPath($plantilla) {
		tablon::foo(); // Incluye fichero principal con constante para ruta de plantillas
		return RUTA_PLANTILLAS.basename($plantilla);
	}
}


?>