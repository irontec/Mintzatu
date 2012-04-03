<?php
/**
 * Interface que se tiene que implementar para poder procesar ficheros subidos mediante fileupload
 */

interface fileupload_interface_processor {
    /*
     * Setter donde encontrar el fichero especificado en processIt
     */
    /**
     * @param string $path Ruta absoluta / relativa (desde ./karma), en la que encontrar el ficehro $hash
     * @return void
     */
    public function setBaseDir($path);

    /*
     * Setter donde encontrar el fichero especificado en processIt
     */
    /**
     * @param string $path nombre del fichero a partr de setBaseDir [0-9]{8-9} <= o algo así.... 
     * @param string $fname Nombre original del fichero subido
     * @param string $token Marca enviada para el control de estado en el procesamiento de fichero (por si se requiere interactuación de usuarios).
     * @return void
     */
    public function proccessIt($path,$fname,$token);

}
