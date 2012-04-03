<?php
class tablonfullcalendar extends tablon {
	protected $currentSection = 0;
	protected $currentFather = false;
	protected $currentValue = false;
	protected $lastValue = false;
	protected $history = array();
	/**
	 * Objeto que contiene la consulta principal de tablón
	 *
	 * @var object objeto consulta a la base de datos.
	 */
	protected $conPrinc = false;
	protected $noShownFields = false;
	protected $noEditFields = false;
	protected $showLimitFields = false;
	protected $totalRows = 0;

	protected $rutaPlantillas = RUTA_PLANTILLAS;

	public $backLink;
	public $aJs = array(
		"../modules/tablon/scripts/tablon.js",
		"../modules/tablon/scripts/jq.ajaxfileupload.js",
		"../modules/tablon/scripts/jeditable.js",
		"../modules/tablon/scripts/jq.taresizer.js",
		"../modules/tablon/scripts/jq.customFile.js",
		"../scripts/jquery/ui.datepicker.js",
		"../scripts/jquery/i18n/ui.datepicker-es.js",
		"../modules/tablon/scripts/tablon_multiedit.js",
		"../modules/tablon/scripts/jquery.mousewheel.js",
		"../modules/tablon/scripts/jquery.timepicker.js",
		"../modules/tablon/scripts/jquery.translate-1.2.6.min.js",
		"../modules/tablonfullcalendar/scripts/tablonfullcalendar.js"
	);
	public $aCss = array("../modules/tablon/css/tablon.css","../modules/tablonfullcalendar/css/tablonfullcalendar.css","../css/datepicker.css");
	public $plantillaPrincipal = false;

	public $selectedConf = null;

	protected $l;

	public $semana=array('d','l','m','m','j','v','s');

	public $timezones;

	/**
	 * método constructor para tablón.
	 *
	 * @param array $conf datos provenientes del fichero de configuración
	 */
	function __construct(&$conf) {
		//new con("set time_zone = 'GMT';");
		//date_default_timezone_set('GMT');
		$this->l = new k_literal(KarmaRegistry::getInstance()->get('lang'));
		$this->rutaPlantillas = "".dirname(__FILE__)."/../../../configuracion/tablon/";
		$this->conf = $conf;
		$this->fixOps();
		$this->setCurrentSection();

		$this->aJs[] = "../modules/tablon/scripts/date.js";
		$this->aJs[] = "../modules/tablon/scripts/jquery.autocomplete.js";
		//$this->aJs[] = "../modules/tablon/scripts/jquery.datePicker.js";
		$this->aJs[] = "../modules/tablon/scripts/tablon_dateformat.js";


		if (isset($this->selectedConf['timezoneselect']) && $this->selectedConf['timezoneselect']=="true"){
			$this->timezones = datemanager::get_timezones();
		}
	}


	public function get_timezone_HTMLselect($sel=false){
		$html = '<select id="comboTimezone" class="comboTimezone" name="k_timezone">';
		foreach ($this->timezones as $timezone) $html.= '<option value="'.$timezone['id_zone'].'"  '.((isset($sel)&&$sel!==false&&($sel==$timezone['timezone']||$sel==$timezone['id_zone']))? 'selected="selected"':'').' >'.$timezone['code'].' '.$timezone['timezone'].'</option>';
		$html.= '</select>';
		return $html;
	}



	protected function drawCalendarOptions(){

		if (isset($this->selectedConf['timezoneselect']) && $this->selectedConf['timezoneselect']=="true"){
			$select = $this->get_timezone_HTMLselect(K_TIMEZONE);
		/*	$this->timezones = datemanager::get_timezones();
			$this->drawTimezoneSelect();*/


			echo '<div class="calendarOptionsBox" ><form method="post" action="" >';
			echo '<p><span>'.date(K_DATEFORMAT).'</span> <span class="rhour">'.date('H').'</span>:<span class="rmin">'.date('i').'</span>:<span class="rsec">'.date('s').'</span>';
			echo $select.' <input type="submit" name="change timezone" value="change timezone" />';
			echo '</p></form></div>';

		}
	}

	protected function monthsInterval(){
		if (isset($_GET['start']))	$datetime = (int)$_GET['start'];
		else	$datetime = time();
		$this->actualTimestamp = $datetime;
		$mlimit = 12;
		$actualYear = date('Y',$datetime);
		$this->actualYear = $actualYear;
		$actualMonth = date('m',$datetime);
		$this->actualMonth = $actualMonth;
		$actualDay = 1;
		if ($this->_type!="default") return;
		$this->aMonths = array();
		$this->aShowDates = array();
		$startMonth = $actualMonth-1;
		$startYear = $actualYear;
		if ($startMonth<=0) {
			$startMonth = 12;
			$startYear--;
		}
		$diffYear=0;
		$naturallimit = 12;
		$naturalMlimit = $naturallimit;
		for ($i=0;$i<$mlimit;$i++){
			$Month = $startMonth+$i;
			$Year = $startYear+$diffYear;
			if ($Month==$naturallimit) {
				$diffYear++;
				$naturallimit = $naturallimit+12;
			}
			if ($Month>$naturalMlimit){
				$Month = (($Month-$naturallimit+12) ==0)? 12:($Month-$naturallimit+12);
			}
			if (!isset($this->aShowDates[$Year])) $this->aShowDates[$Year] = array();
			$this->aShowDates[$Year][$Month] = array();
		}
	}

	public function drawmonthday($aDay,$d=false){
		$daytimestamp = gmmktime(0,0,0,$aDay['m'],$aDay['d'],$aDay['y']);
		$nowtimestamp = time();
		$nowdaytimestamp = gmmktime(0,0,0,date('m'),date('d'),date('y'));
		$class = $aDay['type'];
		if ( $aDay['type'] == "month"){
			if ($nowdaytimestamp == $daytimestamp) $class.=' today';
		}
		$oHtml = array();
		foreach($this->aDateResults as $aContents){
			if ($aContents['FC_startFieldDAY'] == $daytimestamp) $oHtml[]=$aContents;
		}
		if (sizeof($oHtml)>0){
			$class.=' hasEvents';
		}
		echo '<td class="'.$class.'" >';
		echo '<a class="ajaxLoad tooltip" href="'.$this->url.'type=day&amp;start='.$daytimestamp.'" title="'.sizeof($oHtml).' events">';
		echo $aDay['d'];
		echo '</a>';
		if (sizeof($oHtml)>0 &&$d)	$this->drawDayContents($oHtml);
		echo '</td>';
	}

	protected function drawDayContents($arr) {
		$pl = &$this->plantillaPrincipal;
		echo '<ul class="events">';
		foreach ($arr as $r) {
			$colors = $this->getColors();
			echo '<li';
			echo ' id="'.$pl->getBaseFile().'::'.$r['__ID'].'" ';
			echo 'style="display:block;background:#'.$colors[0].';color:#'.$colors[1].';float:left;"';
			for ($i=0;$i<$pl->getNumFields();$i++) {
				switch ($pl->fields[$i]->getIndex()){
					case ($this->selectedConf['princField']):
						$title =  $pl->fields[$i]->drawTableValue($r[$pl->fields[$i]->getAlias()]);
					break;
					case ($this->selectedConf['start']):
						$start =  $pl->fields[$i]->drawTableValue($r[$pl->fields[$i]->getAlias()]);
					break;
					case ($this->selectedConf['end']):
						$end =  $pl->fields[$i]->drawTableValue($r[$pl->fields[$i]->getAlias()]);
					break;
				}
			}
			echo ' class="ltooltip" title="'.$title.': '.$start.'-'.$end.'" >';
			echo $title;
			echo '</li>';
		}
		echo "</ul>";
	}

	public function drawMonthTable($year,$month,$one=false){
		$timestamp = mktime(0,0,0,$month,1,$year);
		$gmtimestamp = gmmktime(0,0,0,$month,1,$year);
		$firstdayWeekDay = date('N',$timestamp);
		$numberofdaysinMonth = date('t',$timestamp);
		$aDays=array();
		$x=1;
		for($d=$firstdayWeekDay;$d>K_FDAY;$d--){
			$otimestamp = $gmtimestamp-($x*(86400));
			$aDays[$d] = array('type'=>'pmonth','y'=>(int)date('Y',$otimestamp),'m'=>(int)date('m',$otimestamp),'d'=>(int)date('d',$otimestamp));
			$x++;
		}
		ksort($aDays);
		$cont = $firstdayWeekDay;
		for($d=1;$d<=$numberofdaysinMonth;$d++){
			$cont++;
			$aDays[$cont] = array('type'=>'month','y'=>$year,'m'=>$month,'d'=>$d);
		}
		$end = 42 - sizeof($aDays);
		$gmendtimestamp = gmmktime(0,0,0,$month,$numberofdaysinMonth,$year);
		for($d=1;$d<=$end;$d++){
			$cont++;
			$otimestamp = $gmendtimestamp+($d*(86400));
			$aDays[$cont] = array('type'=>'nmonth','y'=>(int)date('Y',$otimestamp),'m'=>(int)date('m',$otimestamp),'d'=>(int)date('d',$otimestamp));
		}
		$aDays = array_values($aDays);

		if($one!==false){
			echo '<div class="monthBigBox " >';
			echo '<table class="monthTable BigTable">';
			echo '<tr class="princ"><td colspan="7" ><span class="monthName">'.(($month<10)? '0'.(int)$month:$month).' ('.$year.')</span></td></tr>';
			echo '<tr class="week">';
			$ah = array();
			for ($i=0;$i<=6;$i++) { if($i<K_FDAY) continue; echo "<td>".$this->semana[$i]."</td>"; $ah[]=$i; if($i==K_FDAY) break; }
			for ($z=0;$z<=6;$z++) { if($z<=$i) continue; echo "<td>".$this->semana[$z]."</td>"; $ah[]=$z; }
			for ($z=0;$z<=6;$z++) { if($z>=K_FDAY) continue; echo "<td>".$this->semana[$z]."</td>"; $ah[]=$z; }
			echo '</tr>';
			$ndayofBox = 0;
			for ($i=0;$i<6;$i++){
				echo '<tr>';
				for ($d=0;$d<7;$d++){
					$this->drawmonthday($aDays[$ndayofBox],true);
					$ndayofBox++;
				}
				echo '</tr>';
			}
			echo '</table>';
			echo '</div>';
		}else{
			echo '<div class="monthBox" >';
			echo '<table class="monthTable">';
			echo '<tr class="princ"><td colspan="7" ><span class="monthName">'.(($month<10)? '0'.(int)$month:$month).' ('.$year.')</span><a class="ajaxLoad monthopt  tooltip" title="go to month" href="'.$this->url.'type=month&amp;start='.$timestamp.'"><img src="./icons/calendario.png" /></a></td></tr>';
			echo '<tr class="week">';
			$ah = array();
			for ($i=0;$i<=6;$i++) { if($i<K_FDAY) continue; echo "<td>".$this->semana[$i]."</td>"; $ah[]=$i; if($i==K_FDAY) break; }
			for ($z=0;$z<=6;$z++) { if($z<=$i) continue; echo "<td>".$this->semana[$z]."</td>"; $ah[]=$z; }
			for ($z=0;$z<=6;$z++) { if($z>=K_FDAY) continue; echo "<td>".$this->semana[$z]."</td>"; $ah[]=$z; }
			echo '</tr>';
			$ndayofBox = 0;
			for ($i=0;$i<6;$i++){
				echo '<tr>';
				for ($d=0;$d<7;$d++){
					$this->drawmonthday($aDays[$ndayofBox]);
					$ndayofBox++;
				}
				echo '</tr>';
			}
			echo '</table>';
			echo '</div>';
		}
	}


	public function drawCalendarY(){
		foreach ($this->aShowDates as $Y=>$aM){
			foreach ($aM as $M=>$foo)	{
				$this->drawMonthTable($Y,$M);
			}
		}
	}
	public function drawCalendarM($t){
			$this->drawMonthTable(date('Y',$t),date('m',$t),true);
	}

	public	function random_color(){
	   mt_srand((double)microtime()*1000000);
	    $c = array();
	    while(sizeof($c)<3){
	        $c[]= mt_rand(0, 255);
	    }
	    return $c;
	}

	public function coldiff($R1,$G1,$B1,$R2,$G2,$B2){
	    return max($R1,$R2) - min($R1,$R2) +
	           max($G1,$G2) - min($G1,$G2) +
	           max($B1,$B2) - min($B1,$B2);
	}
	public function brghtdiff($R1,$G1,$B1,$R2,$G2,$B2){
	    $BR1 = (299 * $R1 + 587 * $G1 + 114 * $B1) / 1000;
	    $BR2 = (299 * $R2 + 587 * $G2 + 114 * $B2) / 1000;

	    return abs($BR1-$BR2);
	}
	public function lumdiff($R1,$G1,$B1,$R2,$G2,$B2){
	    $L1 = 0.2126 * pow($R1/255, 2.2) +
	          0.7152 * pow($G1/255, 2.2) +
	          0.0722 * pow($B1/255, 2.2);

	    $L2 = 0.2126 * pow($R2/255, 2.2) +
	          0.7152 * pow($G2/255, 2.2) +
	          0.0722 * pow($B2/255, 2.2);

	    if($L1 > $L2){
	        return ($L1+0.05) / ($L2+0.05);
	    }else{
	        return ($L2+0.05) / ($L1+0.05);
	    }
	}
	public function pythdiff($R1,$G1,$B1,$R2,$G2,$B2){
	    $RD = $R1 - $R2;
	    $GD = $G1 - $G2;
	    $BD = $B1 - $B2;

	    return  sqrt( $RD * $RD + $GD * $GD + $BD * $BD ) ;
	}
	public 	function getHex($o){return sprintf("%02X", $o); }

	public function getColors(){
		do{
			$a = $this->random_color();
			$R1 = $a[0];
			$G1 = $a[1];
			$B1 = $a[2];
			$R2 = 255;
			$G2 = 255;
			$B2 = 255;
			$R3 = 0;
			$G3 = 0;
			$B3 = 0;
			$cd = $this->coldiff($R1,$G1,$B1,$R2,$G2,$B2); //>500
			$cd2 = $this->coldiff($R1,$G1,$B1,$R3,$G3,$B3); //>500

		/*		echo $this->brghtdiff($R1,$G1,$B1,$R2,$G2,$B2); //>125
				echo "<br />";
				echo $this->lumdiff($R1,$G1,$B1,$R2,$G2,$B2);	//>5
				echo "<br />";
				echo $this->pythdiff($R1,$G1,$B1,$R2,$G2,$B2);	//>250
				echo "<br />";*/
		}while($cd>500 || $cd2>500);
		if ($cd<$cd2){
			$x = 2;
			$front = $this->getHex($R2).$this->getHex($G2).$this->getHex($B2);
		}else{
			$x = 3;
			$front = $this->getHex($R3).$this->getHex($G3).$this->getHex($B3);
		}
		$back = $this->getHex($R1).$this->getHex($G1).$this->getHex($B1);
		return array($back,$front);
	}

	public function drawCalendarD($t){


		$pl = &$this->plantillaPrincipal;
		echo '<div ><a id="addEntry" class="tooltip" title="new entry" ><img src="./icons/edit_add.png"  />New Entry</a></div>';
		echo '<h2 >'.date(K_DATEFORMAT,$t).'</h2>';
		$gmstart = gmmktime(0,0,0,date('m',$this->currgmtimestamp),date('d',$this->currgmtimestamp),date('Y',$this->currgmtimestamp));
		//ini_set("memory_limit","100M");
		$aDraw=array();
		foreach ($this->aDateResults as $id=>$aInfo){
			$colors = $this->getColors();
			//$this->aDateResults[$id]['color'] = $this->random_color();
			$this->aDateResults[$id]['color'] = $colors[0];
			$this->aDateResults[$id]['fontcolor'] = $colors[1];
			$falsestart = gmmktime(date('H',$aInfo['FC_startField']),date('i',$aInfo['FC_startField']),date('s',$aInfo['FC_startField']),date('m',$aInfo['FC_startField']),date('d',$aInfo['FC_startField']),date('Y',$aInfo['FC_startField']));
			$falseend = gmmktime(date('H',$aInfo['FC_endField']),date('i',$aInfo['FC_endField']),date('s',$aInfo['FC_endField']),date('m',$aInfo['FC_endField']),date('d',$aInfo['FC_endField']),date('Y',$aInfo['FC_endField']));
			if ($falseend<$falsestart) continue;
			if (($falseend-$falsestart) > 1000000 ) continue;
//			echo $aInfo['__ID'].' => '.	$falsestart.' =>'.$falseend.' '.$aInfo['FC_startField'].' '.$aInfo['FC_endField'].' * '.	date('r',$falsestart).' =>'.date('r',$falseend);

			for ($s=$falsestart;$s<$falseend;$s=$s+300){
				if (!isset($aDraw[$s])) $aDraw[$s]=array();
				$aDraw[$s][]=$id;
			}

		}
		echo '<input type="hidden" id="startField" value="'.$this->selectedConf['start'].'" />';
		echo '<input type="hidden" id="endField" value="'.$this->selectedConf['end'].'" />';
		echo '<input type="hidden" id="princField" value="'.$this->selectedConf['plt'].'::'.$this->selectedConf['princField'].'::%ID%" />';
		echo '<table  id="tabloncale" class="tablon"  genero="'.$pl->getGenero().'" entidad="'.$pl->getEntidad().'">';
		echo '<tr class="tablon_tr_header"><th>Hour</th>';
		if(isset($this->selectedConf['timeinterval']) && !empty($this->selectedConf['timeinterval'])){
			$intTime = (int)$this->selectedConf['timeinterval'];
			//$intTime = 5;
		}else{
			$intTime = 5;
		}
		for($i=0;$i<60/$intTime;$i++){
			echo '<th>'.$intTime.' min interval</th>';
		}
		echo '</tr>';
		$cont = 0;
		$ocont = 0;
		$aEvents = array();

		for ($i=0;$i<=24;$i++){
			echo '<tr>';
			for ($m=0;$m<60;$m=$m+$intTime){
				$time = $gmstart+$cont;
				if ($m==0) echo '<td>'.gmdate('d M',($time)).'<br /> '.gmdate('H:s',($time)).' - '.gmdate('H:s',($time+60*60)).'</td>';
				echo '<td>';
				//'.gmdate('d M',($time)).' -
				//echo '<span value="'.gmdate(K_DATEFORMAT.' '.K_TIMEFORMATNOSECS,($time)).'" endvalue="'.gmdate(K_DATEFORMAT.' '.K_TIMEFORMATNOSECS,($time+(60*5))).'" id="sp_'.$ocont.'" class="dateinfo">'.gmdate('H:i',($time)).'</span>';
				echo '<span value="'.gmdate(K_DATEFORMAT.' '.K_TIMEFORMATNOSECS,($time)).'" endvalue="'.gmdate(K_DATEFORMAT.' '.K_TIMEFORMATNOSECS,($time+(60*$intTime))).'" id="sp_'.$ocont.'" class="dateinfo">'.gmdate('H:i',($time)).'</span>';
				//echo '<span class="dateinfo2">'.gmdate('H:i',($time)).'</span>';
				if (isset($aDraw[$time])){
					foreach ($aDraw[$time] as $event){
						$r = $this->aDateResults[$event];
						echo '<div style="display:block;background:#'.$this->aDateResults[$event]['color'].';color:#'.$this->aDateResults[$event]['fontcolor'].';float:left;" ';
						echo ' class="eventBox e_'.$r['__ID'].'" oatr ="e_'.$r['__ID'].'" tabletrid="'.$pl->getBaseFile().'::'.$r['__ID'].'"   ';

						for ($ii=0;$ii<$pl->getNumFields();$ii++) {
							switch ($pl->fields[$ii]->getIndex()){
								case ($this->selectedConf['princField']):
									$title =  $pl->fields[$ii]->drawTableValue($r[$pl->fields[$ii]->getAlias()]);
								break;
								case ($this->selectedConf['start']):
									$start =  $pl->fields[$ii]->drawTableValue($r[$pl->fields[$ii]->getAlias()]);
								break;
								case ($this->selectedConf['end']):
									$end =  $pl->fields[$ii]->drawTableValue($r[$pl->fields[$ii]->getAlias()]);
								break;
							}
						}
						$iline =  $title.': '.$start.'-'.$end;
						echo ' title="'.$iline.'">';
						echo $iline;
						//echo $this->aDateResults[$event]['PRINC_FIELD'];
						/*foreach( $this->aDateResults[$event] as $q=>$drf){
							echo $q;
							if ($q==$this->selectedConf['princField']){
								echo $drf;
							}

						}*/
						//echo $this->aDateResults[$event]['Session Title'].' * '.date(K_DATETIMEFORMAT,$this->aDateResults[$event]['Start Date']).' * '.date(K_DATETIMEFORMAT,$this->aDateResults[$event]['End Date']);
						echo '</div>';
					}
				}
				echo '</td>';
				$ocont++;
				//$cont=$cont+300;
				$cont=$cont+(60*$intTime);
			}
			echo '</tr>';
		}
		echo '</table>';


		$multiops = ( ($this->selected_have_opts_tablon_edit()) || (isset($this->selectedConf['del'])) || (isset($this->selectedConf['csv'])));
		$generalops = ( (sizeof($this->history)>0) || (isset($this->selectedConf['new'])));
		echo '<table id="tablon" class="tablon"  genero="'.$pl->getGenero().'" entidad="'.$pl->getEntidad().'">';
		echo $this->plantillaPrincipal->drawHead(($this->hasOptions()&&($opts = $this->drawOptions("%id%"))),$this->getNoShownFields(),$multiops,krm_menu::getURL().$this->getHistoryURL());
		$this->drawTableContents($multiops,false);
		echo '</table>';


		if ($multiops) {
			echo '<ul id="optsTablon"';
			echo ' class="">';
			if (($ops = $this->selected_have_opts_tablon_edit())!==false) {

				foreach ($ops as $op){
				$url = krm_menu::getURL();
				$url .= $this->getHistoryURL();
				$url .= 'tSec['.$this->currentSection.'::'.$op.']=%id%';
				$class="opts";
				$mas = "";
				if ($this->conf[$op]['class'] == "tablon_edit"){
					echo  '<li><a href="'.$url.'" '.$mas.' class="'.$class.'" title="'.$this->conf[$op]['tit'].'">';
					if (isset($this->conf[$op]['img'])) {
						echo '<img src="./icons/'.$this->conf[$op]['img'].'" alt="'.$this->conf[$op]['tit'].'" />';
					}
					echo $this->conf[$op]['tit'];
					echo '</a></li>';
				}

				if ($this->conf[$op]['class'] == "tablon_multiedit"){

					$class = " tablon_multiedit";
					$mas = 'campo="'.$this->conf[$op]['campo'].'"';
					$mas.= 'plt="'.$this->conf[$op]['plt'].'"';
					echo  '<li><a href="'.$url.'" '.$mas.' class="'.$class.'" title="'.$this->conf[$op]['tit'].'">';
					if (isset($this->conf[$op]['img'])) {
						echo '<img src="./icons/'.$this->conf[$op]['img'].'" alt="'.$this->conf[$op]['tit'].'" />';
					}
					echo $this->conf[$op]['tit'];
					echo '</a></li>';
				}
				}
			}
			if ((isset($this->selectedConf['csv'])) && ((bool)$this->selectedConf['csv'])) {
				$url = krm_menu::getURL();
				$url .= $this->getHistoryURL();
				echo '<li>'.
				'<a href="'.$url.'&amp;CSV=1" class="tablon_edit" title="'.$this->l->lstr('csv ',false,$pl->getEntidad()).'">'.
				'<img src="./icons/csv.png" alt="Download" />'.
				$this->l->lstr('csv ',false,$pl->getEntidad()).
				'</a></li>';
			}
			if ((isset($this->selectedConf['del'])) && ((bool)$this->selectedConf['del'])) {
			//	echo '<li><img src="./icons/eraser.png" alt="Eliminar" id="multiDelete" />'.$this->l->lstr('Eliminar',false,$pl->getEntidad()).'</li>';
			}
			if(!$generalops) echo '</ul>';
		}


		if ($generalops) {
			if(!$multiops) echo '<ul id="optsTablon">';
			if(!$multiops) echo '<ul id="optsTablon">';
			echo '<li class="sep">&nbsp;</li>';
			if (isset($this->selectedConf['new'])) {
				$aNoEdit = $this->getNoEditFields();
				if(!empty($aNoEdit)){
					$param3 = implode(",",$this->getNoEditFields());
				}
				else{
					$param3 = '0';
				}
				$newOpts = explode(',',$this->selectedConf['new']);
				foreach ($newOpts as $newOpt){
					switch($newOpt) {
						case 'inline':
							echo '<li id="new::'.basename($pl->getFile()).'::'.$param3.'"><img src="./icons/apply.png" alt="'.$this->l->l('Nuevo').'" id="newInline" />'.$this->l->lstr('Nuev',$pl->getGenero(),$pl->getEntidad()).'</li>';
						break;
						case 'toline':
							echo '<li id="new::'.basename($pl->getFile()).'::'.$param3.'"><img src="./icons/apply.png" alt="'.$this->l->l('Nuevo').'" id="newToline" />'.$this->l->lstr('Nuev',$pl->getGenero(),$pl->getEntidad()).'</li>';
						break;
						case 'editPlt':
							$opcionEdit = $this->selectedConf['editPlt'];
							$pltEdit = basename($this->conf[$opcionEdit]['plt']);
							echo '<li id="new::'.$pltEdit.'::show::'.basename($pl->getFile()).'::'.$param3.'"><img src="./icons/apply.png" alt="'.$this->l->l('Nuevo').'" id="newInlineEdit" />'.$this->l->lstr('Nuev',$pl->getGenero(),$pl->getEntidad()).'</li>';
						break;
						case 'zip':
							echo '<li id="newzip::'.basename($pl->getFile()).'::'.$param3.'"><img src="./icons/apply.png" alt="Nuevo" id="newZip" />'.$this->l->lstr('Nuev',$pl->getGenero(),$pl->getEntidad()." Zip").'</li>';
						break;
					}
				}

			}
			if (sizeof($this->history)>0) {
				echo '<li>'.$this->drawBackLink().'</li>';
			}
			echo '</ul>';
		}
	}

	protected function drawTableContents($checkBox = false,$csv = false) {
		$pl = &$this->plantillaPrincipal;
		foreach ($this->aDateResults as $r) {
			//var_dump($r);
			echo '<tr class="hidentr" ';
			if (!$csv) echo ' id="'.$pl->getBaseFile().'::'.$r['__ID'].'" ';
			echo '>';
			if ($checkBox) {
				if (is_string($checkBox)) { // Estoy en multiselect, lo dibujo por cojones
					echo '<td class="multiselect" id="ms::'.$r['__ID'].'"><input type="checkbox"';
					if ($r[$checkBox] != NULL) echo ' checked="checked" value="'.$r[$checkBox].'"';
					echo '  /></td>';
				} else {
					if (!$csv) { // tablon normal, dibujo si no estoy en modo csv
						echo '<td class="multiselect" id="ms::'.$r['__ID'].'"><input type="checkbox" /></td>';
					}
				}
			}

			for ($i=0;$i<$pl->getNumFields();$i++) {
				if ($this->isNoShown($pl->fields[$i]->getIndex())) continue;
				$pl->fields[$i]->setCurrentID($r['__ID']);
				echo '<td';
				if (!$csv) echo ' id="'.basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$r['__ID'].'" class="';
					$lres = array();
					if(isset($this->selectedConf['translate'])){
							$lres =  $this->translatefield($pl->fields[$i]->getSQLFLD());

					}
				if (($this->isPlaceEditable())  && (!$this->isNoEdit($pl->fields[$i]->getIndex())) && (!$csv)) {

					if ($pl->fields[$i]->getCl()&&(trim($pl->fields[$i]->getCl())!="")) echo $pl->fields[$i]->getCl();

					else{
							echo 'editable ';
							if (isset($lres['0'])){ echo $lres['0']; }
					}
					echo ' "';
					echo ' type="' . $pl->fields[$i]->getType().'"  '.((isset($lres['1']))? 'lang="'.$lres['1'].'"':'').' >';
				} else {
					if (isset($lres['0'])){ echo $lres['0']; }
					if (!$csv) echo '">';
					else echo '>';

				}

				if($this->isShowLimit($pl->fields[$i]->getIndex())){
					echo text_utils::text_limit($pl->fields[$i]->drawTableValue($r[$pl->fields[$i]->getAlias()]),$this->showLimitFields[$pl->fields[$i]->getIndex()],"...");
				}else{
					echo $pl->fields[$i]->drawTableValue($r[$pl->fields[$i]->getAlias()]);
				}
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
				if ($opts = $this->drawOptions($r['__ID'])) echo '<td>'.$opts.'</td>';
			}
			echo '</tr>';
		}
		if (!$csv) $this->drawTRModel($checkBox);
	}


	public function getCustomSelectFields(){
		$start = $this->selectedConf['start'];
		$end = $this->selectedConf['end'];
		$sql  = " unix_timestamp(".$start.") as FC_startField, unix_timestamp(".$end.") as FC_endField , ".$this->selectedConf['princField']." as PRINC_FIELD ";
		return array($sql);
	}

	public function getCustomConds(){
		$start = $this->selectedConf['start'];
		$end = $this->selectedConf['end'];
		switch ($this->_type){
			case 'day':
				//$endValue = $this->nextgmtimestamp-1;
				$actualDay = date('d',$this->actualTimestamp);
				$sqltime = $this->currgmtimestamp;
				$sqltime = mktime(0,0,0,$this->actualMonth,$actualDay,$this->actualYear);
				$endValue =  $sqltime+86400+1;


				$sql = "
				( unix_timestamp(".$start.") >= '".($sqltime)."'  and unix_timestamp(".$start.") <= '".$endValue."'   )
				or
				( '".($sqltime)."'  between unix_timestamp(".$start.") and unix_timestamp(".$end.")    )
				";

			break;
			case 'month':
				$endValue = (gmmktime(0,0,0,(($this->actualMonth+1==13)? 1:$this->actualMonth+1),1,(($this->actualMonth+1==13)? $this->actualYear+1:$this->actualYear) )-1);
				$sql = "
				( unix_timestamp(".$start.") >= '".$this->currgmtimestamp."'  and unix_timestamp(".$start.") <= '".$endValue."'   )
				or
				( '".$this->currgmtimestamp."'  between unix_timestamp(".$start.") and unix_timestamp(".$end.")    )
				";
			break;
			default:
				$endValue = $this->nextgmtimestamp-1;
				$sql = "
				( unix_timestamp(".$start.") >= '".$this->currgmtimestamp."'  and unix_timestamp(".$start.") <= '".$endValue."'   )
				or
				( '".$this->currgmtimestamp."'  between unix_timestamp(".$start.") and unix_timestamp(".$end.")    )
				";
			break;


		}

		return array($sql);

	}

	public function loadCalendar(){
		$this->_type="month";
		if (isset($_GET['type'])) $this->_type=$_GET['type'];
		$this->monthsInterval();
		switch ($this->_type){
			case 'day':

				$actualDay = date('d',$this->actualTimestamp);

				$this->currgmtimestamp = gmmktime(0,0,0,$this->actualMonth,$actualDay,$this->actualYear);

				$this->prevgmtimestamp = $this->currgmtimestamp-86400;
				$this->nextgmtimestamp = $this->currgmtimestamp+86400;

				/*$this->currgmtimestamp = $this->actualTimestamp;
				$this->prevgmtimestamp = $this->actualTimestamp-86400;
				$this->nextgmtimestamp = $this->actualTimestamp+86400;*/
			break;
			case 'month':
				$this->currgmtimestamp = gmmktime(0,0,0,$this->actualMonth,1,$this->actualYear);
				$this->prevgmtimestamp = gmmktime(0,0,0,(($this->actualMonth-1==0)? 12:$this->actualMonth-1),1,(($this->actualMonth-1==0)? $this->actualYear-1:$this->actualYear) );
				$this->nextgmtimestamp = gmmktime(0,0,0,(($this->actualMonth+1==13)? 1:$this->actualMonth+1),1,(($this->actualMonth+1==13)? $this->actualYear+1:$this->actualYear) );

			break;
			default:
				$this->currgmtimestamp = gmmktime(0,0,0,$this->actualMonth,1,$this->actualYear);
				$this->prevgmtimestamp = gmmktime(0,0,0,$this->actualMonth,1,$this->actualYear-1);
				$this->nextgmtimestamp = gmmktime(0,0,0,$this->actualMonth,1,$this->actualYear+1);
			break;
		}
	}

	public function drawCalendar(){
		$this->url = krm_menu::getURL();
		$this->url .= $this->getHistoryURL();
		echo '<div>';
		echo '<ul class="calendarMenu">';
		echo '<li><a class="ajaxLoad tooltip" href="'.$this->url.'type=default" title="view year"><img src="./icons/calendar.gif" /></a></li>';
		echo '<li><a class="ajaxLoad tooltip" href="'.$this->url.'type=month" title="view month"><img src="./icons/calendario.png" /></a></li>';
		echo '<li><a class="ajaxLoad tooltip" href="'.$this->url.'type=day" title="view day"><img src="./icons/vcalendar.png" /></a></li>';
		echo '<li><a class="ajaxLoad tooltip" href="'.$this->url.'type='.$this->_type.'&amp;start='.$this->prevgmtimestamp.'" title="previous"><img src="./icons/stock_left.png" /></a></li>';
		echo '<li><a class="ajaxLoad tooltip" href="'.$this->url.'type='.$this->_type.'&amp;start='.$this->nextgmtimestamp.'" title="next"><img src="./icons/stock_right.png" /></a></li>';
		echo '</ul>';
		echo '</div>';
		$pl = &$this->plantillaPrincipal;
		$this->aDateResults = array();
		while ($r = $this->conPrinc->getResult()) {

				$start_date = $r['FC_startField'];
				$dayVtimestamp = gmmktime(0,0,0,date('m',$start_date),date('d',$start_date),date('y',$start_date));
				$r['FC_startFieldDAY'] = $dayVtimestamp;
				$end_date = $r['FC_endField'];

				$dayVtimestamp = gmmktime(23,59,59,date('m',$end_date),date('d',$end_date),date('y',$end_date));
				$r['FC_endFieldDAY'] = $dayVtimestamp;

				$this->aDateResults[] = $r;
		}
		switch ($this->_type){
			case 'day':
				$this->drawCalendarD($this->currgmtimestamp);
			break;
			case 'month':
				$this->drawCalendarM($this->currgmtimestamp);
			break;
			default:
				$this->drawCalendarY();
			break;
		}
	}


	public function draw() {
		echo $this->drawHelpDesc();
		echo $this->drawTitle();
		if (!$plantilla = $this->getPlt()) {
			iError::error("Plantilla no encontrada");
			return false;
		}
		$this->plantillaPrincipal = new tablon_plantilla($plantilla);
		if (isset($this->selectedConf['addplt'])) {
			$pltaux = $this->selectedConf['addplt'];
			$this->plantillaPrincipal->addPlt($this->rutaPlantillas,$pltaux);
		}
		$pl = &$this->plantillaPrincipal;
		if ($pl->hasJs())
				while ($this->aJs[] = $pl->getJS()) {}

		if ($pl->hasCss())
				while ($this->aCss[] = $pl->getCSS()) {}
		$multiops = ( ($this->selected_have_opts_tablon_edit()) || (isset($this->selectedConf['del'])) || (isset($this->selectedConf['csv'])));
		$csv = ((isset($_GET['CSV'])) && ($_GET['CSV']=="1"));
		$generalops = ( (sizeof($this->history)>0) || (isset($this->selectedConf['new'])));
		/*filtrado¿?*/
		$this->loadCalendar();
		$sql = $this->doSQL();

		/*$this->conPrinc->dump();

		exit();*/
		if (isset($this->selectedConf['search'])) {
			echo '<p id="__user_filtrado"><img src="./icons/viewmag.png" alt="'.$this->l->l('ut_filtrado').'" />'.$this->l->l('act_filtrado').'</p>';
			$this->drawSearcher();
		}
		if (isset($this->selectedConf['translate'])) {
			$this->translate();
		}
		/*filtrado¿?*/
		echo '<div id="tablonContainer">';

		$this->drawCalendarOptions();

		echo "<div class=\"calendarContainer\">";

		if (isset($_GET['ajax'])&&$_GET['ajax']=="true"){
			ob_end_clean();
			ob_start();
		}

		$this->drawCalendar();




		echo "</div>";

		if (isset($this->selectedConf['onnew'])) {
			echo '<input type="hidden" name="tablononnew" id="tablononnew" value="'.$this->selectedConf['onnew'].'" />';
		}
		echo '<form id="hiddenFields">';
		if (isset($this->selectedConf['idcond']))  {
			echo '<input type="hidden" name="'.$this->selectedConf['idcond'].'" value="'.$this->currentValue.'" />';
		}


		if (isset($this->selectedConf['idprecond']))  {
			echo '<input type="hidden" name="'.$this->selectedConf['idprecond'].'" value="'.$this->lastValue.'" />';
		}

		if ( isset($this->selectedConf['logincond']) && in_array('insert',explode('|',$this->selectedConf['logincondVConf'])))  {
//			$aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['logincond'].'=\''.$_SESSION["__ID"].'\'';
			echo '<input type="hidden" name="'.$this->selectedConf['logincond'].'" value="'.$_SESSION["__ID"].'" />';
		}
		if (isset($this->selectedConf['pltCond'])){
			$pltCondiciones = explode("|",$this->selectedConf['pltCond']);
			if(sizeof($pltCondiciones)>0){
				for($i=0;$i<sizeof($pltCondiciones);$i++){
					//$aConds[] = ' '.$pl->getTab().'.'.$pltCondiciones[$i];
					/* TODO mirar el igual y el distinto de en las condiciones*/
					$fr = explode('is',$pltCondiciones[$i],2);

					if (isset($fr[1])&&trim($fr[1])=="null"){


					}else{
						if (preg_match("/=/",$pltCondiciones[$i])){
							list($c,$v)=explode('=',$pltCondiciones[$i],2);
							echo '<input type="hidden" name="'.$c.'" value="'.trim($v).'" />';
						}
						if (preg_match("/is/",$pltCondiciones[$i])){
							list($c,$v)=explode('is',$pltCondiciones[$i],2);
							echo '<input type="hidden" name="'.trim($c).'" value="'.trim($v).'" />';
						}
					}
				}
			}
		}
		echo '</form>';
		echo '</div>';
		if (isset($_GET['ajax'])&&$_GET['ajax']=="true"){
			$html = ob_get_contents();
			ob_end_clean();
			echo json_encode(array('html'=>$html));
			exit();
		}
	}

	static function foo() {}


}


?>
