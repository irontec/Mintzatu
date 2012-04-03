#!/usr/bin/php
<?php

    if(getenv('PHP_CGI_FILE') != "check_suid")
    {
        exit(1);
    }

	if(is_link(getenv('KRM_EXEC_SUID'))){
		$ruta = readlink(getenv('KRM_EXEC_SUID'));
		$perms = fileperms($ruta);
	}else
		$perms = fileperms(getenv('KRM_EXEC_SUID'));

	if ($perms & 0x0800) { // Bit S
		exit(0);
	}
	echo "El binario ".getenv('KRM_EXEC_SUID')." no tiene bit de suid.<br />Consulte con un administrador.";
	exit(1);

?>