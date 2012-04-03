<?php
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
	define("SERVER_NAME",$_SERVER['HTTP_X_FORWARDED_HOST']);
} else {
	define("SERVER_NAME",$_SERVER['HTTP_HOST']);
	//.(($_SERVER['SERVER_PORT']!="80")? ":".$_SERVER['SERVER_PORT']:""));
}
	require_once("config.php");
	require_once(dirname(__FILE__)."/clases/class.i.php");

	if(!isset($_GET['action']) || empty($_GET['action'])){
		die();
	}else{
		switch($_GET['action']){
			case 'gallery':
				require(dirname(__FILE__)."/ajax_gallery.php");
				break;
			case 'save':
				require(dirname(__FILE__)."/ajax_save.php");
				break; 
		}
	}
	exit();

?>