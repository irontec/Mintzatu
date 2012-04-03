<?php
/**
 * Fichero de clase para la carga de una plantilla de tablon [.plt]
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class estadisticas_plantilla extends tablon_plantilla {
	private $tipoTabla = false;
	private $indiceCols = false;
	private $indiceFilas =false;
	private $ttlCols = false;
	private $ttlFilas = false;
	private $datoTabla = false;
	private $graph = false;
	private $graphVal = false;
	public $fieldsAlias = false;

	function __construct($f,$noFields= false) {
		parent::__construct($f,$noFields);
		$this->file = $f;
		if (!file_exists($this->file)) return false;
		$aFields = parse_ini_file($this->file,true);
		if(isset($aFields['::main']['tipoTabla'])){
			$this->tipoTabla = $aFields['::main']['tipoTabla'];
		}
		if(isset($aFields['::main']['indicesColumnas'])){
			$this->indiceCols = $aFields['::main']['indicesColumnas'];
		}
		if(isset($aFields['::main']['indicesFilas'])){
			$this->indiceFilas = $aFields['::main']['indicesFilas'];
		}
		if(isset($aFields['::main']['datoTabla'])){
			$this->datoTabla = $aFields['::main']['datoTabla'];
		}
		if(isset($aFields['::main']['ttlCols'])){
			$this->ttlCols = $aFields['::main']['ttlCols'];
		}
		if(isset($aFields['::main']['ttlFilas'])){
			$this->ttlFilas = $aFields['::main']['ttlFilas'];
		}
		if (isset($aFields['::main']['groupbyTtl'])) {
			$this->groupbyTtl = $aFields['::main']['groupbyTtl'];
		}
		if (isset($aFields['::main']['graph'])) {
			$this->graph = $aFields['::main']['graph'];
		}
		if (isset($aFields['::main']['graphVal'])) {
			$this->graphVal = $aFields['::main']['graphVal'];
		}
		$this->doFields();
	}

	public function getTipoTabla() {
		return $this->tipoTabla;
	}

	public function getIndiceCols() {
		return $this->indiceCols;
	}

	public function getIndiceFilas() {
		return $this->indiceFilas;
	}

	public function getTtlCols() {
		return $this->ttlCols;
	}

	public function getTtlFilas() {
		return $this->ttlFilas;
	}

	public function getDatoTabla() {
		return $this->datoTabla;
	}

	public function getGraph() {
		return $this->graph;
	}

	public function getGraphVal() {
		return $this->graphVal;
	}

	protected function doFields() {
		$this->fields = array();
		$this->fieldsAlias = array();
		foreach ($this->aFields as $idx => $aFld) {
			if (preg_match("/::/",$idx)) continue;
			$objName = "estadisticas_FLD".strtolower($aFld['type']);
			$this->fields[] = $this->fieldsAlias[$aFld['alias']] = new $objName($aFld,$idx,$this->getBaseFile());

		}
		$this->sizeofFields = sizeof($this->fields);
	}

	public function saveSingleField($idFLD,&$value,$id,$idSubFLD = false) {
		return;
	}

	public function doDelete($id,$op='1') {
		return;
	}

	public function dounDelete($id) {
		return;

	}
	public function newRow($fields,$conds) {
		return;
	}

	protected function buildConds($conf,$val) {
		$aConds = array();
		//$pl = &$this->plantillaPrincipal;
		if ($del = $this->getDeletedFLD()) {
			$aConds[] = ' '.$this->getTab().'.'.$this.'=\'0\'';
		}
		if (isset($conf['idcond']))  {
			$aConds[] = ' '.$this->getTab().'.'.$conf['idcond'].'=\''.$val.'\'';

		}
		if (isset($conf['logincond']))  {
			if($_SESSION["__ID"]!= '1')
				$aConds[] = ' '.$this->getTab().'.'.$conf['logincond'].'=\''.$_SESSION["__ID"].'\'';
		}
		if (isset($conf['logincond_tabla']))  {
			if($_SESSION["__ID"]!='1')
				$aConds[] = ' '.$conf['logincond_tabla'].'=\''.$_SESSION["__ID"].'\'';
		}
		if (isset($conf['pltCond'])){
			$pltCondiciones = explode("|",$conf['pltCond']);
			if(sizeof($pltCondiciones)>0){
				for($i=0;$i<sizeof($pltCondiciones);$i++){
					$aConds[] = ' '.$this->getTab().'.'.$pltCondiciones[$i];
				}
			}
		}
		if (isset($conf['idTabcond']))  {
			$aConds[] = ' '.$conf['idTabcond'].'=\''.$val.'\'';
		}
		if (isset($conf['fieldCondFather']) && isset($conf['fieldCondSon'])  && isset($conf['fatherTab']) &&  isset($conf['fatherId'])){
			$aConds[] = ' '.$this->getTab().'.'.$conf['fieldCondSon'].'= (select '.$conf['fieldCondFather'].' from '.$conf['fatherTab'].' where '.$conf['fatherId'].' = '.$val.' limit 1 ) ';
		}

		if($leftWhere = $this->getLeftWhere()){
			if(isset($leftWhere) && is_array($leftWhere) && sizeof($leftWhere)>0){
				$aConds[] = ' '.implode(" and ",$leftWhere)." ";
			}
		}

		if (sizeof($aConds)>0) return " where ".implode(' and ',$aConds);
		else return "";
	}

	public function getSQL($conf,$val,$limit = false,$pag = 1) {
		//if ($pl === false) $pl = &$this->plantillaPrincipal;
		$sql = "select SQL_CALC_FOUND_ROWS ";
		if($this->getDistinct()===true){
			$sql .= " distinct ";
		}
		$sql .= $this->getTab().".".$this->getID()." as __ID";
		for ($i=0;$i<$this->sizeofFields;$i++) {
			//if (in_array($this->fields[$i]->getIndex(),$nofields)) continue;
			$sql .= ",".$this->fields[$i]->getSQL($this->getTab());
			/*if ($this->fields[$i]->hasSubFields()) {
				for ($j=0;$j<$this->fields[$i]->sizeofsubFields;$j++) {
					$sql .= ",".$this->fields[$i]->subFields[$j]->getSQL($this->getTab());
				}
			}*/
		}
		$sql .= ' from '.$this->getTab();
		$leftTabs = $this->getALeftTabs();
		if(isset($leftTabs) && is_array($leftTabs) && !empty($leftTabs)){
			$leftTab = $leftTabs['lefttab'];
			$leftCond = $leftTabs['leftcond'];
			$leftWhere = $leftTabs['leftwhere'];
			if(sizeof($leftTab)>0 && sizeof($leftCond)>0 && sizeof($leftTab)==sizeof($leftCond)){
				for($i=0;$i<sizeof($leftTab);$i++){
					if($i!=0){
						$sql.=" ";
					}
					$sql .= " left join ".$leftTab[$i]." on(".$leftCond[$i].") ";
				}
			}
		}
		$sql .= $this->buildConds($conf,$val);

		if($this->getGroupBy()!==false){
			$sql .= " group by  ".$this->getGroupBy();
		}
		if ((isset($_GET['order'])) && (isset($this->fields[(int)$_GET['order']])) ) {
			$sql .= ' order by '.$this->fields[(int)$_GET['order']]->getIndex();
			if ((isset($_GET['orderType'])) && ($_GET['orderType']=="desc")) {
				$sql .= ' desc ';
			}
		}elseif($this->getOrderBy()!==false){
			$sql .= ' order by '.$this->getOrderBy().' ';
		}
		if(isset($limit) && $limit !==false){
			$sql .= ' limit '.$pag.",".$limit;
		}

		if (isset($_GET['DEBUG'])) iError::warn("<textarea>".$sql."</textarea>");
		return $sql;

	}


	public function getDefaultFLD($conf,$val) {
		//if ($pl === false) $pl = &$this->plantillaPrincipal;
		$sql = "select SQL_CALC_FOUND_ROWS ";
		if($this->getDistinct()===true){
			$sql .= " distinct ";
		}
		$sql .= $this->getTab().".".$conf['defaultFLD']." as defaultFLD ";
		for ($i=0;$i<$this->sizeofFields;$i++) {
			$sql .= ",".$this->fields[$i]->getSQL($this->getTab());
		}
		$sql .= ' from '.$this->getTab();
		$leftTabs = $this->getALeftTabs();
		if(isset($leftTabs) && is_array($leftTabs) && !empty($leftTabs)){
			$leftTab = $leftTabs['lefttab'];
			$leftCond = $leftTabs['leftcond'];
			$leftWhere = $leftTabs['leftwhere'];
			if(sizeof($leftTab)>0 && sizeof($leftCond)>0 && sizeof($leftTab)==sizeof($leftCond)){
				for($i=0;$i<sizeof($leftTab);$i++){
					if($i!=0){
						$sql.=" ";
					}
					$sql .= " left join ".$leftTab[$i]." on(".$leftCond[$i].") ";
				}
			}
		}

		$sql .= $this->buildConds($conf,$val);

		if ((isset($_GET['order'])) && (isset($this->fields[(int)$_GET['order']])) ) {
			$sql .= ' order by '.$this->fields[(int)$_GET['order']]->getIndex();
			if ((isset($_GET['orderType'])) && ($_GET['orderType']=="desc")) {
				$sql .= ' desc ';
			}
		}elseif($this->getOrderBy()!==false){
			$sql .= ' order by '.$this->getOrderBy().' ';
		}
		$sql .= ' limit 1 ';

		if (isset($_GET['DEBUG'])) iError::warn("<textarea>".$sql."</textarea>");
		return $sql;

	}
}


?>