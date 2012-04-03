<?php

	// Index para dibujar imágenes de tablon
	$aTipos = array(
		"lista"=>array(187,88),
		"listaaux"=>array(187,88),
		"detalle"=>array(230,185)	
	);
	
	$_args = explode("/",$_GET['args']);
	//var_dump($_args); die();
	//echo "<br />";echo "<br />";
	if ($_args[0] == "listaaux") {
		$args = array("img","noticias.plt","imagen_binario_2",$_args[1]);
	}else $args = array("img","noticias.plt","imagen_binario",$_args[1]);
	$publico = array(
		"w"=>$aTipos[$_args[0]][0],
		"h"=>$aTipos[$_args[0]][1],
		"prefix"=>$_args[0]
	);
	//var_dump($args,$publico); die();
	define("CHK_KARMA",1);	
	require_once("../karma/libs/autoload.php");
	
	tablon::generateCache(implode("/",$args),$publico);

?>