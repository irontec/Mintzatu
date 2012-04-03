#!/usr/bin/php
<?php
/*
*	Genera .plt automáticamente pasando como parámetro t el nombre de la tabla
*/
	require(dirname(__FILE__)."/../libs/autoload.php");
	chdir(dirname(__FILE__));

	
	
	$stdin = fopen("php://stdin","r");

	$gen = new binGenerateML();
	
	$line = "h";
	do {
		$gen->dispatch($line);
		echo "krm_ML#> ";
	} while ($line = $gen->getSt($stdin));
	
	//while($line=stream_get_line($stdin,65535,"\n"));
		
	

?>
