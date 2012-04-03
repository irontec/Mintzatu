<?php
	$checker = new ReflectionClass($_GET['class']);

	if (!$checker->implementsInterface('tablon_InterfacePopupOption')) {
		die(json_encode(array("error"=>"Error indeterminado.<br />La clase solicitada no esta correctamente definida(tablon_popupInterface).")));
	}
	
	$controller = new $_GET['class']($_GET['id']);

	//Esto implica que la clase ha pedido intel antes de hacer nada
	if (isset($_GET['data'])) {
		$controller->feedData();
	}

	$controller->resolveIt();
