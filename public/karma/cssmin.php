<?php

	$aPath = explode("/",$_GET['args']);

	if (strpos($_GET['args'],"..") === 0) die("?1");
	if (strpos($_GET['args'],"/") === 0) die("?2");
	if (!file_exists($_GET['args'])) die("?3");

	$hash = md5("CSS".filemtime($_GET['args']).$_GET['args']);

	chdir(dirname(__FILE__));

	$hashDir = dirname(__FILE__)."/../cache/karma_cached_css/";
	$hashFile = $hashDir.$hash;
	define("CHK_KARMA",1);
	require_once("libs/autoload.php");

	require_once(dirname(__FILE__).'/css/colors_default.php');
	if(file_exists(dirname(__FILE__).'/../configuracion/css/colors.php')){
		require_once(dirname(__FILE__).'/../configuracion/css/colors.php');
	}


	if (!file_exists($hashFile)) {
		foreach ($aPath as $p) {
			if (($p == "..") || ($p == ".")) continue;
			if (is_dir($p)) {
				chdir($p);
			} else {
				if (!file_exists($hashDir)) mkdir($hashDir,0777);
				ob_start();
				require($p);
				$content = ob_get_contents();
				ob_end_clean();

				file_put_contents($hashFile,gzencode(cssmin::minify($content,dirname($p),array('iron-base64-images'))));

			}
		}
	}

	$last_modified_time = filemtime($hashFile);
	if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
		trim($_SERVER['HTTP_IF_NONE_MATCH']) == $hash)) {
		header("HTTP/1.1 304 Not Modified");
		exit;
	}

	header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
	header('Etag: '.$hash);
	header("Content-Encoding: gzip");
	header("Content-Length: ".filesize($hashFile));
	header("Content-Type: text/css");
	readfile($hashFile);

