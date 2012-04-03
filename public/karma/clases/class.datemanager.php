<?php 
class datemanager{
	
	var $mysql_timezone = false;
	
	var $php_timezone = false;
	
	var $date_lang = false;
	
	var $timediff = false;

	function __construct($kMenu){
		$this->mysql_timezone = $kMenu->get_karma_mysql_timezone();
		$this->php_timezone = $kMenu->get_karma_timezone();
		$this->date_lang = $kMenu->get_karma_date_lang();
		$this->date_separator = $kMenu->get_karma_date_separator();
		$this->set_date_lang($kMenu->getLang());
	}
	
	function set_timezone(){
		if ($this->mysql_timezone){
			new con("set time_zone = '".$this->mysql_timezone."';"); // MEJOR GMT
		}
		if( (isset($_POST['k_timezone']) && (int) $_POST['k_timezone']  ) || (isset($_SESSION['k_timezone']))){
			$tz = (isset($_POST['k_timezone']))? (int)$_POST['k_timezone']:$_SESSION['k_timezone'];
			$c = new con("select timezone from timezones where id_zone='".$tz."'");	
			if($c->getNumRows()<=0){
			}else{
				$r = $c->getResult();
				$this->php_timezone = $r['timezone'];
				$_SESSION['k_timezone'] = $tz;
			}
		}
		if ($this->php_timezone) date_default_timezone_set($this->php_timezone);
		else	date_default_timezone_set("Europe/Madrid"); //SYSTEM DEFAULTS
		define("K_TIMEZONE",date_default_timezone_get());
		$_SESSION["K_TIMEZONE"] = K_TIMEZONE; 
		$_SESSION["K_MYSQL_TIMEZONE"] = $this->mysql_timezone;
		$this->timediff = date('P');
		define("K_TIMEDIFF",$this->timediff);
		$_SESSION["K_TIMEDIFF"] = K_TIMEDIFF;
	}
	function get_timezones(){
		$c = new con("select id_zone, code, timezone, lon, lat from timezones order by code,timezone ");
		$aTimezones=array();
		while ($r = $c->getResult()){
			$aTimezones[] = $r;
		}
		return $aTimezones;
	}
	function set_date_lang($lang=false){
		$lang = (isset($this->date_lang))? $this->date_lang:$lang;
		define('K_SEPARATOR',$this->date_separator);
		switch ($lang){
			case "es":
				$jsaux = $lang;
				$datef = 'd'.K_SEPARATOR.'m'.K_SEPARATOR.'Y';
				$jsdatef = 'dd'.K_SEPARATOR.'mm'.K_SEPARATOR.'yy';
				$fDay = 1;
			break;
			case "eu":
				$jsaux = $lang;
				$datef = 'Y'.K_SEPARATOR.'m'.K_SEPARATOR.'d';
				$jsdatef = 'yy'.K_SEPARATOR.'mm'.K_SEPARATOR.'dd';
				$fDay = 1;
			break;
			case "en":
				$jsaux = $lang;
				$datef = 'm'.K_SEPARATOR.'d'.K_SEPARATOR.'Y';
				$jsdatef = 'mm'.K_SEPARATOR.'dd'.K_SEPARATOR.'yy';
				$fDay = 0;
			break;
			case "en_uk":
				$jsaux = $lang;
				$datef = 'd'.K_SEPARATOR.'m'.K_SEPARATOR.'Y';
				$jsdatef = 'dd'.K_SEPARATOR.'mm'.K_SEPARATOR.'yy';
				$fDay = 0;
			break;
			case "fr":
				$jsaux = $lang;
				$datef = 'd'.K_SEPARATOR.'m'.K_SEPARATOR.'Y';
				$jsdatef = 'dd'.K_SEPARATOR.'mm'.K_SEPARATOR.'yy';
				$fDay = 1;
			break;
			case "it":
				$jsaux = $lang;
				$datef = 'd'.K_SEPARATOR.'m'.K_SEPARATOR.'Y';
				$jsdatef = 'dd'.K_SEPARATOR.'mm'.K_SEPARATOR.'yy';
				$fDay = 1;
			break;
			case "de":
				$jsaux = $lang;
				$datef = 'd'.K_SEPARATOR.'m'.K_SEPARATOR.'Y';
				$jsdatef = 'dd'.K_SEPARATOR.'mm'.K_SEPARATOR.'yy';
				$fDay = 1;
			break;
			case "ch":
				$jsaux = $lang;
				$datef = 'Y'.K_SEPARATOR.'m'.K_SEPARATOR.'d';
				$jsdatef = 'yy'.K_SEPARATOR.'mm'.K_SEPARATOR.'dd';
				$fDay = 1;
			break;
			case "jp":
				$jsaux = "ja";
				$datef = 'Y'.K_SEPARATOR.'m'.K_SEPARATOR.'d';
				$jsdatef = 'yy'.K_SEPARATOR.'mm'.K_SEPARATOR.'dd';
				$fDay = 1;
			break;
			default: 
				$jsaux = $lang;	
				$datef = 'Y'.K_SEPARATOR.'m'.K_SEPARATOR.'d';
				$jsdatef = 'yy'.K_SEPARATOR.'mm'.K_SEPARATOR.'dd';
				$fDay = 1;
			break;
		}
		
		define('K_DATELANG',$jsaux); 
		define('K_FDAY',$fDay);
		define('K_DATEFORMAT',$datef);
		define('K_JSDATEFORMAT',$jsdatef);
		$_SESSION['K_JSDATEFORMAT'] = K_JSDATEFORMAT;
		$_SESSION['K_DATEFORMAT'] = K_DATEFORMAT;
		$_SESSION['K_SEPARATOR'] = K_SEPARATOR;
		define('K_TIMEFORMAT','H:i:s');
		define('K_TIMEFORMATNOSECS','H:i');
		define('K_DATETIMEFORMAT',K_DATEFORMAT.' '.K_TIMEFORMAT);
		define('K_DATETIMEFORMATNOSECS',K_DATEFORMAT.' '.K_TIMEFORMATNOSECS);
		$_SESSION['K_DATETIMEFORMATNOSECS'] = K_DATETIMEFORMATNOSECS;
		///if ($jsaux!="en") $aJs[] = 'scripts/jquery/i18n/ui.datepicker-'.$jsaux.'.js'; //datepickerLANGscripts		
	}
	
}
?>