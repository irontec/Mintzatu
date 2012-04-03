<?php
        $legacyPath = '../../../../../../../tinyCache/';

        if (is_dir($legacyPath)) {
           $tinycachePath = $legacyPath;
        } else {
            $tinycachePath = '../../../../../../../cache/tinyCache/';
        }

        define('CACHE', $tinycachePath);

	/*if(file_exists('../../../../../../../configuracion/tinyConf/config.cfg')){
		define('CONFIG_FILE','../../../../../../../configuracion/tinyConf/config.cfg');
	}*/
?>
