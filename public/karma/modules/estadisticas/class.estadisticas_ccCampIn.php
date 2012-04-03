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

class estadisticas_ccCampIn extends tablon{
	public $aJs = array(
		//"../modules/estadisticas/scripts/estadisticas.js"
	);

	function __construct(&$conf) {
		parent::__construct($conf);
		$this->aCss[] = "../modules/estadisticas/css/estadisticas.css";
	}

	private function drawBarras($aDatos,$aTitulos,$tituloGnral = ""){
		if(empty($tituloGnral)){
			$titulo = "Estadisticas";
		}else{
			$titulo = $tituloGnral;
		}
		$bar_blue = new bar_3d( 75, "#3334AD");
		$max = 0;
		for($i=0;$i<sizeof($aDatos);$i++){
			$bar_blue->data[] = $aDatos[$i];
			if($aDatos[$i]>$max){ $max = $aDatos[$i];}
		}
		$intervalo = ceil($max/50);
		$escala = 50*$intervalo;
		$g = new graph();
		$g->title( $titulo, "{font-size:20px; color: #FFFFFF; margin: 5px; background-color: #505050; padding:5px; padding-left: 20px; padding-right: 20px;}");

		$g->data_sets[] = $bar_blue;

		$g->set_x_axis_3d( 12 );
		$g->x_axis_colour( "#909090", "#ADB5C7");
		$g->y_axis_colour( "#909090", "#ADB5C7");

		$g->set_x_labels($aTitulos);
		$g->set_y_max( $escala );
		$g->y_label_steps( $intervalo );

		return $g->render();

	}

	private function drawQuesitos($aDatos,$aTitulos,$tituloGnral = ""){
		if(empty($tituloGnral)){
			$titulo = "Estadisticas";
		}else{
			$titulo = $tituloGnral;
		}
		$data = array();
		for($i=0;$i<sizeof($aDatos);$i++){
			$data[] = $aDatos[$i];

		}
		$g = new graph();
		$g->pie(60,"#505050","{font-size: 10px; color: #404040;");

		$g->pie_values( $data, $aTitulos);
		$g->pie_slice_colours( array("#d01f3c","#356aa0","#C79810", "#E496AB") );
		$g->title( $titulo, "{font-size:18px; color: #d01f3c}" );
		return $g->render();

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
		$aNomGraficos = array();
		if(isset($aPL['::main']['nomGrafico']) && !empty($aPL['::main']['nomGrafico'])&& isset($aPL['::main']['tipoGrafico']) && !empty($aPL['::main']['tipoGrafico'])){
			$aGraficosN = explode("|",$aPL['::main']['nomGrafico']);
			$aGraficosT = explode("|",$aPL['::main']['tipoGrafico']);
			if(sizeof($aGraficosN) == sizeof($aGraficosT)){
				for($i=0;$i<sizeof($aGraficosN);$i++){
					$nombreGrafico = $aGraficosN[$i];
					$aNomGraficos[$nombreGrafico]['datos'] = array();
					$aNomGraficos[$nombreGrafico]['tipo'] = $aGraficosT[$i];
				}
			}
		}
		$sqlDebug = "";
		echo '<div id="tablonContainer">';
		echo '<div id="containerStats">';
		echo '<h3 class = "cabeceraStats">Estadísticas generales</h3>';
		echo '<dl class = "stat">';
		foreach($aCampos as $idx=>$valor){
			$sql = $valor['sql'];
			if(isset($valor['sqlChangeCurrent'])){
				$sqlChangeCurrent = $valor['sqlChangeCurrent'];
				if(preg_match("/{$sqlChangeCurrent}/",$sql)){
					$valChange = $this->currentValue;
					$sql = preg_replace("/{$sqlChangeCurrent}/",$valChange,$sql);
					$sqlDebug .= $sql."\n";
				}
			}
			$j=1;

			if(isset($valor['sqlChangeHistory'])){
				$sqlChangeHistory= $valor['sqlChangeHistory'];
				foreach($historia as $idx=>$vlr){
					if($historia[$idx][0]==$this->currentFather){
						$valorHist = $idx;
					}
				}
				$valChangeHistory = $historia[$valorHist][1];
				$sql = preg_replace("/{$sqlChangeHistory}/",$valChangeHistory,$sql);
				$sqlDebug .= $sql."\n";
			}
			$unidad = "";
			if(isset($valor['unidad'])){
				$unidad = $valor['unidad'];
			}

			$con = new con($sql);
			if($con->getNumRows()>0){
				$r = $con->getResult();
				$valorAlias = "";
				foreach($r as $k=>$v){
					if(isset($v) && !empty($v)){
						$valorAlias .= $v.' ';
					}
				}
				if(empty($v)){
					$valorAlias = 0;
				}

				if(isset($valor['transform'])){
					$trans = trim($valorAlias).$valor['transform'];
					eval("\$valorAlias = $trans;");
				}

				if(isset($valor['aGrafico'])){
					$adonde = explode("|",$valor['aGrafico']);
					for($i=0;$i<sizeof($adonde);$i++){
						$aNomGraficos[$adonde[$i]]['datos'][] = round($valorAlias,2);
						$aNomGraficos[$adonde[$i]]['titulos'][] = $valor['alias2'];
					}
				}

			}else{
				$valorAlias = "No hay datos";
				if(isset($valor['aGrafico'])){
					$adonde = explode("|",$valor['aGrafico']);
					for($i=0;$i<sizeof($adonde);$i++){
						$aNomGraficos[$adonde[$i]]['datos'][] = 0;
						$aNomGraficos[$adonde[$i]]['titulos'][] = $valor['alias2'];
					}
				}
			}
			echo '<dt>'.$valor['alias'].':</dt>';
			echo '<dd>'.round($valorAlias,2).' <span class="unidades">'.$unidad.'</span></dd><br/>';
		}
		echo '</dl>';
		echo '</div>'; //Fin containerStats
		echo '<div id="graficaStats">';
		echo '<h3 class = "cabeceraStats">Gráficas generales</h3>';
		foreach($aNomGraficos as $nom => $aValores){
			$aDatos = $aValores['datos'];
			$aTitulos = $aValores['titulos'];
			echo "<div align='center'>";
			if($aValores['tipo'] == 'barras'){
				$miUrl = $this->drawBarras($aDatos,$aTitulos,$nom);
				echo '<embed class = "embebido" src="./modules/estadisticas/flash/open-flash-chart.swf?variables=true'.$miUrl.'quality="high" bgcolor="#FFFFFF" width="400px" height="400px" name="grafico" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"/>';
			}elseif($aValores['tipo'] == 'quesitos'){
				$miUrl = $this->drawQuesitos($aDatos,$aTitulos,$nom);
				echo '<embed class = "embebido" src="./modules/estadisticas/flash/open-flash-chart.swf?variables=true'.$miUrl.'quality="high" bgcolor="#FFFFFF" width="400px" height="400px" name="grafico" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"/>';
			}elseif($aValores['tipo'] == 'combinacion'){
				$miUrl = $this->drawQuesitos($aDatos,$aTitulos,$nom);
				echo '<embed class = "embebido" src="./modules/estadisticas/flash/open-flash-chart.swf?variables=true'.$miUrl.'quality="high" bgcolor="#FFFFFF" width="400px" height="400px" name="grafico" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"/>';
				$miUrl = $this->drawBarras($aDatos,$aTitulos,$nom);
				echo '<embed class = "embebido" src="./modules/estadisticas/flash/open-flash-chart.swf?variables=true'.$miUrl.'quality="high" bgcolor="#FFFFFF" width="400px" height="400px" name="grafico" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"/>';
			}
		   	echo "</div>";

		}

		echo '</div>';

		echo '<ul id="optsTablon"><li class="sep">&nbsp;</li>';
		if (sizeof($this->history)>0) {
			echo '<li>'.$this->drawBackLink().'</li>';
		}
		echo '</ul>';
		if (isset($_GET['DEBUG'])) iError::warn("<textarea>".$sqlDebug."</textarea>");
	}
}


