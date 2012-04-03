<?php
    if(file_exists(dirname(__FILE__).'/../../configuracion/css/colors.php')){
	   require_once(dirname(__FILE__).'/../../configuracion/css/colors.php');
    }

	require_once(dirname(__FILE__).'/colors_default.php');

	//*************************Sobre el CSS:
	$isIE = !(!isset($_SERVER['HTTP_USER_AGENT']) || !preg_match('/msie\s(5\.[5-9]|[6-9]\.[0-9]*).*(win)/i',$_SERVER['HTTP_USER_AGENT']) || preg_match('/opera/i',$_SERVER['HTTP_USER_AGENT']));
	function setBG($ruta,$attrib,$color="") {
		global $isIE;
		if ($isIE)
			echo (($color!="")? "\tbackground:".$color.";\n":"")."\tfilter:progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod='scale', src='".$ruta."');\n";
		else
			echo "\tbackground:".$color." url(\"../".$ruta."\") ".$attrib.";\n";
	}
	header("Content-Type: text/css");
?>