<?php
	session_id($_GET['session_name']);
	session_name("karmaPrivate");
	session_start();
	session_cache_limiter("private");
	header("Expires: 0");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false); // HTTP/1.1
	header("Pragma: no-cache");// HTTP/1.0
	/* fin de header anti cache */

	define("CHK_KARMA",1); // Constante para comprobar que todos los ficheros son cargados desde index
	if (isset($_GET['DEBUG'])) define("DEBUG",1);

	// Cargamos el "cargador" de classes
	require_once("../../../libs/autoload.php");
	if (empty($_FILES)) die("ERROR");

	$tempFile = $_FILES['Filedata']['tmp_name'];

	$targetPath = "/tmp/farsa/";
		$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];

	// Uncomment the following line if you want to make the directory if it doesn't exist
	// mkdir(str_replace('//','/',$targetPath), 0755, true);

	move_uploaded_file($tempFile,$targetFile);

	$buf = ob_get_contents();
    $fp  = fopen("/tmp/farsa/logggggg","a");
    fwrite($fp,$buf);
    fclose($fp);
    ob_end_clean();
    echo "1";
?>