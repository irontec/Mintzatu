<?php
/**
 * Fichero principal de la clase tablon,
 *
 *
 * @author Eider Bilbao <eider@irontec.com>
 * @version 1.0
 * @package karma
 */


define("RUTA_PLTS",dirname(__FILE__)."/../../../configuracion/estadisticas/");
include_once( dirname(__FILE__).'/flash/open-flash-chart.php' );
include_once(dirname(__FILE__).'/flash/open_flash_chart_object.php');


class estadisticas_tablon extends tablon {
	public $aPlantillas = array();
	public $currentPlantilla = 0;
	private $aColores = array('#0d89be','#dc33c3c','#caca6e','#a9d1b5','#b5b59b','#007f00','#c77fe1',
		'#ffa700','#7f007f','#827f00','#ff4600','#00e97f', '#0000ff','#a8a8a8', '#ff0000','#cccccc');
	/**
	 * método constructor para tablón.
	 *
	 * @param array $conf datos provenientes del fichero de configuración
	 */
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->aCss[] = "../modules/estadisticas/css/estadisticas.css";
		$this->rutaPlantillas = "".dirname(__FILE__)."/../../../configuracion/estadisticas/";
		$this->aPlantillas = explode("|",$this->conf[$this->currentSection]['plt']);
	}

	private function _drawTitle($c) {
		return '<p class="title">'.$c.'</p>';
	}
	
	public function getAPlantillas(){
		return $this->aPlantillas;
	}
	
	public function getCurrentPlantilla(){
		return $this->currentPlantilla;
	}
	
	public function setCurrentPlantilla($plt){
		return $this->currentPlantilla = $plt;
	}

	protected function getActTitle() {
		global $kMenu;
		$link = "";
		foreach ($this->history as $sec => $cont) {
			$tit = '';
			$link .= '&amp;tSec['.$sec.'::'.$cont[0].']='.$cont[1];
			if (($miniplt = $this->_getPlt($this->currentPlantilla))!==false) {
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
	}

	protected function drawTitle() {
		global $kMenu;
		$link = "";
		foreach ($this->history as $sec => $cont) {
			$tit = '<a href="?op='.$kMenu->selectedURL.$link.'">'.$this->conf[$sec]['tit'].'</a> > ';
			$link .= '&amp;tSec['.$sec.'::'.$cont[0].']='.$cont[1];
			if (($con = $this->_getPltSec($sec,$cont[1]))!==false) {
				$aTits = array();
				while ($r = $con->getResult()) {
					$aTits[] = $r['defaultFLD'];
				}

				$tit .= implode(", ",$aTits);

			}
				
			echo $this->_drawTitle($tit);
		}
		$this->backLink = '?op='.$kMenu->selectedURL.$link;
		echo $this->_drawTitle($this->selectedConf['tit']);
	}
		
	private function _getPltSec($sec,$cont) {
		if($this->conf[$sec]['class']=='tablon'){
			if (!file_exists(dirname(__FILE__)."/../../../configuracion/tablon/".$this->conf[$sec]['plt'])) return false;
			$miniplt = dirname(__FILE__)."/../../../configuracion/tablon/".$this->conf[$sec]['plt'];
			$miniPlt = new tablon_plantilla($miniplt,true);
			$sql = 'select '.$miniPlt->getDefaultFLD().' as defaultFLD from '.$miniPlt->getTab().' where ';
			$aIds = explode(",",$cont);
			foreach($aIds as $idx=>$id) $aIds[$idx] = $miniPlt->getID().'=\''.tablon_FLD::cleanMysqlValue($id).'\'';
			$sql .= "(".implode(" or ",$aIds).")";
			$con = new con($sql);
		}elseif($this->conf[$sec]['class']=='estadisticas_tablon'){
			$thePlt =  $this->_getPlt($this->currentPlantilla);
			if($thePlt === false) return false;
			$miniPlt = new estadisticas_plantilla($thePlt,true);
			$con = new con($miniPlt->getDefaultFLD($this->conf[$sec]),$cont);
		}
		return $con;
	}
	
	private function _getPlt($sec) {
		if(isset($this->aPlantillas) && !empty($this->aPlantillas) && is_array($this->aPlantillas)){
			if (!file_exists($this->rutaPlantillas.$this->aPlantillas[$sec])) return false;
			else return $this->rutaPlantillas.$this->aPlantillas[$sec];
		}else{
			return false;
		}
	}

	public function getPlt() {
		return $this->_getPlt($this->currentPlantilla);
	}

	private function hasOptions() {
		return false;
	}
	protected function isPlaceEditable() {
		return false;
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
			if($_SESSION["__ID"]!= '1')
			$aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['logincond'].'=\''.$_SESSION["__ID"].'\'';
		}
		if (isset($this->selectedConf['logincond_tabla']))  {
			if($_SESSION["__ID"]!='1')
			$aConds[] = ' '.$this->selectedConf['logincond_tabla'].'=\''.$_SESSION["__ID"].'\'';
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
			$aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['fieldCondSon'].'= (select '.$this->selectedConf['fieldCondFather'].' from '.$this->selectedConf['fatherTab'].' where '.$this->selectedConf['fatherId'].' = '.$this->currentValue.' limit 1 ) ';
		}
		if (sizeof($aConds)>0) return " where ".implode(' and ',$aConds);
		else return "";
	}

	public function doSQL() {
		$pl = &$this->plantillaPrincipal;
		if(isset($this->selectedConf['limit'])){
			$limit = $this->selectedConf['limit'];
			$pag = $this->getPag();
			$this->conPrinc = new con($pl->getSQL($this->conf[$this->currentSection],$this->currentValue,$limit,$pag));
		}else{
			$this->conPrinc = new con($pl->getSQL($this->conf[$this->currentSection],$this->currentValue));
		}
		if ($this->conPrinc->error()) return false;
		$cCont = new con("SELECT FOUND_ROWS() as cont");
		$rCont = $cCont->getResult();
		$this->totalRows=$rCont['cont'];
		return true;
	}

	public function formatTable(){
		$pl = &$this->plantillaPrincipal;
		$iCol = explode("|",$pl->getIndiceCols());
		$iFil = explode("|",$pl->getIndiceFilas());
		$datos = explode("|",$pl->getDatoTabla());
		$aTabla = array();
		$aFilas = array();
		$aCols = array();
		switch ($pl->getTipoTabla()){
			case 'dobleIndiceSQL':
				$iCol = $iCol[0];
				$iFil = $iFil[0];
				$datos = $datos[0];
				$aTtlFila = array();
				while ($r = $this->conPrinc->getResult()) {
					$aCols[$r[$iCol]] = $r[$iCol];
					$aFilas[$r[$iFil]] = $r[$iFil];
					/*
					 * if($pl->fieldsAlias[$iCol]->getTransform()!==false){
						$trans = trim($r[$datos])."".$pl->fieldsAlias[$iCol]->getTransform();
						eval("\$r[$datos] = $trans;");
					}*/
					$aTabla[$r[$iFil]][$r[$iCol]] = $r[$datos];
					if(!isset($aTtlFila[$r[$iFil]]['F_totales'])){
						$aTtlFila[$r[$iFil]]['F_totales'] = 0;
					}
					if(!isset($aTabla['C_totales'][$r[$iCol]])){
						$aTabla['C_totales'][$r[$iCol]] = 0;
					}
					$aTtlFila[$r[$iFil]]['F_totales'] += $aTabla[$r[$iFil]][$r[$iCol]];
					$aTabla['C_totales'][$r[$iCol]] += $aTabla[$r[$iFil]][$r[$iCol]];
				}
				$ttlTtls = 0;
				foreach($aTtlFila as $idT=>$vlrT){
					$aTabla[$idT]['F_totales'] = $aTtlFila[$idT]['F_totales'];
					$ttlTtls += $aTtlFila[$idT]['F_totales'];
				}
				$aTabla['C_totales']['F_totales'] = $ttlTtls;
				//$this->doSQLTtl();
				/*while ($r = $this->conPrinc->getResult()) {
					$aTabla['C_totales'][$r[$iCol]] += $aTabla[$r[$iFil]][$r[$iCol]];
				}*/
				break;
			case 'filasIndiceSQL':
				$iFil = $iFil[0];
				$aCols = $iCol;
				$contF = 1;
				while ($r = $this->conPrinc->getResult()) {
					$aFilas[$r[$iFil]] = $r[$iFil];
					if(!isset($aTabla[$r[$iFil]]['F_totales'])){
						$aTabla[$r[$iFil]]['F_totales'] = 0;
					}
					foreach($aCols as $id=>$vlr){
						if(!isset($aTabla['C_totales'][$iCol[$id]])){
							$aTabla['C_totales'][$iCol[$id]] = 0;
						}
						if(!isset($aTtlFila[$r[$iFil]]['F_totales'])){
							$aTtlFila[$r[$iFil]]['F_totales'] = 0;
						}
						if($pl->fieldsAlias[$vlr]->getTransform()!==false){
							$trans = trim($r[$vlr])."".$pl->fieldsAlias[$vlr]->getTransform();
							eval("\$trans = $trans;");
						}else{
							$trans = $r[$vlr];
						}
						$aTabla[$r[$iFil]][$iCol[$id]] = round($trans,2);

						//$aTabla[$r[$iFil]]['F_totales'] += $aTabla[$r[$iFil]][$iCol[$id]];
						$aTtlFila[$r[$iFil]]['F_totales'] += $aTabla[$r[$iFil]][$iCol[$id]];
						$aTabla['C_totales'][$iCol[$id]] += $aTabla[$r[$iFil]][$iCol[$id]];
					}
					$contF++;
				}
				$ttlTtls = 0;
				foreach($aTtlFila as $idT=>$vlrT){
					$aTabla[$idT]['F_totales'] = $aTtlFila[$idT]['F_totales'];
					$ttlTtls += $aTtlFila[$idT]['F_totales'];
				}
				$aTabla['C_totales']['F_totales'] = $ttlTtls;
				break;
			/*case 'columnsaIndiceSQL':
				//$iFil = $iFil[0];
				$iCol = $iCol[0];
				$aFilas = $iFil;
				while ($r = $this->conPrinc->getResult()) {
					$aCols[$r[$iCol]] = $r[$iCol];
					if(!isset($aTabla['C_totales'][$r[$iCol]])){
						$aTabla['C_totales'][$r[$iCol]] = 0;
					}
					foreach($datos as $id=>$vlr){
						if(!isset($aTabla[$iFil]]['F_totales'])){
							$aTabla[$r[$iFil[$id]]]['F_totales'] = 0;
						}
						$aTabla[$r[$iFil[$id]][$iCol]] = $r[$vlr];

						$aTabla[$r[$iFil[$id]]]['F_totales'] += $aTabla[$iFil[$id]][$r[$iCol]];
						$aTabla['C_totales'][$r[$iCol]] += $aTabla[$iFil[$id]][$r[$iCol]];
					}
				}
				$ttlTtls = 0;
				foreach($aTtlFila as $idT=>$vlrT){
					$aTabla[$idT]['F_totales'] = $aTtlFila[$idT]['F_totales'];
					$ttlTtls += $aTtlFila[$idT]['F_totales'];
				}
				$aTabla['C_totales']['F_totales'] = $ttlTtls;
				break;*/
		}
		return array("filas"=>$aFilas,"cols"=>$aCols,"datos"=>$aTabla);
	}
	
	private function drawQuesitos($aDatos,$aTitulos,$tituloGnral = ""){
		if(empty($tituloGnral)){
			$titulo = "Estadisticas";
		}else{
			$titulo = $tituloGnral;
		}
		$num1 = 0;
		$num2 = 100;
		$num3 = 200;
		$data = array();
		$losColores = array();
		$contCol = 0;
		$limitCol = sizeof($this->aColores);
		for($i=0;$i<sizeof($aDatos);$i++){
			$losColores[] = $this->aColores[$contCol];
			$contCol++;
			if($contCol==$limitCol){
				$contCol = 0;
			}
			$num1 += 50;
			if($num1>255){
				$num1 = $num1 - 100;
			}
			$num2 += 50;
			if($num2>255){
				$num2 = $num2 - 100;
			}
			$num3 = $num3 - 70;
			if($num3<0){
				$num3 += 100;
			}
			$data[] = $aDatos[$i];
			//$aColores[] = "#".dechex($num1).dechex($num2).dechex($num3);
			
			if(strlen($aTitulos[$i])>30){
				$lenStr = strlen($aTitulos[$i]);
				$limite = ceil($lenStr/30);
				$dsdDnd = 0;
				for($j=0;$j<$limite;$j++){
					$dsdDnd += 30;
					if($dsdDnd<$lenStr){
						$aTitulos[$i] = substr($aTitulos[$i],0,$dsdDnd)."<br>".substr($aTitulos[$i],$dsdDnd);
						$dsdDnd +=4;
					}
				}	
			}
			$num1 = $num3;
			$num2 = $num1;
			$num3 = $num2;
		}
		$g = new graph();
		//$g->pie(60,"#505050","{font-size: 10px; color: #404040;");
		$g->pie(60,"#505050","{display:none}");
		
		$g->pie_values( $data, $aTitulos);
		//$g->pie_slice_colours( array("#d01f3c","#356aa0","#C79810", "#E496AB") );
		$g->pie_slice_colours($losColores);
		$g->title( $titulo, "{font-size:18px; color: #d01f3c}" );
		return $g->render();
		
	}
	
	public function drawTableContents($aTabla,$csv = false) {
		$pl = &$this->plantillaPrincipal;
		//$aTabla = $this->formatTable();
		$aCols = $aTabla['cols'];
		$aFilas = $aTabla['filas'];
		$aDatos = $aTabla['datos'];
		$ret = "";
		$url = krm_menu::getURL().$this->getHistoryURL();
		if(!$csv){
			$ret .= '<tr>';
			$ret .= '<th>&nbsp;</th>';
			foreach($aCols as $idC => $vlrC) {
				$ret .= '<th>'.$vlrC.'</th>';
			}
			if($pl->getTtlCols()==1){
				$ret .= '<th>Total</th>';
			}
			$ret .= '</tr>';
		}
		foreach($aFilas as $idF => $vlrF){
			$ret  .= '<tr>';
			$ret .=  '<td>'.$vlrF.'</td>';
			foreach($aDatos[$idF] as $idD => $vlrD){
				if($idD == 'F_totales' && $pl->getTtlCols()==1){
					$ret .=  "<td>".$vlrD."</td>";
				}elseif($idD != 'F_totales'){
					$ret .=  "<td>".$vlrD."</td>";
				}
			}
			$ret .= '</tr>';
		}
		if($pl->getTtlFilas()==1){
			$ret .= '<tr>';
			$ret .=  '<td>Total</td>';
			$idField = 0;
			if($pl->getTipoTabla()=="filasIndiceSQL"){
				foreach($pl->aFields as $real => $conf){
					if(isset($conf['transfTtl']) && !empty($conf['transfTtl'])){
						$transf = $conf['transfTtl'];
					}else{
						$transf = false;
					}
					$idC = $conf['alias'];
					if(isset($aDatos['C_totales'][$idC])){
						$ret .= '<td>';
						switch($transf){
							case 'avg':
								$ret .= ($aDatos['C_totales'][$idC]/$this->totalRows);
								break;
							case 'round-avg':
								$ret .= round(($aDatos['C_totales'][$idC]/$this->totalRows),2);
								break;
							case 'truncate-avg':
								$ret .= round(($aDatos['C_totales'][$idC]/$this->totalRows),2);
								break;
							default:
								$ret .= $aDatos['C_totales'][$idC];
								break;
						}
						$ret .= '</td>';
					}
				}
			}elseif($pl->getTipoTabla()=="dobleIndiceSQL"){
				foreach($aDatos['C_totales'] as $real => $conf){
					$ret .= '<td>'.$conf.'</td>';
				}
			}
			$ret .= '</tr>';
		}
		echo $ret;
	}
	
	
	public function setPlantillaPrincipal($plantilla){
		$this->plantillaPrincipal = new estadisticas_plantilla($plantilla);
	}
	
	public function drawErrorsP() {
		$this->drawErrors();
	}
	
	public function getTotalRows() {
		return $this->totalRows;
	}

	public function draw() {
		echo $this->drawTitle();
		for($x=0;$x<sizeof($this->aPlantillas);$x++){
			//$this->plantillaPrincipal = $this->aPlantillas[$x];
			
			$this->currentPlantilla = $x;
			if (!$plantilla = $this->getPlt()) {
				iError::error("Plantilla no encontrada");
				return false;
			}
			$this->plantillaPrincipal = new estadisticas_plantilla($plantilla);
			$pl = &$this->plantillaPrincipal;
			//$multiops = ( ($this->selected_have_opts_tablon_edit()) || (isset($this->selectedConf['del'])));
			$multiops = false;
			$csv = ((isset($_GET['CSV'])) && ($_GET['CSV']=="1"));
			//$generalops = ( (sizeof($this->history)>0) || (isset($this->selectedConf['new'])));
			$generalops = false;
			echo '<div id="tablonContainer">';

			if ($csv) {
				ob_end_clean();
				header("Content-type: application/vnd.ms-excel");
				header("Content-Disposition:  filename=\"".i::clean($this->selectedConf['tit']).".xls\";");
			}
			if (!$this->doSQL()) {
				$this->drawErrors();
			} else {
				echo "<h3>".$pl->getEntidad()."</h3>";
				if($this->totalRows>0){
					$aTabla = $this->formatTable();
					echo '<table id="tablon" class="tablon" >';
					$this->drawTableContents($aTabla,$csv);
					echo '</table>';
					if($pl->getGraph()==1 && $pl->getGraphVal()){
						$valGrafico = $pl->getGraphVal();
						$aCols = $aTabla['cols'];
						$aFilas = $aTabla['filas'];
						$aDatos = $aTabla['datos'];
						$graphVals = array();
						$cont = 1;
						$aDatosQ= array();
						$aTitulosQ = array();
						foreach($aFilas as $idF => $vlrF){
							$aDatosQ[] = $aDatos[$idF][$valGrafico];
							$aTitulosQ[] = $vlrF;
							$cont++;
						}
						if(!isset($miUrl)){
							$miUrl = array();
						}
						$miUrl[] = $this->drawQuesitos($aDatosQ,$aTitulosQ,$pl->getEntidad());
					}
				}
				else echo "<p><em>No hay datos</em></p>";
			}
			if ($csv) exit();
			
				
			if ( (isset($this->selectedConf['limit'])) && ($this->totalRows>$this->selectedConf['limit']) ) {
				$url = krm_menu::getURL().$this->getHistoryURL();
				if (isset($_GET['order'])&&$_GET['order']!="") $url.='order='.$_GET['order'].'&amp;';
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
			if (sizeof($this->history)>0) {
				echo '<ul id="optsTablon"'; 
				echo '<li>'.$this->drawBackLink().'</li>';
				echo '</ul>';					
			}
				
			echo '</div>';
		}
		
		if(isset($miUrl) && sizeof($miUrl)>0){
			echo "<div align='center'>";
			foreach($miUrl as $idU => $vlrU){
				//$miUrl[] = $this->drawQuesitos($aDatosQ,$aTitulosQ,$pl->getEntidad());
				echo '<embed class = "embebido" src="./modules/estadisticas/flash/open-flash-chart.swf?variables=true'.$vlrU.'quality="high" bgcolor="#FFFFFF" width="300px" height="300px" name="grafico" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"/>';
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			echo "</div>";
		}
	}
	static function foo() {}

}


?>
