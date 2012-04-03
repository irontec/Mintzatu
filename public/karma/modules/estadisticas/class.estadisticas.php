<?php
/**
 * Módulo de estadísticas del callcenter
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class estadisticas extends tablon{
	public $aJs = array(
		"../modules/estadisticas/scripts/estadisticas.js"
	);

	public function draw() {
		echo "
			<center>
				<h1>Gr&aacute;ficas Estad&iacute;sticas Ivoz</h1>
				<button id=\"mostrardst\">Ver Datos DST</button>
				<button id=\"mostrarqueue\">Ver Datos Queue</button>
			<br>";
		$cont = new con("SELECT count(id_queue_stat) FROM cdr_callcenter left join queue_stats on(cdr_callcenter.id = queue_stats.id_cdr_callcenter) where cdr_callcenter.end = '1'");
		$cont = $cont->getResult();

		foreach($cont as $cont => $u)$tope = $u;

		$aux = 0;
		$total = 0;
		$elementos = array();

		$con2 = new con("SELECT wait_time FROM cdr_callcenter left join queue_stats on(cdr_callcenter.id = queue_stats.id_cdr_callcenter) where cdr_callcenter.end = '1' and queue_stats.id_queue_stat is not null");

		if($con2->getNumRows()>0){
			while($con3 = $con2->getResult()) {
					$con3 = $con3['wait_time'];

					$total = $total + $con3;
					$aux++;

					//$id++;

					}
			$media = $total / $aux;
			$media = substr($media,0,4);
			echo "<br/>";
			echo "<table border=1><tr><td>El tiempo de espera medio es de ".$media." segundos</td></tr></table>";
		}
		echo "<br/><br/>
			<object
				id=\"dst\"
				classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"
				codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\"
				width=\"500\"
				height=\"500\">
			<param
				name=\"movie\"
				value=\"modules/estadisticas/charts.swf?xmlfile=modules/estadisticas/dst.xml\">
			<param
				name=\"quality\"
				value=\"high\">
			<embed
				src=\"modules/estadisticas/charts.swf?xmlfile=modules/estadisticas/dst.xml\"
				quality=\"high\"
				pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\"
				type=\"application/x-shockwave-flash\"
				width=\"500\"
				height=\"500\">
			</embed>
			</object>

			<object
				id=\"queue\"
				classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"
				codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\"
				width=\"500\"
				height=\"500\">
			<param
				name=\"movie\"
				value=\"modules/estadisticas/charts.swf?xmlfile=modules/estadisticas/queue.xml\">
			<param
				name=\"quality\"
				value=\"high\">
			<embed
				src=\"modules/estadisticas/charts.swf?xmlfile=modules/estadisticas/queue.xml\"
				quality=\"high\"
				pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\"
				type=\"application/x-shockwave-flash\"
				width=\"500\"
				height=\"500\">
			</embed>
			</object>
		";
	}
}


