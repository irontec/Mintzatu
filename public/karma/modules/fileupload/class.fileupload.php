<?php

/**
 * Clase diseñada para 
 *  a) Ser extendida e implementar el método a especificar en atributo processMethod="método"
 *  b) Especificar una carpeta destino en el atributo destDir="/absolute/path or ./relative/from/karma" y allí se quedará el fichero. Sin más ni menos. * 
 * 
 *  processMethod, ignorará destDir
 * 
 *  abstract protected processMethod(string $pathToTempFile.NoExt, string $filename, mixed $token)
 *  @return void;
 *  el método debe devolver un JSON => array(string [*ok|warn|error], mixed ret[status|array('token1'=>'label1','token2'=>'label2')
 *  success => determina el color en el status (ok por defecto)
 *  ret => String => Mensaje de información
 *      => Array => opciones para usuario. Se reinvocará el método con el tercer parámetro seteado (a false el primer viaje)
 *  
 *  Process method deberá eliminar el fichero (en ./cache/fileuploadDir/[rand])
 */
  
class fileupload extends contenidos {

    protected $_description = '';
    protected $_extensions = array();
    protected $_min =0;
    protected $_max = 0;
    protected $_action = "Browse";
    protected $_cacheDir = false;
    protected $_processClass = false;

    function __construct($conf) {
        $this->conf = $conf;

        $this->_description = $conf['main']['description'];
        $this->_extensions = explode(",",$conf['main']['extensions']);

        if (isset($conf['main']['maxSize'])) {
            $this->_max = $conf['main']['maxSize'];
        }

        if (isset($conf['main']['action'])) {
            $this->_action = $conf['main']['action'];
        }


        if (isset($conf['main']['minSize'])) {
            $this->_min = $conf['main']['minSize'];
        }


        if (isset($conf['main']['processClass'])) {
            $this->_processClass = $conf['main']['processClass'];
            $this->_cacheDir = "../cache/fileuploadDir";
            
        } else {
            
            if (!isset($conf['main']['destDir'])) {
                iError::error("No existe ni directoiro destino, ni método de postProceso válido. Consulte la documentación.");
                return;
            } else {
                $this->_cacheDir = $conf['main']['destDir'];
            }
            
            // la comprobación de escritura, se hará más abajo, aprovechando la comprobación de cacheDir
            // Se evita la creación de la carpeta.
        }


        if ( $this->_cacheDir && (!file_exists($this->_cacheDir)) ) {
            if ($this->_processClass !== false) {
                if (!mkdir($this->_cacheDir,0755)) {
                    iError::error("No se puede crear carpeta especificada para fileupload.<br />Contacte con su administrador");
                }   
            }
        } else {
            if (!is_dir($this->_cacheDir)) {
                iError::error("La carpeta especificada de fileupload, es un fichero.<br />Contacte con su administrador" . $this->_cacheDir);
            }
            if (!is_writable($this->_cacheDir)) {
                iError::error("La carpeta especificada para fileupload, no es escribible.<br />Contacte con su administrador");
            }
        }

        $this->aJs[] = "../modules/fileupload/js/qq.fileuploader.js";
        $this->aJs[] = "../modules/fileupload/js/fileupload.js";

        $this->aCss[] = "../modules/fileupload/css/fileuploader.css";
    }

    public function upload() {

        $allowedExtensions = $this->_extensions;
        $sizeLimit = (float)$this->_max;
        $uploader = new fileupload_qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload($this->_cacheDir);

        // to pass data through iframe you will need to encode all html tags

        die(htmlspecialchars(json_encode($result), ENT_NOQUOTES));
    }

    protected function _doProcess($hash,$path,$token) {
        $processor = new $this->_processClass;
        $processor->setBaseDir($this->_cacheDir);
        $processor->proccessIt($hash,$path,$token);
        return;
    }

    public function ajax() {

        if (isset($_GET['upload'])) {
            ob_end_clean();
            return $this->upload();
        }
        
        if (isset($_GET['postProcess'])) {

            ob_end_clean();
            $path = (isset($_GET['fname']))? $_GET['fname'] : '';
            $hash = (isset($_GET['hash']))? $_GET['hash'] : '';
            $token = (isset($_GET['token']))? $_GET['token'] : false;
            return $this->_doProcess($hash,$path,$token);
        }
    }

    public function draw() {
        if ( (isset($_GET['upload']))  || (isset($_GET['postProcess'])) ) {
            // Aunque ya lo hace karma solo, para que funcione abrir en pestaña nueva desde firebug
            return $this->ajax();
        }

        echo '<div class="subirFichero">';
        echo '<form action="'. krm_menu::getURL() .'" method="post" enctype="multipart/form-data">';
        echo '<input type="hidden" name="ext" value="'.implode(",",$this->_extensions).'" />';
        echo '<input type="hidden" name="min" value="'.$this->_min.'" />';
        echo '<input type="hidden" name="max" value="'.$this->_max.'" />';
        echo '<input type="hidden" name="action" value="'.$this->_action.'" />';
        echo '<input type="hidden" name="toBeProcessed" value="'.( ($this->_processClass === false)? '0':'1').'" />';
        echo $this->_description;
        echo '<div id="uploadIt" ></div>';
        echo '</form>';
        echo '</div>';
        
        
    }

}
