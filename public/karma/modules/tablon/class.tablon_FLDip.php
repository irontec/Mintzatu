<?php
/**
 * Fichero de clase para campo tipo ENUM
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_FLDip extends tablon_FLD {

	public function getSQLType() {

		return
			"varchar(50) default null \n";
	}
	public function getMysqlValue($value) {
		$c = new con("select 1;");
		$this->setValue(tablon_FLD::cleanMySQLValue($value));
		$vl = $this->getValue();
		return '\''.$vl.'\' ';
	}
	public function makear($value) {



		$a = long2ip($value);
		//$b = gethostbyaddr($a);
		//$b = "<span title=\"".$a."\">".$b."</span>";

		return $a;
	}
	public function drawTableValue($value) {
		if($value==0) return false;
		return $this->makear($value);

	}
	/* Aunque el resto de parámetros además de $value no se usan, hay que ponerlas por ser una clase que extiende a otra (FLD)
	public function drawTableValueEdit($value,$clone=false,$disabled=false) {
		$this->setValue($value);
		return '<input type="'.$this->getType().'" datef="'.$this->getdateformat(1).'" name="'.$this->getSQLFLD().(($clone)? '_clone':'').'" '.(($clone)? 'class="clone"':'').' class="date-pick" value="'.$this->drawTableValue($this->getValue()).'" />';
	}*/
	/*
	public function drawEditJSON() {
		$this->setValue($value);
		$aRet['datef'] = $this->getdateformat(1);
		$aRet['name'] = $this->getSQLFLD();
		$aRet['class'] = "date-pick";

		return json_encode($aRet);
	}
	*/
	public function getType() {
		//var_dump($this->conf);
		return "text";
	}
	/*
	public function getCl() {
		//var_dump($this->conf);
		return "jdate";
	}
	*/
	public function getdateformat($a=false) {
		$ds = isset($this->conf['separator'])? $this->conf['separator']:"-";
		$def = "d".$ds."m".$ds."Y";
		$def2 = "dd".$ds."mm".$ds."yyyy";
		if(isset($this->conf['dateformat'])) {
			switch($this->conf['dateformat']){
				case "ymd":
					$def = "Y".$ds."m".$ds."d";
					$def2 = "yyyy".$ds."mm".$ds."dd";
				break;
				case "dmy":
				default:
					$def = "d".$ds."m".$ds."Y";
					$def2 = "dd".$ds."mm".$ds."yyyy";
				break;
			}
		}


		/*if(isset($this->conf['date'])) {
			switch($this->conf['date']){
				case "datetime":
					$def = "%d".$ds."%m".$ds."%Y %H:%i";
				break;
				case "date":
					$def = "%d".$ds."%m".$ds."%Y";
					$def2 = "dd".$ds."mm".$ds."yyyy";
				break;
				case "time":
					$def = "%H:%i";
				break;
			}
		}*/
		if($a) return $def2;
		return $def;
	}

	public function getSQL($tab,$alias = true) {

		//$def = $this->getdateformat();
		//if(isset($this->conf['format']))  $def = $this->conf['format'];
		$ret = "";
		$ret = " ".$this->getSQLFLDRequest()." ";
		if ($alias) $ret .= " as '".$this->getAlias()."'";
		//echo $ret;
		return $ret;
	}

}

?>