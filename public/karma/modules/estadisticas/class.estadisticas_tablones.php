<?php
/**
 * Conector a base de datos MySQL
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

include_once( dirname(__FILE__).'/flash/open-flash-chart.php' );
include_once(dirname(__FILE__).'/flash/open_flash_chart_object.php');

class estadisticas_tablones extends tablon{
	public $aJs = array(
		//"../modules/estadisticas/scripts/estadisticas.js"
	);

	function __construct(&$conf) {
		parent::__construct($conf);
		$this->aCss[] = "../modules/estadisticas/css/estadisticas.css";
		$this->rutaPlantillas = "".dirname(__FILE__)."/../../../configuracion/estadisticas/";
	}

	private function getCreateSQL($sql) {
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

	public function draw() {
		echo $this->drawTitle();
		echo "<br/><br/>";
		if (!$plantilla = $this->getPlt()) {
			iError::error("Plantilla no encontrada");
			return false;
		}

		$this->plantillaPrincipal = new tablon_plantilla($plantilla);
		$pl = &$this->plantillaPrincipal;
		$aPL = parse_ini_file($pl->getFile(),true);
		$historia = $this->getHistory();

		$aCampos = $pl->aFields;


		echo '<div id="tablonContainer">';
		//var_dump($aCampos);
		$sqlDebug = "";
		$sqlDebugTtl = "";
		foreach($aCampos as $idx=>$config){
			$sql = $config['sqlSec'];
			if(isset($config['sqlChangeCurrent'])){
				$sqlChangeCurrent = $config['sqlChangeCurrent'];
				if(preg_match("/{$sqlChangeCurrent}/",$sql)){
					$valChange = $this->currentValue;
					$sql = preg_replace("/{$sqlChangeCurrent}/",$valChange,$sql);
					$sqlDebug .= $sql."\n";
					if(isset($config['sqlTtlCols']) && !empty($config['sqlTtlCols'])){
						$config['sqlTtlCols'] = $sql = preg_replace("/{$sqlChangeCurrent}/",$valChange,$config['sqlTtlCols']);
					}
					$sqlDebugTtl .= $config['sqlTtlCols']."\n";
				}
			}
			$j=1;

			if(isset($config['sqlChangeHistory'])){
				$sqlChangeHistory= $config['sqlChangeHistory'];
				foreach($historia as $hid=>$hvlr){
					if($historia[$hid][0]==$this->currentFather){
						$valorHist = $hid;
					}
				}
				$valChangeHistory = $historia[$valorHist][1];
				$sql = preg_replace("/{$sqlChangeHistory}/",$valChangeHistory,$sql);
				$sqlDebug .= $sql."\n";
				if(isset($config['sqlTtlCols']) && !empty($config['sqlTtlCols'])){
					$config['sqlTtlCols'] = $sql = preg_replace("/{$sqlChangeHistory}/",$valChangeHistory,$config['sqlTtlCols']);
				}
				$sqlDebugTtl .= $config['sqlTtlCols']."\n";
			}
			if(empty($sqlDebug)){
				$sqlDebug = $sql."\n";
			}
			$this->conPrinc = new con($sql);
			if($this->conPrinc->error()){
				$this->drawErrors();
			}else{
				$con = $this->conPrinc;
				echo "<h3>".$config['titSec']."</h3>";
				if($con->getNumRows()>0){
					echo '<table id="tablon" class="tablon" >';
					if($config['tipoTabla'] == 'dobleIndiceSQL'){
						$aTabla = array();
						$aCols = array();
						$aFilas = array();
						$iCol = $config['indicesColumnas'];
						$iFil = $config['indicesFilas'];
						$datos = $config['datoTabla'];
						while($r = $con->getResult()){
							$aCols[$r[$iCol]] = $r[$iCol];
							$aFilas[$r[$iFil]] = $r[$iFil];
							$aTabla[$r[$iFil]][$r[$iCol]] = $r[$datos];
						}
						echo "<tr><th></th>";
						foreach($aCols as $i=>$v){
							echo "<th>".$v."</th>";

						}
						if(isset($config['ttlFilas']) && $config['ttlFilas']==1){
							echo "<th>Total</th>";
						}
						echo "</tr>";
						$ttlCols = array();
						//$cont = 0;
						foreach($aFilas as $i=>$v){
							//$ttlCols[$i] = 0;
							$ttlFila = 0;
							echo "<tr>";
							echo "<td>".$v."</td>";
							foreach($aCols as $i2=>$v2){
								//echo "<th>".$v."</th>";
								if(isset($aTabla[$v][$v2])){
									if(!isset($ttlCols[$i2])){
										$ttlCols[$i2] = 0;
									}
									$ttlCols[$i2] += $aTabla[$v][$v2];
									$ttlFila += $aTabla[$v][$v2];
									echo "<td>".$aTabla[$v][$v2]."</td>";
								}else{
									echo "<td>0</td>";
								}
							}
							if(isset($config['ttlFilas']) && $config['ttlFilas']==1){
								echo "<td>".$ttlFila."</td>";
							}
							//$cont++;
							echo "</tr>";
						}
						if(isset($config['ttlCols']) && $config['ttlCols']==1){
							echo "<tr><td>Total</td>";
							$vlrTtl = 0;
							$sqlTtl = $config['sqlTtlCols'];
							$conTtl = new con($sqlTtl);
							$aTtl = array();
							while($r = $conTtl->getResult()){
								//$aCols[$r[$iCol]] = $r[$iCol];
								//$aFilas[$r[$iFil]] = $r[$iFil];
								$aTtl[$r[$iCol]] = $r[$datos];
							}
							foreach($aCols as $i=>$v){
								echo "<td>".$aTtl[$i]."</td>";
								$vlrTtl += $aTtl[$i];
							}
							if(isset($config['ttlFilas']) && $config['ttlFilas']==1){
								echo "<td>".$vlrTtl."</td>";
							}
							echo "</tr>";
						}
					}elseif($config['tipoTabla'] == 'simpleIndiceSQL'){
						$iCol = $config['indicesColumnas'];
						$iFil = $config['indicesFilas'];
						$datos = explode("|",$config['datoTabla']);
						$aTabla = array();
						$aCols = explode("|",$iCol);
						$aFilas = array();
						while($r = $con->getResult()){
							//$aCols[$iCol] = $iCol;
							$aFilas[$r[$iFil]] = $r[$iFil];
							foreach($datos as $id=>$vlr){
								$aTabla[$r[$iFil]][$aCols[$id]] = $r[$vlr];
							}
							//$aTabla[$r[$iFil]][$r[$iCol]] = $r[$datos];
						}
						echo "<tr><th></th>";
						foreach($aCols as $i=>$v){
							echo "<th>".$v."</th>";
						}
						if(isset($config['ttlFilas']) && $config['ttlFilas']==1){
							echo "<th>Total</th>";
						}
						echo "</tr>";
						$ttlCols = array();
						foreach($aFilas as $i=>$v){
							$ttlFila = 0;
							echo "<tr>";
							echo "<td>".$v."</td>";
							foreach($aCols as $i2=>$v2){
								//echo "<th>".$v."</th>";
								if(isset($aTabla[$v][$v2])){
									if(!isset($ttlCols[$i2])){
										$ttlCols[$i2] = 0;
									}
									$ttlCols[$i2] += $aTabla[$v][$v2];
									$ttlFila += $aTabla[$v][$v2];

									echo "<td>".$aTabla[$v][$v2]."</td>";
								}else{
									echo "<td>0</td>";
								}
							}
							if(isset($config['ttlFilas']) && $config['ttlFilas']==1){
								echo "<td>".$ttlFila."</td>";
							}
							echo "</tr>";
						}
						if(isset($config['ttlCols']) && $config['ttlCols']==1){
							echo "<tr><td>Total</td>";
							$vlrTtl = 0;
							$sqlTtl = $config['sqlTtlCols'];
							$conTtl = new con($sqlTtl);
							$aTtl = array();
							while($r = $conTtl->getResult()){
								//$aCols[$iCol] = $iCol;
								//$aFilas[$r[$iFil]] = $r[$iFil];
								foreach($datos as $id=>$vlr){
									$aTtl[$aCols[$id]] = $r[$vlr];
								}
								//$aTabla[$r[$iFil]][$r[$iCol]] = $r[$datos];
							}
							foreach($aCols as $i=>$v){
								echo "<td>".$aTtl[$v]."</td>";
								$vlrTtl += $aTtl[$v];
							}
							if(isset($config['ttlFilas']) && $config['ttlFilas']==1){
								echo "<td>".$vlrTtl."</td>";
							}
							echo "</tr>";
						}
					}
					echo '</table>';
				}else{
					echo "No hay datos";
				}
			}
		}

		echo '</div>';

		echo '<ul id="optsTablon"><li class="sep">&nbsp;</li>';
		if (sizeof($this->history)>0) {
			echo '<li>'.$this->drawBackLink().'</li>';
		}
		echo '</ul>';
		if (isset($_GET['DEBUG'])){
			$debuggeo = "<textarea>".$sqlDebug."</textarea>";
			if(!empty($sqlDebugTtl)){
				$debuggeo .= "<textarea>".$sqlDebugTtl."</textarea>";
			}
			iError::warn($debuggeo);
		}
	}
}


