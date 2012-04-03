<?php
/**
 * Fichero principal de la clase tablon,
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

//define("RUTA_PLANTILLAS",dirname(__FILE__)."/../../../configuracion/tablon/");


class dev extends contenidos {
	protected $currentSection = 0;
	protected $currentFather = false;
	protected $currentValue = false;
	protected $history = array();
	/**
	 * Objeto que contiene la consulta principal de tablón
	 *
	 * @var object objeto consulta a la base de datos.
	 */
	protected $conPrinc = false;
	protected $noShownFields = false;
	protected $noEditFields = false;
	protected $totalRows = 0;

	public $backLink;
	public $aJs = array(
		"../modules/dev/scripts/dev.js"
	);
	public $aCss = array("../modules/tablon/css/tablon.css");
	public $plantillaPrincipal = false;

	public $selectedConf = null;

	/**
	 * método constructor para tablón.
	 *
	 * @param array $conf datos provenientes del fichero de configuración
	 */
	function __construct(&$conf) {
		$this->conf = $conf;
		$this->fixOps();
		$this->setCurrentSection();


	}

	function fixOps() {
		foreach ($this->conf as $sec => $cont) {
			if ($sec === "main") continue;
			if (isset($cont['ops'])) {
				$aOps = explode(",",$cont['ops']);
				$this->conf[$sec]['ops'] = array();
				foreach ($aOps as $op) {
					if (isset($this->conf[$op])) $this->conf[$sec]['ops'][$op] = true;
				}

			}
		}
	}

	protected function setCurrentSection() {
		if ((isset($_GET['tSec'])) && (is_array($_GET['tSec']))) {
			foreach($_GET['tSec'] as $idx => $vlr) {
				list($sec,$op) = explode("::",$idx);
				if ( (isset($this->conf[$sec]['ops'][$op])) && (isset($this->conf[$op])) ) {
					$this->history[$sec] = array($op,$vlr);
					$this->currentSection = $op;
					$this->currentFather = $sec;
					$this->currentValue = $vlr;
				}
			}
		}

		$this->selectedConf = &$this->conf[$this->currentSection];

	}
	public function getCurrentClassName() {
		return (empty($this->selectedConf['class']))? false:$this->selectedConf['class'];
	}
	private function _drawTitle($c) {
		return '<p class="title">'.$c.'</p>';

	}

	protected function getActTitle() {
		global $kMenu;
		$link = "";
		foreach ($this->history as $sec => $cont) {
			$tit = '';
			$link .= '&amp;tSec['.$sec.'::'.$cont[0].']='.$cont[1];
			if (($miniplt = $this->_getPlt($sec))!==false) {

				$miniPlt = new tablon_plantilla($miniplt,true);
				$sql = 'select '.$miniPlt->getDefaultFLD().' from '.$miniPlt->getTab().' where ';
				$aIds = explode(",",$cont[1]);
				foreach($aIds as $idx=>$id) $aIds[$idx] = $miniPlt->getID().'=\''.tablon_FLD::cleanMysqlValue($id).'\'';
				$sql .= "(".implode(" or ",$aIds).")";
				$con = new con($sql);

				$aTits = array();
				while ($r = $con->getResult()) {
					$aTits[] = $r[$miniPlt->getDefaultFLD()];
				}

				$tit .= implode(", ",$aTits);

			}

			return $tit;
		}
		//$this->backLink = '?op='.$kMenu->selectedURL.$link;
		//return $this->_drawTitle($this->selectedConf['tit']);
	}

	protected function drawTitle() {
		global $kMenu;
		$link = "";
		foreach ($this->history as $sec => $cont) {
			$tit = '<a href="?op='.$kMenu->selectedURL.$link.'">'.$this->conf[$sec]['tit'].'</a> > ';
			$link .= '&amp;tSec['.$sec.'::'.$cont[0].']='.$cont[1];
			if (($miniplt = $this->_getPlt($sec))!==false) {

				$miniPlt = new tablon_plantilla($miniplt,true);
				$sql = 'select '.$miniPlt->getDefaultFLD().' from '.$miniPlt->getTab().' where ';
				$aIds = explode(",",$cont[1]);
				foreach($aIds as $idx=>$id) $aIds[$idx] = $miniPlt->getID().'=\''.tablon_FLD::cleanMysqlValue($id).'\'';
				$sql .= "(".implode(" or ",$aIds).")";
				$con = new con($sql);

				$aTits = array();
				while ($r = $con->getResult()) {
					$aTits[] = $r[$miniPlt->getDefaultFLD()];
				}

				$tit .= implode(", ",$aTits);

			}

			echo $this->_drawTitle($tit);
		}
		$this->backLink = '?op='.$kMenu->selectedURL.$link;
		echo $this->_drawTitle($this->selectedConf['tit']);
	}
	private function _getPlt($sec) {
		if (!file_exists(RUTA_PLANTILLAS.$this->conf[$sec]['plt'])) return false;
		return RUTA_PLANTILLAS.$this->conf[$sec]['plt'];
	}

	protected function getPlt() {
		return $this->_getPlt($this->currentSection);
	}

	private function hasOptions() {
		return (
		(isset($this->selectedConf['del'])) ||
		(isset($this->selectedConf['ops'])));
	}
	protected function isPlaceEditable() {
		return (isset($this->selectedConf['self_editable']));
	}

	protected function getHistoryURL($limit = 0) {
		$ret = '';
		if (sizeof($this->history)==0) return false;
		$limitHist = sizeof($this->history)-$limit;

		foreach ($this->history as $sec=>$aSec) {
			if ($limitHist==0) break;
			$limitHist--;
			$ret .= 'tSec['.$sec.'::'.$aSec[0].']='.$aSec[1].'&amp;';
		}
		return $ret;
	}


	private function drawOptions($id) {
		$ret = "";
		if ((isset($this->selectedConf['ops']))&&(sizeof($this->selectedConf['ops'])>0)) {
			foreach ($this->selectedConf['ops'] as $op=>$foo) {
				if (!isset($this->conf[$op])) {
					iError::warn("No existe la sección [".$op."] en el fichero de configuración.");
					continue;
				}
				$url = krm_menu::getURL();
				$url .= $this->getHistoryURL();
				$url .= 'tSec['.$this->currentSection.'::'.$op.']='.$id;
				$ret .= '<a href="'.$url.'" class="opts';
				if (isset($this->conf[$op]['markerClass'])) $ret .= ' '.$this->conf[$op]['markerClass'];
				$ret .= '" title="'.$this->conf[$op]['tit'].'" >';
				if (isset($this->conf[$op]['img'])) {
					$ret .= '<img src="./icons/'.$this->conf[$op]['img'].'" alt="'.$this->conf[$op]['tit'].'" />';
				}
				if (!isset($this->selectedConf['hide_label'])) $ret .= $this->conf[$op]['tit'];

				$ret .= '</a>';
			}
		}
		if (isset($this->selectedConf['del'])) {
			$ret .= '<img src="./icons/eraser.png" alt="Borrar" class="deleteRow" />';
		}

		return $ret;
	}

	protected function getPag($absolute = false) {
		if (isset($_GET['pag'])) {
			$cPage = (int)$_GET['pag'];
			if ($cPage == 0) return 0;
			if ($absolute) return ($cPage);
			return $this->selectedConf['limit']*($cPage-1);
		}

		return 0;
	}


	protected function buildSearchConds(&$aConds) {
		$aCondSearch = array();

		if (!isset($_SESSION['search'][$this->currentSection])) $_SESSION['search'][$this->currentSection] = array();
		if (!isset($_SESSION['search'][$this->currentSection][$this->currentFather])) $_SESSION['search'][$this->currentSection][$this->currentFather] = array();
		if (!isset($_SESSION['search'][$this->currentSection][$this->currentFather][$this->currentValue])) $_SESSION['search'][$this->currentSection][$this->currentFather][$this->currentValue] = array();

		$sess = &$_SESSION['search'][$this->currentSection][$this->currentFather][$this->currentValue];

		if ( (isset($_POST['submit'])) && ($_POST["submit"]== 'cancel') ) {
			$sess = array();
			return;
		}

		$pl = &$this->plantillaPrincipal;
		$searchFields = explode(",",$this->selectedConf['search']);

		for ($i=0;$i<$pl->getNumFields();$i++) {
			if (!in_array($pl->fields[$i]->getIndex(),$searchFields)) continue;

			$var = i::set($_POST[$pl->fields[$i]->getSQLFLD()],i::set($sess[$pl->fields[$i]->getSQLFLD()],""));

			if ($var!="") {
				$pl->fields[$i]->setSearchValue($var);
				$sess[$pl->fields[$i]->getSQLFLD()] = $pl->fields[$i]->getSearchValue();
				$aCondSearch[] = $pl->fields[$i]->getSQLFLDSearch().$pl->fields[$i]->getSearchOp();
			} else {
				if (isset($sess[$pl->fields[$i]->getSQLFLD()])) unset($sess[$pl->fields[$i]->getSQLFLD()]);
			}
		}

		if (sizeof($aCondSearch)==0) return false;
		$aConds[] = ' ('.implode(' and ',$aCondSearch).')';
	}

	protected function buildConds() {
		$aConds = array();
		$pl = &$this->plantillaPrincipal;
		if ($del = $pl->getDeletedFLD()) {
			$aConds[] = ' '.$pl->getTab().'.'.$del.'=\'0\'';
		}
		if (isset($this->selectedConf['idcond']))  {
			$aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['idcond'].'=\''.$this->currentValue.'\'';
		}
		if (isset($this->selectedConf['logincond']))  {
			$aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['logincond'].'=\''.$_SESSION["__ID"].'\'';
		}

		if (isset($this->selectedConf['hideifnotadmin'])&&($this->selectedConf['hideifnotadmin']!=$_SESSION["__ID"]))  {
			$aConds[] = ' '.$pl->getTab().'.'.$pl->getId().'!=\''.$this->selectedConf['hideifnotadmin'].'\'';
		}
		if (isset($this->selectedConf['hideifnotroladmin'])&&!in_array($this->selectedConf['hideifnotroladmin'],$_SESSION["__IDROL"]))  {
			$aConds[] = ' '.$pl->getTab().'.'.$pl->getId().'!=\''.$this->selectedConf['hideifnotroladmin'].'\'';
		}
		if (isset($this->selectedConf['logincond_tabla']))  {
			$aConds[] = ' '.$this->selectedConf['logincond_tabla'].'=\''.$_SESSION["__ID"].'\'';
		}

		if (isset($this->selectedConf['search'])) {
			$this->buildSearchConds($aConds);
		}

		if (isset($this->selectedConf['pltCond'])){
			$pltCondiciones = explode("|",$this->selectedConf['pltCond']);
			if(sizeof($pltCondiciones)>0){
				for($i=0;$i<sizeof($pltCondiciones);$i++){
					$aConds[] = ' '.$pl->getTab().'.'.$pltCondiciones[$i];
				}
			}
		}
		if (isset($this->selectedConf['idTabcond']))  {
			$aConds[] = ' '.$this->selectedConf['idTabcond'].'=\''.$this->currentValue.'\'';
		}
		if (isset($this->selectedConf['fieldCondFather']) && isset($this->selectedConf['fieldCondSon'])  && isset($this->selectedConf['fatherTab']) &&  isset($this->selectedConf['fatherId'])){
		 //	fieldCondFather = "iden_cola"
		 //fieldCondSon = "queue_name"
		 //$aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['titleCond'].'= \''.$this->getActTitle().'\'';
			$aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['fieldCondSon'].'= (select '.$this->selectedConf['fieldCondFather'].' from '.$this->selectedConf['fatherTab'].' where '.$this->selectedConf['fatherId'].' = '.$this->currentValue.' limit 1 ) ';
		}
		if (sizeof($aConds)>0) return " where ".implode(' and ',$aConds);
		else return "";
	}

	protected function getSQL(&$pl = false,$nofields = false) {

		if ($pl === false) $pl = &$this->plantillaPrincipal;
		$sql = "select SQL_CALC_FOUND_ROWS ";
		if($pl->getDistinct()===true){
			$sql .= " distinct ";
		}
		$sql .= $pl->getTab().".".$pl->getID()." as __ID";
		for ($i=0;$i<$pl->getNumFields();$i++) {
			if (in_array($pl->fields[$i]->getIndex(),$this->getNoShownFields())) continue;
			$sql .= ",".$pl->fields[$i]->getSQL($pl->getTab());
			if ($pl->fields[$i]->hasSubFields()) {
				for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
					$sql .= ",".$pl->fields[$i]->subFields[$j]->getSQL($pl->getTab());
				}
			}
		}
		$sql .= ' from '.$pl->getTab();
		$leftTabs = $pl->getLeftTabs();
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
		//$sql .= $this->buildConds();
		$condiciones = $this->buildConds();
		if(isset($leftWhere) && is_array($leftWhere) && sizeof($leftWhere)>0){
			if($condiciones == ""){
				$condiciones .= ' where '.implode(" and ",$leftWhere)." ";
			}else{
				$condiciones .= ' and '.implode(" and ",$leftWhere)." ";
			}
		}
		$sql .= $condiciones;
		if (isset($this->selectedConf['group'])	) {
				$sql .= ' group by '.$this->selectedConf['group'].' ';
		}
		if (($pl->getGroupBy()!==false) || (isset($this->selectedConf['group'])) ){
			if (trim($this->selectedConf['group'])!="" ) $gr = $this->selectedConf['group'];
			if (trim($pl->getGroupBy()!="") ) $gr = $pl->getGroupBy();
			$sql .= " group by  ".$gr;
		}
		if ((isset($_GET['order'])) && (isset($pl->fields[(int)$_GET['order']])) ) {
			$sql .= ' order by '.$pl->fields[(int)$_GET['order']]->getIndex();
			if ((isset($_GET['orderType'])) && ($_GET['orderType']=="desc")) {
				$sql .= ' desc ';
			}
		}elseif($pl->getOrderBy()){
			if (isset($this->selectedConf['order'])	) {
				$sql .= ' order by '.$this->selectedConf['order'].' ';
			}else{
				$sql .= ' order by '.$pl->getOrderBy().' ';
			}
		}

		if(isset($this->selectedConf['limit'])){
			$sql .= ' limit '.$this->getPag().",".$this->selectedConf['limit'];
		}

		if (isset($_GET['DEBUG'])) iError::warn("<textarea>".$sql."</textarea>");
		return $sql;

	}

	private function getCreateSQL() {
		$pl = &$this->plantillaPrincipal;
		$sql = "create table ".$pl->getTab()."(\n".$pl->getID()." mediumint unsigned not null auto_increment";

		for ($i=0;$i<$pl->getNumFields();$i++) {
			if ($pl->fields[$i]->getSQLType()!==false) { // comprobación para campos ghost
				$sql .= ",\n".$pl->fields[$i]->getSQL("",false)." ".$pl->fields[$i]->getSQLType();
				if ($pl->fields[$i]->hasSubFields()) {
					for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
						//TODO campos dependientes
 					}
				}
			}
		}
		if ($del = $pl->getDeletedFLD()) {
			$sql .= ",\n".$del." enum('0','1') not null default '0'";

		}
		$sql .= ",\nprimary key(".$pl->getID().")\n) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		return $sql;
	}

	private function doSQL() {
		$this->conPrinc = new con($this->getSQL());
		if ($this->conPrinc->error()) return false;
		$cCont = new con("SELECT FOUND_ROWS() as cont");
		$rCont = $cCont->getResult();
		$this->totalRows=$rCont['cont'];
		return true;
	}

	private function drawTableContents($checkBox = false,$csv = false) {
		$pl = &$this->plantillaPrincipal;
		while ($r = $this->conPrinc->getResult()) {
			echo '<tr';
			if (!$csv) echo ' id="'.$pl->getBaseFile().'::'.$r['__ID'].'" ';
			echo '>';
			if (($checkBox) && (!$csv)) {
				echo '<td class="multiselect" id="ms::'.$r['__ID'].'"><input type="checkbox" /></td>';
			}

			for ($i=0;$i<$pl->getNumFields();$i++) {
				if ($this->isNoShown($pl->fields[$i]->getIndex())) continue;

				echo '<td';
				if (!$csv) echo ' id="'.basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$r['__ID'].'" class="';

				if (($this->isPlaceEditable()) && (!$this->isNoEdit($pl->fields[$i]->getIndex())) && (!$csv)) {
					if ($pl->fields[$i]->getCl()&&(trim($pl->fields[$i]->getCl())!="")) echo $pl->fields[$i]->getCl();
					else echo 'editable  ';
					echo ' "';
					echo ' type="' . $pl->fields[$i]->getType().'">';
				} else {
					echo '">';
				}

				echo $pl->fields[$i]->drawTableValue($r[$pl->fields[$i]->getAlias()]);
				echo '</td>';
				for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
					if (in_array($pl->fields[$i]->subFields[$j]->getIndex(),$this->getNoShownFields())) continue;
					echo '<td';
					if (!$csv) echo ' id="'.basename($pl->getFile()).'::'.$pl->fields[$i]->subFields[$j]->getSQLFLD().'::'.$r['__ID'].'"';
					echo '>';
					echo $pl->fields[$i]->subFields[$j]->drawTableValue($r[$pl->fields[$i]->subFields[$j]->getAlias()]).'</td>';
				}
			}
			if (($this->hasOptions()) && (!$csv)) {
				echo '<td>'.$this->drawOptions($r['__ID']).'</td>';
			}
			echo '</tr>';
		}
		if (!$csv) $this->drawTRModel($checkBox);
	}

	private function drawTRModel($checkBox) {
		$pl = &$this->plantillaPrincipal;
		echo '<tr id="trModel" class="tablonClone" >';
		if ($checkBox) {
			echo '<td class="multiselect" id="ms::%id%"><input type="checkbox" /></td>';
		}

		for ($i=0;$i<$pl->getNumFields();$i++) {
			if (in_array($pl->fields[$i]->getIndex(),$this->getNoShownFields())) continue;
			echo '<td id="'.basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::%id%" class="';
			if ($this->isPlaceEditable()) {
				echo 'editable"';
				echo ' type="' . $pl->fields[$i]->getType().'">';
			} else {
				echo '">';
			}
			echo '</td>';
			for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
				if (in_array($pl->fields[$i]->subFields[$j]->getIndex(),$this->getNoShownFields())) continue;
				echo '<td id="'.basename($pl->getFile()).'::'.$pl->fields[$i]->subFields[$j]->getSQLFLD().'::%id%"></td>';
			}
		}
		if ($this->hasOptions()) {
			echo '<td>'.$this->drawOptions('%id%').'</td>';
		}
		echo '<tr/>';

	}

	private function drawSearcher() {
		echo '<form action="'.krm_menu::getURL().$this->getHistoryURL().'" method="post">';
		echo '<input type="hidden" name="do" value="search" />';
		echo '<table id="tablonsearch">';
		$pl = &$this->plantillaPrincipal;
		$searchFields = explode(",",$this->selectedConf['search']);
		for ($i=0;$i<$pl->getNumFields();$i++) {
				if (!in_array($pl->fields[$i]->getIndex(),$searchFields)) continue;
				echo '<tr><td class="cabecera">'.$pl->fields[$i]->getAlias().'</td>';
				$val = $pl->fields[$i]->getSearchValue();
				echo '<td';
				if ($val!="") echo ' class="used"';
				echo '>'.$pl->fields[$i]->drawTableValueEdit($val).'</td></tr>';
		}
		echo '<tr><td colspan="2" class="cabecera centrar"><p><button name="submit" value="search"><img src="./icons/viewmag.png" />&nbsp;Buscar</button><button name="submit" value="cancel"><img src="./icons/cancel.png" />&nbsp;Cancelar Filtrado</button></p><p id="__hide_filtrado"><img src="./icons/viewmag-.png" />&nbsp;Ocultar Filtrado</p></td></tr>';
		echo '</table>';
		echo '</form>';
	}

	public function getNoShownFields() {
		if ($this->noShownFields===false) {
			if (isset($this->selectedConf['noshow'])) $this->noShowFields = explode(",",$this->selectedConf['noshow']);
			else $this->noShowFields = array();
		}
		return $this->noShowFields;

	}

	public function isNoShown($idx) {
		return (in_array($idx,$this->getNoShownFields()));
	}

	public function getNoEditFields() {
		if ($this->noEditFields===false) {
			if (isset($this->selectedConf['noedit'])) $this->noEditFields = explode(",",$this->selectedConf['noedit']);
			else $this->noEditFields = array();
		}
		return $this->noEditFields;

	}

	public function isNoEdit($idx) {
		return (in_array($idx,$this->getNoEditFields()));
	}



	protected function drawErrors() {
		iError::error($this->conPrinc->getError());
		switch($this->conPrinc->getErrorNumber()) {
			case 1146:
				iError::warn("<textarea rows='7' cols='40' class='warn'>".$this->getCreateSQL()."</textarea>");
			break;
			case 1054:
				if (preg_match("/column '(.*)' in/",$this->conPrinc->getError(),$r)) {
					$pl = &$this->plantillaPrincipal;
					$warnAux = "";
					for ($i=0;$i<$pl->getNumFields();$i++) {
						if ($r[1] == $pl->fields[$i]->getIndex()) {
							$warnAux =  $pl->fields[$i]->getSQLType();
							break;
						}
					}
					$warnText = "alter table ".$this->plantillaPrincipal->getTab()." add ".$r[1]." ".$warnAux;
					iError::warn("<textarea rows='7' cols='40' class='warn'>".$warnText."</textarea>");
				}
			break;
		}
	}
	protected function drawBackLink() {
		return '<a href="'.krm_menu::getURL().$this->getHistoryURL(1).'" id="backButton"><img src="./icons/back.png" >Volver</a>';
	}

	private function is_tablon_edit($op) {
		return ((isset($this->conf[$op])) && (isset($this->conf[$op]['class'])) && ($this->conf[$op]['class']=="tablon_edit"));
	}

	private function selected_have_opts_tablon_edit() {
		if (!isset($this->selectedConf['ops'])) return false;
		foreach ($this->selectedConf['ops'] as $op=>$foo) {
			if ($this->is_tablon_edit($op)) return $op;
		}
		return false;
	}










	private function showtables(){
		$sql = "show tables ";
		$c = new con($sql);
		$aTables = array();
		while ($r = $c->getResult()) {
			 $a = array_values($r);
			 $aTables[] = $a[0];
		}
		return $aTables;
	}
	private function databaseTables(){
		$tables = $this->showtables();
		sort($tables);
		$res="<div><select id=\"tableSelect\">";
		$res.= "<option value=\"__null\" >Selecciona la tabla</option>";
		foreach ($tables as $k) {
			$res.= "<option value=\"".$k."\" >".$k."</option>";
		}
		$res.="</select></div>";
		return $res;
	}

	private function desctable($t){

		$sql = "desc ".$t;
		$c = new con($sql);
		$aDatos = array();
		while ($r = $c->getResult()) {
			 $aDatos[] = $r;
		}
		return $aDatos;
	}
	private function quepuedeser($a) {
		$r = '['.$a['Field'].']';
		//var_dump($a);
		$r.='
alias="'.$a['Field'].'"';


		switch(true){

			case($a['Type'] == "text"):
				$r.='
type="HTML"';

			break;
			/*case "enum":
				$r.='
type="ENUM"';
			break;*/
			default:
				$r.='
type="SAFETEXT"';
			break;
		}



		return $r;
	}

	public function detallesTabla($t){
		$tables = $this->showtables();
		if (!in_array($t,$tables)) return false;
		$ret = $this->desctable($t);
		$aFields = array();
		foreach($ret as $aInfo){ $aFields[]=$aInfo['Field']; }
		$r = "";
		$r.='<li>main <br /><textarea>
[::main]
tab="'.$t.'"
genero=""
entity=""
defaultFLD=""
id="'.$ret[0]['Field'].'"
deleted="'.((in_array('borrado',$aFields))? "borrado":"" ).'"
		</textarea></li>';
		foreach($ret as $aInfo){
			$r.="<li>".$aInfo['Field']." <br /><textarea>".$this->quepuedeser($aInfo)."</textarea></li>";
		//	var_dump($aInfo);
		}

		return $r;

	}

	public function draw() {

		echo $this->drawTitle();

		echo '<div id="tablonContainer">';

		echo $this->databaseTables();

		echo '</div>';
		echo '<br /><br /><div id="detalles"></div>';

		return;










		if (!$plantilla = $this->getPlt()) {
			iError::error("Plantilla no encontrada");
			return false;
		}

		$this->plantillaPrincipal = new tablon_plantilla($plantilla);
		$pl = &$this->plantillaPrincipal;
		$multiops = ( ($this->selected_have_opts_tablon_edit()) || (isset($this->selectedConf['del'])));
		$csv = ((isset($_GET['CSV'])) && ($_GET['CSV']=="1"));
		$generalops = ( (sizeof($this->history)>0) || (isset($this->selectedConf['new'])));
		/*filtrado¿?*/
		$sql = $this->doSQL();

		if (isset($this->selectedConf['search'])) {
			echo '<p id="__user_filtrado"><img src="./icons/viewmag.png" alt="utilizar filtrado" />Activar Filtrado de campos</p>';
			$this->drawSearcher();
		}
		/*filtrado¿?*/
		echo '<div id="tablonContainer">';

		if ($csv) {
				ob_end_clean();
			 	header("Content-type: application/vnd.ms-excel");
	         header("Content-Disposition:  filename=\"".i::clean($this->selectedConf['tit']).".xls\";");
		}
		echo '<table'.(($csv)? '>':' id="tablon" class="tablon"  genero="'.$pl->getGenero().'" entidad="'.$pl->getEntidad().'">');

		if (!$csv) echo $this->plantillaPrincipal->drawHead($this->hasOptions(),$this->getNoShownFields(),$multiops,krm_menu::getURL().$this->getHistoryURL());

		if (!$this->doSQL()) {
			$this->drawErrors();
		} else {

			$this->drawTableContents($multiops,$csv);

		}
		echo '</table>';

		if ($csv) exit();
		echo '<ul id="optsTablon"';
		if ($multiops) {
			echo ' class="multiOptsTablon">';
			if (($op = $this->selected_have_opts_tablon_edit())!==false) {
				$url = krm_menu::getURL();
				$url .= $this->getHistoryURL();
				$url .= 'tSec['.$this->currentSection.'::'.$op.']=%id%';
				echo  '<li><a href="'.$url.'" class="opts" title="'.$this->conf[$op]['tit'].'">';
				if (isset($this->conf[$op]['img'])) {
					echo '<img src="./icons/'.$this->conf[$op]['img'].'" alt="'.$this->conf[$op]['tit'].'" />';
				}
				echo $this->conf[$op]['tit'];
				echo '</a></li>';
			}
			if ((isset($this->selectedConf['del'])) && ((bool)$this->selectedConf['del'])) {
				echo '<li><img src="./icons/eraser.png" alt="Eliminar" id="multiDelete" objetoElim="'.$pl->getEntidad().'" />Eliminar '.$pl->getEntidad().'</li>';
			}
		} else echo '>';


		if ($generalops) {
			echo '<li class="sep">&nbsp;</li>';
			if (isset($this->selectedConf['new'])) {
				$aNoEdit = $this->getNoEditFields();
				if(!empty($aNoEdit)){
					$param3 = implode(",",$this->getNoEditFields());
				}
				else{
					$param3 = '0';
				}
				switch($this->selectedConf['new']) {
					case 'inline':
						echo '<li id="new::'.basename($pl->getFile()).'::'.$param3.'"><img src="./icons/apply.png" alt="Nuevo" id="newInline" />Nuev'.$pl->getGenero().' '.$pl->getEntidad().'</li>';
					break;
					case 'editPlt':
						$opcionEdit = $this->selectedConf['editPlt'];
						$pltEdit = basename($this->conf[$opcionEdit]['plt']);
						echo '<li id="new::'.$pltEdit.'::show::'.basename($pl->getFile()).'::'.$param3.'"><img src="./icons/apply.png" alt="Nuevo" id="newInlineEdit" />Nuev'.$pl->getGenero().' '.$pl->getEntidad().'</li>';
					break;
				}
			}
			if (sizeof($this->history)>0) {
				echo '<li>'.$this->drawBackLink().'</li>';
			}
			echo '</ul>';

		}

		if ( (isset($this->selectedConf['limit'])) && ($this->totalRows>$this->selectedConf['limit']) ) {
			$url = krm_menu::getURL().$this->getHistoryURL();
			if (isset($_GET['order'])&&$_GET['order']!="") $url.='order='.$_GET['order'].'&amp;';
			if (isset($_GET['orderType'])&&$_GET['orderType']!="") $url.='orderType='.$_GET['orderType'].'&amp;';

			$url.='pag=';
			echo '<ul class="paginado">';
			echo '<li><a href="'.$url.'1">&lt;&lt;</a></li>';
			echo '<li><a href="'.$url.(($this->getPag(true)<=1)? 1:($this->getPag(true)-1)).'">&lt;</a></li>';
			for($i=($this->getPag(true)-2);$i<($this->getPag(true)+3);$i++) {
				if ($i<1) continue;
				if ($i>ceil($this->totalRows/$this->selectedConf['limit'])) break;
				if ($i==$this->getPag(true)) {
					echo '<li><strong>'.$i.'</strong></li>';
					continue;
				}
				echo '<li><a href="'.$url.$i.'">'.$i.'</a></li>';


			}
			echo '<li><a href="'.$url.(($this->getPag(true)==ceil($this->totalRows/$this->selectedConf['limit']))? ceil($this->totalRows/$this->selectedConf['limit']):($this->getPag(true)+1)).'">&gt;</a></li>';
			echo '<li><a href="'.$url.ceil($this->totalRows/$this->selectedConf['limit']).'">&gt;&gt;</a></li>';
			echo '</ul>';
		}


		/*
			hidden fields for ajax new.
		*/
		echo '<form id="hiddenFields">';
		if (isset($this->selectedConf['idcond']))  {
			echo '<input type="hidden" name="'.$this->selectedConf['idcond'].'" value="'.$this->currentValue.'" />';
		}
		echo '</form>';

		echo '</div>';
	}

	static function generateCache($args,$public = false) {
	    $kRegistry = KarmaRegistry::getInstance();
        $literal = new k_literal($kRegistry->get('lang'));

        list($type,$plantilla,$campo,$valor) = tablon_AJAXjeditable::decodeId($args,$cmps = 4,"/");

		switch($type) {
			case "img":

				if ($public === false) {
					if (!file_exists($plantilla)) {
						umask(0000);
						if (!mkdir($plantilla, 0755)) die("Imposible crear directorio cache para la plantilla [".$plantilla."].");
					} else {
						if (!is_dir($plantilla)) die("Existe el elemento [".$plantilla."] pero es un fichero.");
						if (!is_writeable($plantilla)) die("El directorio [".$plantilla."] no es escribible.");
					}
					chdir($plantilla);
				}
				//var_dump(tablon_AJAXjeditable::setPlantillaPath($plantilla));
				$pl = new tablon_plantilla(tablon_AJAXjeditable::setPlantillaPath($plantilla));
				if (($idFLD = $pl->findField($campo)) === false) die("no se encuentra el campo");
				$nCampo = $pl->fields[$idFLD]->getSQLFLD();
				$nCampoNombreImagen = $pl->fields[$idFLD]->subFields[$pl->fields[$idFLD]->img_name]->getSQLFLD();
				//var_dump($pl->fields);
				$ret = $pl->getIdFromUnique($nCampoNombreImagen,$valor,array($pl->fields[$idFLD]->subFields[$pl->fields[$idFLD]->img_size]->getSQLFLD()));

				if ($ret===false) die($literal->l("No existe el fichero"));
				list($id,$size) = $ret;
				$file = "_".$nCampo."_".$id;
				$file_mime = $file.'_mime_type';
				$file_name = (isset($_GET['thumb']))? $file."_thumb":$file;
				if ($public !== false) {
					/* Es una llamada desde la zona pública, en $public vendrá el prefijo */
					$w = $public['w'];
					$h = $public['h'];
					$file_name = $file."_".$public['prefix'];
					$no_resize_if_smaller = false;
				} else {
					$no_resize_if_smaller = true;
					$w = $h = 70;
				}
				$ETag = md5($file_name.$size);
				if (!isset($_GET['nocache'])) i::checkETAG($ETag);

				if ((isset($_GET['nocache'])) || (!( (@file_exists($file)) && (@file_exists($file_mime)) && (@filesize($file)==$size) && (@file_exists($file_name))))) {

					$pl->loadSingleField($idFLD,$id,true);
					file_put_contents($file,$pl->fields[$idFLD]->getValue());
					if ($file_name!=$file) { //thumb

						$im = new i_image($file);
						if (!$im->setNewDim($w,$h,$no_resize_if_smaller)) {
							@unlink($file_name);
							symlink($file,$file_name);
						} else {
							@unlink($file_name);
							$im->prepare();
							$im->imResize($file_name);

						}
						unset($im);
					} else {
						@unlink($file_name."_thumb");
						/* BORRAR TODOS LOS FICHEROS DEL MISMO PATRON*/
					}
				 	@unlink($file_mime);
					$mime = i::mime_content_type($file);
					$mime_file_name = i::clean($mime);
					if (!file_exists($mime_file_name)) file_put_contents($mime_file_name,$mime);
					symlink($mime_file_name,$file_mime);

				}

				header('ETag: "'.$ETag.'"');
				header("Content-Length: ".filesize($file_name));
				header("Content-Type: ".file_get_contents($file_mime));
				header("Content-Disposition: inline; filename=\"{$valor}\";");
				readfile($file_name);
				exit();

			break;
			case "file":
			// TODO


			break;
		}




	}

	static function foo() {}

	protected function getHistory(){
		return $this->history;
	}
}


?>
