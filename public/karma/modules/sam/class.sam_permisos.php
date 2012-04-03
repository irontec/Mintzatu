<?php


class sam_permisos {
	public $tab = false;
	public $fld = false;
	public $id = false;
	public $type = false;
	public $hijos = false;
	public $padres = false;
	public $negado = false;
	public $idPermisoSAM = false;
	
	function __construct($tab,$fld) {
		$this->tab = tablon_FLD::cleanMysqlValue($tab);
		$this->fld = tablon_FLD::cleanMysqlValue($fld);
	}
	
	public function setId($id) {
		$this->id = tablon_FLD::cleanMysqlValue($id);
	}
	
	public function getPermisos() {
		$sql = 'select id_permiso,';
		$sql .= $this->fld.' as id_perm_externo,';
		$sql .= 'id_permiso_sam,';
		$sql .= 'tipo,';
		$sql .= 'negativo,';
		$sql .= 'padres,';
		$sql .= 'hijos ';
		$sql .= ' from '.$this->tab.' where '.$this->fld.' = \''.$this->id.'\'';
		$con = new con($sql);
		$permisos = array();
		if ($con->getNumRows()<=0) return $permisos;
		$permisos = array();
		while ($row = $con->getResult()) $permisos[] = $row;
		
		
		return $permisos;
	}
	
	
	public function setType($t) {
		switch ($t) {
			case 'nodo': case 'nivel': case 'gNodos': case 'gPersonas':
				$this->type = $t;
			break;	
		}
		return;
	}
			
	public function setHijos($h) {
		$this->hijos = (int)(bool)$h;
	}

	public function setPadres($p) {
		$this->padres = (int)(bool)$p;
	}
	
	public function setNegado($n) {
		$this->negado = (int)(bool)$n;
	}
	
	public function setPermisoSam($idPerm) {
		$this->idPermisoSAM = (int)$idPerm;
	}

	public function savePermiso() {
		$sql = 'insert into '.$this->tab.'('.$this->fld.', id_permiso_sam, tipo, negativo, padres, hijos) values (';
		$sql .= '\''.$this->id.'\',';
		$sql .= '\''.$this->idPermisoSAM.'\',';
		$sql .= '\''.$this->type.'\',';
		$sql .= '\''.$this->negado.'\',';
		$sql .= '\''.$this->padres.'\',';
		$sql .= '\''.$this->hijos.'\');';
		$con = new con($sql);
		if ($con->error()) return false;
		return $con->getId();

	}
	public function deletePerm($id) {
		$sql = 'delete from '.$this->tab.' where id_permiso=\''.(int)$id.'\' limit 1';
		$con = new con($sql);
		if ($con->error()) return false;
		return true;

	}
	
	
}

?>