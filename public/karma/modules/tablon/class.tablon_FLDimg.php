<?php
/*
 * Clase para campo tipo SIZE de IMG
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */
class tablon_FLDimg_SIZE extends tablon_FLDsafetext {

		public function getMysqlValue($value) {
			$this->setValue($value['size']);
			return '\''.$value['size'].'\'';
		}

		public function drawTableValue($v) {
			return i::tamFich($v);

		}

}

/*
 * Clase para campo tipo NOMBRE DE IMAGEN
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */
class tablon_FLDimg_NAME extends tablon_FLDsafetext {

		function __construct($conf,$idx,$plt = false) {
			parent::__construct($conf,$idx,$plt);
			$this->unique = true;
		}

		public function getMysqlValue($value) {


			$aName  = explode(".",$value['name']);
			$exten = $aName[sizeof($aName)-1];
			unset($aName[sizeof($aName)-1]);
			$aTmp=array();
			foreach ($aName as $n) $aTmp[]=i::clean($n);
			$value['name'] = str_replace(array("_____","____","___","__"),"_",implode("_",$aTmp)).".".$exten;
			if ( trim($value['name'])=="." || trim($value['name'])=="" ) $value['name']="";
			$this->setValue($value['name']);
			return '\''.$value['name'].'\'';
		}
}



/**
 * Clase para campo tipo img
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */
class tablon_FLDimg extends tablon_FLD {
	public $img_name = false;
	public $img_size = false;

	public function getType() {
		return "ajaxfileupload";
	}
	public function getSQLType() {
		return "mediumblob".$this->getSQLRequired();;
	}

	public function registerCustomSubField($type,$idSubField) {
		switch ($type) {
			case "IMG_NAME":
				$this->img_name = $idSubField;
			break;
			case "IMG_SIZE":
				$this->img_size = $idSubField;
			break;
		}

	}
	public function getMysqlValue($value) {
		if (!file_exists($value['tmp_name'])) return NULL;
		if (isset($this->conf['compress']) && ($this->conf['compress']=="0") ){
				return '\''.$this->cleanMysqlValue(file_get_contents($value['tmp_name'])).'\'';
		}else{

				return 'compress(\''.$this->cleanMysqlValue(file_get_contents($value['tmp_name'])).'\')';
		}

	}

	public function getSQLFLDRequest() {
		return $this->subFields[$this->img_name]->getSQLFLD();
	}
	/*public function getSQLFLD() {
		return 'img_binario'; <==== WTF?????
	}*/

	public function getSQLFLDRaw($t=false) {
		if (isset($this->conf['compress']) && ($this->conf['compress']=="0") ){
				return ''. $this->getSQLFLD() .'';
		}else{
				return 'uncompress('. $this->getSQLFLD() .')';
		}

	}

	public function getConstantTypeAjaxUpload() {
		return "_FILES";
	}

	public function drawTableValueEdit($value,$clone=false,$disabled=false) {
		//name="'.$this->getSQLFLD().'"
		return
			'<div class="updatedValue">'. $this->drawTableValue($value). '</div>'.
			'<input type="file" name="'.$this->getSQLFLD().'" id="'.$this->getSQLFLD().'" '.(($this->isRequired())? ' class="required"':'').'/>';
	}

	private function existe($n) {
		$aFields = parse_ini_file(RUTA_PLANTILLAS.$this->plt,true);
		$sql = 'select '.$aFields['::main']['id'].' from '.$aFields['::main']['tab'].' where '.$this->conf['name_fld'].'= \''.$n.'\'';
		$con = new con($sql);
		return ($con->getNumRows()!==0);
	}


	// Este método recibe como referencia porque se cambia el value... malo si, pero es lo que hay... (supongo que no costaría setearle la propieda al subfield asociado, pero esto es caos)
	public function preInsertCheckValue(&$value) {
		// Necesito el nombre de la tabla de la plantilla
		if (!file_exists(RUTA_PLANTILLAS.$this->plt)) return false;
		$aFields = parse_ini_file(RUTA_PLANTILLAS.$this->plt,true);


		if ( ((!is_array($value)) || ($value['name']=="")) ) {
		    if ($this->isRequired())  {
			    return array("1","No se ha subido una imagen.");
		    } else {
		        return true;
		    }
		}


		$exten = $nFile = "";
		if (preg_match("/(.*)\.([a-zA-Z0-9]{1,5})$/",$value['name'],$rExtension)) {
			$extension = $rExtension[2];
			$nFile = $rExtension[1];
		} else $nFile = $value['name'];

		$NombreSinPuntos = explode(".",$nFile);
		$value['name'] = "";
		foreach ($NombreSinPuntos as $idx => $nombre) $value['name'] .= i::clean($nombre)."_";
		$value['name'] = substr($value['name'],0,-1) . ".".$extension;

		// Que pasa si tenemos más de 4 guiones bajos?
		//$value['name'] = str_replace(array("_____","____","___","__"),"_",implode("_",$aTmp)).".".$exten;
		//vaR_dump("***".$value['name']);
		$value['name'] = preg_replace("/[_]{2,}/","_",$value['name']);
		//vaR_dump("*****".$value['name']);


		// Donde dije v digo value? Zelako cagada... llevamos años sin imágenes con nombre único...
		//if ( trim($v['name'])=="." || trim($v['name'])=="" ) return true;


		$nombreTemp  = $value['name'];

		$cont = 0;
		do {
	 		$sql = 'select '.$this->subFields[$this->img_name]->getSQLFLD().' as url from '.$aFields['::main']['tab'].' where '.$this->subFields[$this->img_name]->getSQLFLD().'= \''.$value['name'].'\'';
	 		//echo $sql;
			$con = new con($sql);
			if ($con->error()) return array("1","Error indeterminado antes de guardar la imagen");
	 		if ($con->getNumRows()==0) break;
			$value['name'] = preg_replace("/^([^\.]*)/","\\1_".$cont++,$nombreTemp);
			$value['name'] = preg_replace("/[_]{2,}/","_",$value['name']);


	 	} while (1);

	 	//var_dump($value['name']);

		if (!getimagesize($value['tmp_name'])) return array("1","El fichero no es una imagen válida.");
		return true;

	}


//	public function drawTableValue($v = false) {
    public function drawTableValue($v) {
	if ($v === false) $v = $this->subFields[$this->img_name]->getValue();

		return '<img src="../cache/tablon/img/'.$this->getPlt().'/'.$this->getSQLFLD().'/'.$v.'?thumb" class="mag" alt="'.$v.'" >';
	}
	public function drawTableValueEspecialEdit($v = false) {
		if ($v === false) $v = $this->subFields[$this->img_name]->getValue();

		return '<img src="./icons/edit.png"   />';
	}

	public function getImg($v = false) {
		//if ($v === false) $v = $this->subFields[$this->img_name]->getValue();

		return '<img src="../cache/tablon/img/'.$this->getPlt().'/'.$this->getSQLFLD().'/'.$v.'?thumb" class="" alt="'.$v.'" >';
	}

	public function processReturnJSONValue($v,$pl,$id,$edit=false) {
		$ret = array(0=>0,"principal"=>rawurlencode($this->drawTableValue($v['name'])),"subfields"=>array());
		for($i=0;$i<$this->sizeofsubFields;$i++) {
			$ret['subfields'][basename($pl->getFile()).'::'.$this->subFields[$i]->getSQLFLD().'::'.$id] = rawurlencode($this->subFields[$i]->drawTableValue($this->subFields[$i]->getValue()));
		}

		return $ret;

	}
}


?>
