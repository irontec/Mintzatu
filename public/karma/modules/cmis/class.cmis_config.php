<?php


class cmis_config {

    /*
    * Clase constructora de cmis_service, en funcion a los perfiles CMIS definidos en ./configuracion/cmis_karma_conf.cfg
    */
    public static function factory($profile = "default") {
        $cmisProfiles = parse_ini_file(dirname(__FILE__) .'/../../../configuracion/cmis_karma_conf.cfg',true);

        if (!isset($cmisProfiles[$profile])) {
            if (($profile != "default") && (isset($cmisProfiles["default"])) ) {
                iError::warn("Falta el perfil ".$profile." de CMIS. Se utilizará 'default'");
                $profile = "default";
            } else {
                iError::error("Falta el perfil default de CMIS");
                return false;
            }
        }

        if (!isset($cmisProfiles[$profile]['url'])) {
            iError::error("Falta el campo url para ".$default." [CMIS]");
            return false;
        }
        
        if (!isset($cmisProfiles[$profile]['user'])) {
            iError::error("Falta el campo user para ".$default." [CMIS]");
            return false;
        }
        
        if (!isset($cmisProfiles[$profile]['pass'])) {
            iError::error("Falta el campo pass para ".$default." [CMIS]");
            return false;
        }

        $client = new cmis_service($cmisProfiles[$profile]['url'],$cmisProfiles[$profile]['user'],$cmisProfiles[$profile]['pass']);
        if (isset($cmisProfiles[$profile]['location.host'])) {
            $client->setLocationHost($cmisProfiles[$profile]['location.host']);
        }

        if (isset($cmisProfiles[$profile]['location.port'])) {
            $client->setLocationPort($cmisProfiles[$profile]['location.port']);
        }

        return $client;

    }



}
