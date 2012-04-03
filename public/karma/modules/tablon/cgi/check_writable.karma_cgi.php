#!/usr/bin/php
<?php

    if(getenv('PHP_CGI_FILE') != "check_writable")
    {
        exit(1);
    }

    if (!file_exists(getenv('KRM_TMP_FILE'))) {
        mkdir(getenv('KRM_TMP_FILE'));
    }

	if (!is_writeable(getenv('KRM_TMP_FILE'))) {
		echo "El directorio '".basename(getenv('KRM_TMP_FILE'))."' no tienes permisos de escritura para el uid ".posix_geteuid().".<br />Por favor, contacte con un administrador.";
		exit(2);
	}

	exit(0);

?>