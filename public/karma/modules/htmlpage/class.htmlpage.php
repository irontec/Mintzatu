<?php
/**
 * Fichero de clase para campo tipo ENUM
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 *
 * ::PLT::
 * nullable
 * dependencia
 * keys
 * values
 * defaultKey
 * grupodependiente_X
 */
/**
 * Fichero de clase para el módulo htmlpage
 *
 * @author eider
 * @version 1.0
 * @package karma
 *
 * ::CFG::
 * htmlfile : fichero (con ruta incluida) donde está el html a mostrar
 * dircss (opcional): si ¡¡todos!! los css están en el mismo directorio, se
 *      especifica aquí cual para no tener que repertilo en cada css. Hay que
 *      poner la barra final TODO--> controlar si hay barra puesta
 * dirjs (opcional): si ¡¡todos!! los js están en el mismo directorio, se
 *      especifica aquí cual para no tener que repertilo en cada js. Hay que
 *      poner la barra final TODO--> controlar si hay barra puesta
 * cssfiles (opcional): si hay CSS que incluir, se listan aquí separados por
 *      comas (si no se especifica 'cssdir', también hay que poner la ruta)
 * cssfiles (opcional): si hay JS que incluir, se listan aquí separados por
 *      comas (si no se especifica 'jsdir', también hay que poner la ruta)
 *
 */
class htmlpage extends contenidos
{
    protected $_rutaBase;
    protected $_rutaHeaders;
    protected $_htmlFile;
    protected $_titSecc;

    /**
     * Constructor de la clase
     * @param $conf : información de configuración (desde el .cfg)
     * @return unknown_type
     */
    function __construct(&$conf)
    {
        $this->_rutaBase = dirname(__FILE__)."/../../../";
        $this->_rutaHeaders = '../../';
        $this->l = new k_literal(KarmaRegistry::getInstance()->get('lang'));
        $this->conf = $conf;
        $this->fixOps();
    }

    /**
     * Función que dado un array trimea cada elemento del mismo y les añade un prefijo
     * @param array $elArray: el array a transformar
     * @param string $prefijo: prefijo para los elementos
     * @return array: el array tranformado
     */
    protected function trimArray($elArray, $prefijo)
    {
        for ($i = 0; $i < count($elArray); $i++) {
            $elArray[$i] = $prefijo.trim($elArray[$i]);
        }
        return $elArray;
    }

    /**
     * Función que recorre la configuración del cfg, la comprueba y en caso de
     * que todo vaya bien, carga las propiedades de la clase
     * @return boolean
     */
    protected function fixOps()
    {
        $cont = $this->conf['0'];
        if (isset($cont['tit']) && !empty($cont['tit'])) {
            $this->_titSecc = $cont['tit'];
        }
        if (!isset($cont["htmlfile"])) {
            iError::error("No hay fichero definido para el HTML");
            return false;
        } else {

            if (!file_exists($this->_rutaBase.$cont["htmlfile"])) {
                iError::error("No se encuentra el fichero HTML especificado ");
                return false;
            } else {
                $this->_htmlFile = $this->_rutaBase.$cont["htmlfile"];

                $dirCss = "";
                if (isset($cont['cssdir']) && !empty($cont['cssdir'])) {
                    if (is_dir($this->_rutaBase . $cont['cssdir'])) {
                        $dirCss .= $this->_rutaHeaders . $cont['cssdir'];
                    } else {
                        iError::error("El direcotorio de CSS especificado no existe");
                        return false;
                    }
                }
                $dirJs = "";
                if (isset($cont['jsdir']) && !empty($cont['jsdir'])) {
                    if (is_dir($this->_rutaBase . $cont['jsdir'])) {
                        $dirJs .= $this->_rutaHeaders . $cont['jsdir'];
                    } else {
                        iError::error("El direcotorio de JS especificado no existe");
                        return false;
                    }
                }

                $aCss = array();
                $aJs = array();
                if (isset($cont['cssfiles']) && !empty($cont['cssfiles'])) {
                    $aCss = explode(",", $cont['cssfiles']);
                    $aCss = $this->trimArray($aCss, $dirCss);
                }

                if (isset($cont['jsfiles']) && !empty($cont['jsfiles'])) {
                    $aJs = explode(",", $cont['jsfiles']);
                    $aJs = $this->trimArray($aJs, $dirJs);
                }

                $this->aCss = $aCss;
                $this->aJs = $aJs;
            }

        }
    }

    /**
     * Función que devuelve el array de js necesarios de la clase
     * @return array
     */
    public function getJS()
    {
        return $this->aJs;
    }

    /**
     * Función que devuelve el array de css necesarios de la clase
     * @return array
     */
    public function getCSS()
    {
        return $this->aCss;
    }

    /**
     * Para dibujar el título de segundo nivel
     * @return unknown_type
     */
    public function drawMigas()
    {
        if (!empty($this->_titSecc)) {
            echo "<h2>".$this->_titSecc."</h2>";
        } else {
            echo "<h2></h2>";
        }

    }

    /**
     * Implementación de la función que dibuja el módulo en karma
     */
    public function draw()
    {
        echo "<div id='karmaHtmlPage' class='karmaHtmlPage''>";
        require_once($this->_htmlFile);
        echo "</div>";
    }
}