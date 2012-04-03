<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es">
<head>
	<title><?php echo PG_TITLE ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<?php
	if (($meta_refresh !== false)&&(!isset($_GET['jsKey']))) {
		eval("\$metaURL = ".$meta_refresh.";"); 
		echo "\n\t<meta http-equiv=\"refresh\" content=\"".$metaURL."\" />";
	}
?>
    
	<meta name="title" content="<?php echo $meta_titulo ?>" />
	<meta name="description" content="<?php echo $meta_desc ?>" />
	<meta name="keywords" content="<?php echo $meta_keys ?>" />
	<meta name="robots" content="all" />
	<meta name="language" content="<?php echo $meta_language ?>" />
	<meta name="author" content="Irontec"/>
	<meta name="Copyright" content="<?php echo $meta_copy ?>" />
	<link rel="Index" href="<?php echo $pag_inicio ?>" />
	<link rel="shortcut icon" href="http://www.visitacostadelsol.com/images/favicon.ico" />
	
<?php
	//Insertamos las hojas de estilo definidas en modules.ini
	if (sizeof($aCss)>0) {
		//echo "\t<link rel=\"stylesheet\" href=\"".i::base_url()."css/".implode(",".i::base_url()."css/",$aCss)."\" type=\"text/css\" media=\"all\" />\n";
		foreach ($aCss as $css) echo "\t<link rel=\"stylesheet\" href=\"".i::base_url().$css."\" type=\"text/css\" media=\"all\" />\n";
	}
	echo "\t<link rel=\"stylesheet\" href=\"".i::base_url()."css/print.css\" type=\"text/css\" media=\"print\" />\n";
	//Insertamos los scripts definidos en modules.ini
	if (sizeof($aJs)>0) {
		//echo "\n\t<script type=\"text/javascript\" src=\"".i::base_url()."scripts/".implode(",".i::base_url()."scripts/",$aJs)."\" ></script>\n";
		foreach ($aJs as $js) echo "\t<script type=\"text/javascript\" src=\"".i::base_url().$js."\" ></script>\n";
	}
?>
	
<!--[if IE]>
<style>v\: * { behavior: url(#default#VML);display:inline-block; }</style>
<xml:namespace ns='urn:schemas-microsoft-com:vml' prefix='v' ></xml>
<![endif]-->
	
</head>
<body>
<div id="contenido">
<!--pagina -->
<div id="pagina">