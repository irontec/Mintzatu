<?php


class cmis_FLDfilefs extends tablon_FLDfilefs {

    const default_source_action ='mv'; // especifica al origen si debe mv / cp

    function __construct($conf, $idx, $plt = false) {
        parent::__construct($conf, $idx, $plt);
        // leer karma_config

    }


    public function preInsertCheckValue(&$value) {

        $objId = $value;
        $value = array();
        $profile = (isset($_GET['cmis_profile']))? $_GET['cmis_profile']:'';

        $client = cmis_config::factory($profile);

        $props = $client->getProperties($objId);
        $content = $client->getContentStream($objId);
        $tmpfname = tempnam("/tmp", "CMIS");
        file_put_contents($tmpfname,$content);

        $value['tmp_name'] = $tmpfname;
        $value['name'] = $props->properties['cmis:name'];
        $value['size'] = $props->properties['cmis:contentStreamLength'];
        $value['type'] = $props->properties['cmis:contentStreamMimeType'];
        
        return true;
    }
    
    public function getConstantTypeAjaxUpload() {
        return "_GET";
    }

    /*
    * Para borrar el fichero temporal (no viene por _FILE, no se borrará sólo... y el exec_suid tampoco lo borraba.
    */
    public function afterupdate($value, $id, $tab) {
        if ($ret = parent::afterupdate($value,$id,$tab)) {
            unlink($value['tmp_name']);
            return $ret;
        }

        return $ret;
    }


}
