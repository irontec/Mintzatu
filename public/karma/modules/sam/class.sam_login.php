<?php


class sam_login extends login {
	function doLogin() {
		$this->user = i::set($_SERVER['PHP_AUTH_USER'],false);
		$this->pass = i::set($_SERVER['PHP_AUTH_PW'],false);

		if (!$this->_checkUser()) {
			for (;1;$this->autentificar()) {
				if (!isset($_SERVER['PHP_AUTH_USER'])) continue;
				if ($this->_checkUser($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])) break;
			}
		}
	}


	protected function _checkUser() {
		ini_set("soap.wsdl_cache_enabled", 0);
		//$cliente = new SoapClient($_SERVER['SAM_WSDL'],array("trace"=> 1,"exceptions" => 1));
		$cliente = new samService(false);
		if ($this->user !==false && $this->pass !==false) {
			$clienteSOAPtemp = new SoapClient($_SERVER['SAM_WSDL'],array("trace"=> 1,"exceptions" => 1));
			$id_sesionUsr = $clienteSOAPtemp->validar_usuario($user, $pass);
			$_SESSION['id_ws_karma'] = $id_sesionUsr;
		} else {
			if (!isset($_SESSION['id_ws_karma'])) return false;
		}

		if ($user = $cliente->validar_autenticado($_SESSION['id_ws_karma'])) {
			$datosCliente = get_object_vars($cliente->getDatosUsuario($_SESSION['id_ws_karma'],$user));
			if ($datosCliente['esAdmin']) {
				return true;
			}
		}
		return false;
	}

	function autentificar() {
        header('WWW-Authenticate: Basic realm="ZONA PRIVADA"');
        header('HTTP/1.0 401 Unauthorized');
        echo "<strong><center>Datos de acceso incorrectos.</center></strong>";
        exit();
	}
}

?>