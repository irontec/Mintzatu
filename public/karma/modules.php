<?php
	function getModuloSeccionAscendente(&$aConf,$seccion,$niv,$modSecc,$constante) {
		if (!isset($aConf[$seccion][$modSecc])) {
				do {
					if (isset($aConf[$niv][$modSecc])) {
						define($constante,$aConf[$niv][$modSecc]);
						return true;
					} else $niv--;
				} while($niv>=0);
		} else {
			define($constante,$aConf[$seccion][$modSecc]);
			return true;
		}
	}

	function makePath(&$vlr,$clave,$path) {
		$vlr = $path.$vlr;
	}



	#=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=#
	# Script que debe ser cargado dentro de index.php
	# Devuelve la siguientes constantes:
	# __CABECERA
	# __PIE
	# __FICH
	# __TRAT (no siempre)
	#=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=#
	# A este script llegan la constante __TIPO
	# Y el $_GET modulo
	#=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=#

	$modulo = (isset($_GET['modulo']))? preg_replace("/[^a-zA-Z0-9_]+/","",$_GET['modulo']):"index";

	$modulo = ($modulo=="")? "index":$modulo;
	define("__MODULO",$modulo);



	$aConf = parse_ini_file(dirname(__FILE__)."../../modules.ini.php",true);

	$defLang = false;
	if (function_exists("ofuncDefaultLang")) {
		$defLang = ofuncDefaultLang();
	}

	if (!isset($aConf[0]['langs'])) $langs = array("es","eu","en");
	else $langs = explode(",",$aConf[0]['langs']);

	if ($defLang === false) {
		if (!isset($aConf[0]['defLang'])) $defLang = "es";
		else $defLang = $aConf[0]['defLang'];
	}

	function funcIdioma(){
		return  (isset($_GET['lang']))? $_GET['lang']:"";
	}

	 $oFuncIdioma = function_exists ("ofuncIdioma");


	if ($oFuncIdioma){
		$fidioma = "ofuncIdioma";
		$idioma = ofuncIdioma();
	}else{
		$fidioma = "funcIdioma";
		$idioma = funcIdioma();
	}



	if ($idioma=="") $idioma = ((isset($_SESSION['lang']))? $_SESSION['lang']:false );

	if (!in_array($idioma,$langs)) {
		$idioma = $defLang;

	}


	if (isset($aConf['login'])) {
		switch(true) {
			case ( (isset($aConf['login']['file'])) && (file_exists($aConf['login']['file'])) ) :
				require($aConf['login']['file']);
			break;
			default:
				define("__TIPO",0);
			break;

		}

	} else define("__TIPO",0);



	$aJs = array();
	$aCss = array();
	$aCssPrint = array();
	$alternateCss = array();
	$forIECss = array();
	$forIElt8Css = array();
	$forIE8Css = array();
	$forIElt7Css = array();
	$forIE7Css = array();
	$forIElt6Css = array();
	$forIE6Css = array();

	$aTitle = array();
	$jsPath = "scripts/";
	$cssPath = "css/";
	//Rellenamos las variables para los metas de la cabecera.
	$title = i::set($aConf[0]['page_title']);
	$meta_titulo = i::set($aConf[0]['meta_titulo']);
	$meta_desc = i::set($aConf[0]['meta_desc']);
	$meta_keys = i::set($aConf[0]['meta_keys']);
	$meta_language = i::set($aConf[0]['meta_language']);
	$meta_url = i::set($aConf[0]['meta_url']);
	$meta_autor = i::set($aConf[0]['meta_autor']);
	$meta_copy = i::set($aConf[0]['meta_copy']);
	$pag_inicio = i::set($aConf[0]['meta_pag_inicio']);

	$niv = __TIPO;

	do {
		$seccion = $niv."::".$modulo;
		$niv--;
	} while ((!isset($aConf[$seccion]))&&($niv>=0));


	($niv++)&&($niv<0)&&($seccion = $niv."::".$modulo);



	if (!isset($aConf[$seccion])) {
		$i=0;
		while (isset($aConf[$i])) {
			if ((isset($aConf[$i."::".$modulo]))&&($i>__TIPO)) {
				if (($aConf[$i]['nopermiso'])&&($aConf[$aConf[$i]['nopermiso']])) {
					$niv = 0;
					$seccion = $aConf[$i]['nopermiso'];
					break;
				}
			}
			$i++;
		}

		if (!isset($aConf[$seccion])) {

			$dynamic_section = $niv."::%";
			if (isset($aConf[$dynamic_section])) {
				if (isset($aConf[$dynamic_section]['function']) ){
					$func = $aConf[$dynamic_section]['function'];
					list($class,$method) = explode("::",$func,2);
					if (method_exists($class,$method)){
						$aDynSecs = false;
						$evalstr='$aDynSecs='.$class.'::'.$method.'();';
						eval($evalstr);
						if (is_array($aDynSecs) && in_array($modulo,$aDynSecs['mods']) && ( (int)__TIPO>=(int)$aDynSecs['NIVEL'] )){
							$seccion = $niv."::%";
							if (isset($aDynSecs['css'])) $aConf[$seccion]['css'] = $aDynSecs['css'];
							if (isset($aDynSecs['js'])) $aConf[$seccion]['js'] = $aDynSecs['js'];
							if (isset($aDynSecs['request_lang'])) {
								$autoLang = $aDynSecs['request_lang'];
							}
							if (isset($aDynSecs['IDSEC'])) {
								$_IDSEC = $aDynSecs['IDSEC'];
							}
							if (isset($aDynSecs['IDENSEC'])) {
								$_IDENSEC = $aDynSecs['IDENSEC'];
							}
							$_dyn_mod = $modulo;
						}else{
							if ( !((int)__TIPO>=(int)$aDynSecs['NIVEL']) ){
								$niv = 0;
								$seccion = $aConf[1]['nopermiso'];
							}else{
								ob_end_clean();
								i::_404();
							}
						}
					}else{
						ob_end_clean();
						i::_404();
					}
				}else{

					ob_end_clean();
					i::_404();
				}
			}else{
				ob_end_clean();
				i::_404();
			}
		}

	}
	$niv = __TIPO;

	//Buscamos el navegador al que estamos sirviendo, por si le tenemos que dar un CSS o javascript especial.


	/*
	function makePath($vlr,$clave,$path) {
		$vlr = $path.$vlr;
	}
	$jsPath = "scripts/";
	$cssPath = "css/";


*/

	if (!isset($_GET['lang']) && isset($autoLang)) {
		if (!isset($_SESSION['lang']))
		$idioma = $autoLang;
	}
	if( !isset($_SESSION['lang']) && !isset($_GET['lang']) && !isset($autoLang) ){
		 $idioma= $defLang;
	}

	$_SESSION['lang'] = $idioma;
	if (!defined("__IDIOMA")) define("__IDIOMA",$idioma);


	if (!isset($aConf[$seccion]['no_css'])) {
		$curNiv = $niv;
		while ($curNiv>=0) {
			if (!isset($aConf[$curNiv]['css'])) {
				$curNiv--;
				continue;
			}
			$__aCss = explode(",",$aConf[$curNiv]['css']);
			$_cssPath = (isset($aConf[$curNiv]['css_path']))? $aConf[$curNiv]['css_path']:$cssPath;
			array_walk($__aCss,'makePath',$_cssPath);
			$aCss = array_keys(array_flip($aCss)+array_flip($__aCss));
			$curNiv--;
		}
	}

	if (isset($aConf[$niv]['cssPrint'])) {
		$__aCssPrint = explode(",",$aConf[$niv]['cssPrint']);
		$_cssPath = (isset($aConf[$niv]['css_path']))? $aConf[$niv]['css_path']:$cssPath;
		array_walk($__aCssPrint,'makePath',$_cssPath);
		$aCssPrint = array_keys(array_flip($aCssPrint)+array_flip($__aCssPrint));
	}

	if (isset($aConf[$niv]['alternatecss'])) {
		$__alternateCss = explode(",",$aConf[$niv]['alternatecss']);
		$_cssPath = (isset($aConf[$niv]['css_path']))? $aConf[$niv]['css_path']:$cssPath;
		array_walk($__alternateCss,'makePath',$_cssPath);
		$alternateCss = array_keys(array_flip($alternateCss)+array_flip($__alternateCss));
	}

	if (isset($aConf[$niv]['forIECss'])) {
		$__forIECss = explode(",",$aConf[$niv]['forIECss']);
		$_cssPath = (isset($aConf[$niv]['css_path']))? $aConf[$niv]['css_path']:$cssPath;
		array_walk($__forIECss,'makePath',$_cssPath);
		$forIECss = array_keys(array_flip($forIECss)+array_flip($__forIECss));
	}

	if (isset($aConf[$niv]['forIElt8Css'])) {
		$__forIElt8Css = explode(",",$aConf[$niv]['forIElt8Css']);
		$_cssPath = (isset($aConf[$niv]['css_path']))? $aConf[$niv]['css_path']:$cssPath;
		array_walk($__forIElt8Css,'makePath',$_cssPath);
		$forIElt8Css = array_keys(array_flip($forIElt8Css)+array_flip($__forIElt8Css));
	}

	if (isset($aConf[$niv]['forIE8Css'])) {
		$__forIE8Css = explode(",",$aConf[$niv]['forIE8Css']);
		$_cssPath = (isset($aConf[$niv]['css_path']))? $aConf[$niv]['css_path']:$cssPath;
		array_walk($__forIE8Css,'makePath',$_cssPath);
		$forIE8Css = array_keys(array_flip($forIE8Css)+array_flip($__forIE8Css));
	}

	if (isset($aConf[$niv]['forIElt7Css'])) {
		$__forIElt7Css = explode(",",$aConf[$niv]['forIElt7Css']);
		$_cssPath = (isset($aConf[$niv]['css_path']))? $aConf[$niv]['css_path']:$cssPath;
		array_walk($__forIElt7Css,'makePath',$_cssPath);
		$forIElt7Css = array_keys(array_flip($forIElt7Css)+array_flip($__forIElt7Css));
	}

	if (isset($aConf[$niv]['forIE7Css'])) {
		$__forIE7Css = explode(",",$aConf[$niv]['forIE7Css']);
		$_cssPath = (isset($aConf[$niv]['css_path']))? $aConf[$niv]['css_path']:$cssPath;
		array_walk($__forIE7Css,'makePath',$_cssPath);
		$forIE7Css = array_keys(array_flip($forIE7Css)+array_flip($__forIE7Css));
	}

	if (isset($aConf[$niv]['forIElt6Css'])) {
		$__forIElt6Css = explode(",",$aConf[$niv]['forIElt6Css']);
		$_cssPath = (isset($aConf[$niv]['css_path']))? $aConf[$niv]['css_path']:$cssPath;
		array_walk($__forIElt6Css,'makePath',$_cssPath);
		$forIElt6Css = array_keys(array_flip($forIElt6Css)+array_flip($__forIElt6Css));
	}

	if (isset($aConf[$niv]['forIE6Css'])) {
		$__forIE6Css = explode(",",$aConf[$niv]['forIE6Css']);
		$_cssPath = (isset($aConf[$niv]['css_path']))? $aConf[$niv]['css_path']:$cssPath;
		array_walk($__forIE6Css,'makePath',$_cssPath);
		$forIE6Css = array_keys(array_flip($forIE6Css)+array_flip($__forIE6Css));
	}

	$aJs = array();
	$curNiv = $niv;

	while ($curNiv>=0) {
		if (!isset($aConf[$curNiv]['js'])) {
			$curNiv--;
			continue;
		}
		$__aJs = explode(",",$aConf[$curNiv]['js']);
	//	var_dump($__aJs,"===".$curNiv);
		$_jsPath = (isset($aConf[$curNiv]['js_path']))? $aConf[$curNiv]['js_path']:$jsPath;
		array_walk($__aJs,'makePath',$_jsPath);
		$aJs = array_keys(array_flip($__aJs)+array_flip($aJs));
		$curNiv--;
	}

//var_dump($aJs);
	$aJsallinone = array();
	$curNiv = $niv;
	while ($curNiv>=0) {
		if (!isset($aConf[$curNiv]['allinone'])) {
			$curNiv--;
			continue;
		}
		$__aJsallinone = explode(",",$aConf[$curNiv]['allinone']);

		$_jsPathallinone = (isset($aConf[$curNiv]['js_path']))? $aConf[$curNiv]['js_path']:$jsPath;
		array_walk($__aJsallinone,'makePath',$_jsPathallinone);
		$aJsallinone = array_keys(array_flip($__aJsallinone)+array_flip($aJsallinone));
		$curNiv--;
	}



	$aDinJS = array();
	if (isset($aConf[$niv]['dinamicJS'])) {
		$aDinJS = explode(",",$aConf[$niv]['dinamicJS']);
	}



	$aTitle = $aConf[0]['title'];

	if (!getModuloSeccionAscendente($aConf,$seccion,__TIPO,"cabecera","__CABECERA")) echo "Error";
	if (!getModuloSeccionAscendente($aConf,$seccion,__TIPO,"pie","__PIE")) echo "Error";

	if (isset($aConf[$seccion]['js'])) {
		$__aJs = explode(",",$aConf[$seccion]['js']);

		$_jsPath = (isset($aConf[$seccion]['js_path']))?  $aConf[$seccion]['js_path']:$jsPath;
		array_walk($__aJs,'makePath',$_jsPath);
		$aJs = array_keys(array_flip($aJs)+array_flip($__aJs));
	}

	if (isset($aConf[$seccion]['css'])) {
		$__aCss = explode(",",$aConf[$seccion]['css']);
		$_cssPath = (isset($aConf[$seccion]['css_path']))? $aConf[$seccion]['css_path']:$cssPath;
		array_walk($__aCss,'makePath',$_cssPath);
		$aCss = array_keys(array_flip($aCss)+array_flip($__aCss));
	}

	if (isset($aConf[$seccion]['cssPrint'])) {
		$__aCssPrint = explode(",",$aConf[$seccion]['cssPrint']);
		$_cssPath = (isset($aConf[$seccion]['css_path']))? $aConf[$seccion]['css_path']:$cssPath;
		array_walk($__aCssPrint,'makePath',$_cssPath);
		$aCssPrint = array_keys(array_flip($aCssPrint)+array_flip($__aCssPrint));
	}


	define("__FICH",$aConf[$seccion]['f']);
	if (isset($aConf[$seccion]['trat'])) define("__TRAT",$aConf[$seccion]['trat']);

	if (isset($aConf[$seccion]['title'])) {
			$aTitle = $aConf[$seccion]['title']." - ".$aTitle;
	}

	define("PG_TITLE",$aTitle);

	if (isset($aConf[$seccion]['meta_refresh'])) $meta_refresh = $aConf[$seccion]['meta_refresh'];
	else $meta_refresh = false;


						$arrmeses=array(
						/*"LANG"=>array('N_MES'=>array('ABREV','min','mayus', ... ),*/
						"es"=>array('1'=>array('ene','enero','Enero'),
										'2'=>array('feb','febrero','Febrero'),
										'3'=>array('mar','marzo','Marzo'),
										'4'=>array('abr','abril','Abril'),
										'5'=>array('may','mayo','Mayo'),
										'6'=>array('jun','junio','Junio'),
										'7'=>array('jul','julio','Julio'),
										'8'=>array('ago','agosto','Agosto'),
										'9'=>array('sep','septiembre','Septiembre'),
										'10'=>array('oct','octubre','Octubre'),
										'11'=>array('nov','noviembre','Noviembre'),
										'12'=>array('dic','diciembre','Diciembre'),
								),
						"eu"=>array('1'=>array('urt','urtarrila','Urtarrila','ren'),
										'2'=>array('ots','otsaila','Otsaila'),
										'3'=>array('mar','martxoa','Martxoa'),
										'4'=>array('apr','aprila','Apirila'),
										'5'=>array('mai','maiatza','Maiatza'),
										'6'=>array('eka','ekaina','Ekaina'),
										'7'=>array('uzt','uztaila','Uztaila'),
										'8'=>array('abu','abuztua','Abuztua'),
										'9'=>array('ira','iraila','Iraila'),
										'10'=>array('urr','urria','Urria'),
										'11'=>array('aza','azaroa','Azaroa'),
										'12'=>array('abe','abendua','Abendua'),
								),
						"en"=>array('1'=>array('Jan','january','January'),
										'2'=>array('Feb','february','February'),
										'3'=>array('Mar','march','March'),
										'4'=>array('Apr','april','April'),
										'5'=>array('May','may','May'),
										'6'=>array('Jun','june','June'),
										'7'=>array('Jul','july','July'),
										'8'=>array('Aug','august','August'),
										'9'=>array('Sep','septembre','Septembre'),
										'10'=>array('Oct','october','October'),
										'11'=>array('Nov','november','November'),
										'12'=>array('Dec','december','December'),
								),
						"de"=>array('1'=>array('Jan','Januar','Januar'),
										'2'=>array('Feb','Februar','Februar'),
										'3'=>array('Mar','MÃ¤rz','MÃ¤rz'),
										'4'=>array('Apr','April','April'),
										'5'=>array('May','Mai','Mai'),
										'6'=>array('Jun','Juni','Juni'),
										'7'=>array('Jul','Juli','Juli'),
										'8'=>array('Aug','August','August'),
										'9'=>array('Sep','September','September'),
										'10'=>array('Oct','Oktober','Oktober'),
										'11'=>array('Nov','November','November'),
										'12'=>array('Dec','Dezember','Dezember'),
								),

								"ch"=>array('1'=>array('Jan','january','January'),
										'2'=>array('Feb','february','February'),
										'3'=>array('Mar','march','March'),
										'4'=>array('Apr','april','April'),
										'5'=>array('May','may','May'),
										'6'=>array('Jun','june','June'),
										'7'=>array('Jul','july','July'),
										'8'=>array('Aug','august','August'),
										'9'=>array('Sep','septembre','Septembre'),
										'10'=>array('Oct','october','October'),
										'11'=>array('Nov','november','November'),
										'12'=>array('Dec','december','December'),
								),

						"fr"=>array('1'=>array('jan','janvier','Janvier'),
										'2'=>array('fév','février','Février'),
										'3'=>array('mar','mars','Mars'),
										'4'=>array('avr','avril','Avril'),
										'5'=>array('mai','mai','Mai'),
										'6'=>array('jui','juin','Juin'),
										'7'=>array('juil','juillet','Juillet'),
										'8'=>array('aou','aout','Aout'),
										'9'=>array('sep','septembre','Septembre'),
										'10'=>array('oct','octobre','Octobre'),
										'11'=>array('nov','novembre','Novembre'),
										'12'=>array('déc','décembre','Décembre'),
								),
						);
?>
