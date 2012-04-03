<?php


class fileupload_qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {    
        $input = fopen("php://input", "r");
        if (!$target = fopen($path, "w")) {
            return array("No se puede abrir el fichero para escritura","[".$path."]");
        }

        while ($buffer = fread($input,8048)) {
            fwrite($target,$buffer);
        }

        fclose($input);
        fclose($target);

        if (filesize($path) != $this->getSize()) {
            return array("Los tamaños de la petición y el fichero destino no coinciden","[".$filesize($path)." / ".$this->getSize()."]");
            
        }
        return true;
    }
    function getName() {
        return basename($_GET['qqfile']);
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            return "CONTENT_LENGTH no soportado";
        }      
    }   
}
