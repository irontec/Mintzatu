<?php
/**
 * Fichero de clase para campo tipo XHTML
 *
 * @author Arkaitz Etxeberria <arkaitz@irontec.com>
 * @version 1.0
 * @package karma
 */
class tablon_FLDxhtml extends tablon_FLDsafetext {
    public function drawTableValueEdit($value,$clone=false,$disabled=false) {
        return '<textarea name="'.$this->getSQLFLD().'" cols="140" rows="20" style="width:98%;" id="'.$this->getSQLFLD().'_'.$this->getCurrentID().'" class="wymeditor" rel="'.$this->conf['perfil'].'">'.$this->drawTableValue($value).'</textarea>';

    }

    public function getConstantTypeAjaxUpload() {
        return "_POST";
    }

    public function loadJS(){
        $js[] = "../modules/tablon/scripts/wymeditor/jquery.wymeditor.js";
        $js[] = "../modules/tablon/scripts/wymeditor/plugins/resizable/jquery.wymeditor.resizable.js";
        $js[] = "../modules/tablon/scripts/wymeditor/plugins/hovertools/jquery.wymeditor.hovertools.js";
        $js[] = "../modules/tablon/scripts/wymeditor/plugins/jquery.wymeditor.resize-min.js";
        /**
         * TODO: Cargar los plugins con getScript desde tablon
         */

        return $js;
    }
}
