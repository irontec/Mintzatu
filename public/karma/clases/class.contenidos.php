<?php
/**
 * Fichero principal de la clase tablon,
 * para listados de tablas sobre jqgrid.
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

abstract class contenidos
{
    protected $conf;
    public $selectedConf = null;
    public $aJs = array();
    public $aCss = array();

    public function getJS()
    {
        return $this->aJs;
    }

    public function getCSS()
    {
        return $this->aCss;
    }

    abstract protected function draw();



    /**
     * Devuelve el nombre de la clase, tal y como se especifica en el atributo "class" del módulo
     */
    public function getCurrentClassName()
    {
        if (isset($this->selectedConf['class']) && !empty($this->selectedConf['class'])) {
            return $this->selectedConf['class'];
        }
        return false;
    }

    /**
     * Devuelve el texto de ayuda de la sección en caso de que exista, false en caso contrario
     * @return mixed
     */
    public function isHelper()
    {
        $doc = '';
        if (isset($this->selectedConf['desc']) && $this->selectedConf['desc']!="") {
            $doc = self::getMLval($this->selectedConf['desc'], $this->selectedConf, 'desc');
        }

        if (!defined("RUTA_HELP")) {
            define("RUTA_HELP", dirname(dirname(dirname(__FILE__))). "/configuracion/tablon_help/");
        }

        if (isset($this->selectedConf['descfile'])) {
                $descfile = self::getMLval($this->selectedConf['descfile'], $this->selectedConf, 'descfile');
                if ($this->selectedConf['descfile'] == "auto"
                && isset($this->conf['main']['iden'])) {
                    $descfile = $this->conf['main']['iden'] . '/' . $this->_currentOption . '_' . KarmaRegistry::getInstance()->get('lang') . '.html';
                }

                if (file_exists((RUTA_HELP . $descfile))) {
                    $doc = implode("\n", file(RUTA_HELP . $descfile));
                }
        }
        if ($doc != '') {
            $imgClass = "open";
            $divStyle = "";
            if (isset($this->selectedConf['descopen']) && $this->selectedConf['descopen']=="0") {
                $imgClass="";
                $divStyle="display:none;";
            }

            /*
             * Si existe el link moreInfo, se coge lo que haya entre el link que contiene "moreInfo"
             * y el link <a id="moreInfoEnd"/> para ocultarlo.

             * Si no existe moreInfoEnd se coge desde el link hasta el final.
             *
             * TODO: Molaría cambiar las siguientes líneas por un preg_match, a que sí?
             */
            $moreInfoPos = strpos($doc, 'moreInfo');
            $hidden = '';
            $finDoc = '';
            if ($moreInfoPos) {
                $linkEndPos = strpos($doc, '</a>', $moreInfoPos) + 4;
                $sh = substr($doc, 0, $linkEndPos);
                $iniHidden = strpos($doc, '<a id="moreInfoEnd"/>', $linkEndPos);
                if ($iniHidden !== false) {
                    $iniHidden2 = ($iniHidden - 1) - $linkEndPos;
                    $finHidden = $iniHidden + strlen('<a id="moreInfoEnd"/>');
                    $finDoc = substr($doc, $finHidden + 1, strlen($doc));
                } else {
                    $iniHidden2 = strlen($doc);
                }
                $hidden = substr($doc, $linkEndPos + 1, $iniHidden2);
                if ($hidden !== '') {
                    $doc = $sh . '<span id="hiddenInfo" style="display:none;"><br>' . $hidden . '</span>';
                }
                if ($finDoc !== '') {
                    $doc = $doc .  $finDoc;
                }
            }
            return $doc;
        }

        return false;
    }

    /**
     * Indica si el área de ayuda se debe iniciar desplegada o no
     * @return bool
     */
    public function mustHelperStartOpened()
    {
        if ((isset($this->selectedConf['desc'])&&$this->selectedConf['desc']!="")
            || (isset($this->selectedConf['descfile']) && file_exists(RUTA_HELP.$this->selectedConf['descfile']))) {
                if (isset($this->selectedConf['startHelperOpened']) && ($this->selectedConf['startHelperOpened'])) {
                    return true;
                }
        }
        return false;
    }

    protected function _getPlt($sec)
    {
        if (!file_exists($this->rutaPlantillas.$this->conf[$sec]['plt'])) {
            return false;
        }
        return $this->rutaPlantillas.$this->conf[$sec]['plt'];
    }

    protected function _drawTitle($title)
    {
        return '<p class="title">' . $title . '</p>';
    }

    protected function drawBackLink()
    {
        if ($this->getHistoryURL(1)===false) return "&nbsp;";
        return '<a class="optsLink" href="'.krm_menu::getURL().$this->getHistoryURL(1).'" id="backButton" title="'
               . $this->l->l('Volver').'" ><img src="./icons/_back.png" alt="' . $this->l->l('Volver') . '">'
               . $this->l->l('Volver').'</a>';
    }

    public static function getMLval($key, $arr, $q="alias")
    {
        return KarmaRegistry::getInstance()->get('translator')->translate($key, $arr, $q);
    }


    public function addModLiterals($path)
    {
         $this->l->addLiteralsFromPath($path);
    }

}
