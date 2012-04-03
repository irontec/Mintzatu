<?php
/**
 * Fichero principal de la clase tablon con funcionalidades para SAM,
 * añade el javascript + métodos estáticos para la gestión de permisos.
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */


class sam_tablon extends tablon{
	
	function __construct(&$conf) {
		$this->aJs[] = "../modules/sam/scripts/tablon_sam.js";
		$this->aCss[] = "../modules/sam/css/extra_impromptu.css";
		parent::__construct($conf);
	}

}

?>