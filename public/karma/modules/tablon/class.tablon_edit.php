<?php
/**
 * Fichero principal de la clase tablon_edit,
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_edit extends tablon
{
    protected $_aIds = array();
    protected $_slideabbleFields = false;
    protected $_numResultados = 0;
    public $plantillaPrincipalCompare;

    function __construct($conf)
    {
        parent::__construct($conf);
        $this->aJs[] = "../modules/tablon/scripts/tablon_edit.js";
        $this->aJs[] = "../modules/tablon/scripts/jquery.datePicker.js";
        $this->aJs[] = "../modules/tablon/scripts/wymeditor/jquery.wymeditor.js";
        $this->aJs[] = "../modules/tablon/scripts/wymeditor/plugins/jquery.wymeditor.resize-min.js";
        $this->aJs[] = "../modules/tablon/scripts/wymeditor/plugins/hovertools/jquery.wymeditor.hovertools.js";
        $this->aJs[] = "../modules/tablon/scripts/wymeditor/plugins/resizable/jquery.wymeditor.resizable.js";
        $this->aJs[] = "../modules/tablon/scripts/tiny_mce/tiny_mce.js";
        if (isset($this->conf['main']['tinyConf'])) {
            $this->aJs[] = "../../configuracion/js/".$this->conf['main']['tinyConf'];
        } else {
            $this->aJs[] = "../modules/tablon/scripts/tiny_conf.js";
        }
        $this->aJs[] = "../modules/tablon/scripts/jqueryMultiSelect.js";
        $this->aCss[] = "../modules/tablon/css/jqueryMultiSelect.css";
        $this->aJs[] = "../modules/tablon/scripts/jquery.mousewheel.js";
        $this->aJs[] = "../modules/tablon/scripts/jquery.timepicker.js";
    }


    protected function buildConds()
    {
        $aConds = array();
        $pl = &$this->plantillaPrincipal;
        if ($del = $pl->getDeletedFLD()) {
            $aConds[] = ' '.$pl->getTab().'.'.$del.'=\'0\'';
        }

        $aIds = explode(",", $this->currentValue);

        $aMiniConds = array();

        if (isset($this->selectedConf['idcond'])) {

            for ($i=0;$i<sizeof($aIds);$i++) {
                $aMiniConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['idcond'].'=\''.$aIds[$i].'\'';
            }

        } else {
            if (!isset($aIds[0])
            || $aIds[0] == ""
            || $aIds[0] === false
            ) {



            } else {
                for ($i=0;$i<sizeof($aIds);$i++) {
                    $this->_aIds[] = $aIds[$i];
                    $aMiniConds[] = ' '.$pl->getTab().'.'.$this->plantillaPrincipal->getID().'=\''.$aIds[$i].'\'';
                }
            }
        }
        $aLT = $pl->getALeftTabs();
        if (isset($this->selectedConf['idcondRel']) && !empty($aLT['lefttab'])) {

            for ($i=0;$i<sizeof($aIds);$i++) {
                $aMiniConds[] = ' '.$this->selectedConf['idcondRel'].'=\''.$aIds[$i].'\'';
            }

        }


        if (isset($this->selectedConf['logincond'])
           && in_array('select', explode('|', $this->selectedConf['logincondVConf']))
           && $_SESSION["__ID"]!='1' ) {
            $aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['logincond'].'=\''.$_SESSION["__ID"].'\'';
        }

	//IVOZ-NG
        if (isset($this->selectedConf['logingrupocond']) && $_SESSION["__ID"]!='1' && !in_array($_SESSION["__ID"],explode("|",$this->selectedconf['adminarray']))){
		$aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['logingrupocond'].' =\''.$_SESSION["__GRUPO_VINCULADO"].'\'';
	}

        if (isset($this->selectedConf['hideifnotadmin'])
           &&($this->selectedConf['hideifnotadmin'] != $_SESSION["__ID"])) {
            $aConds[] = ' '.$pl->getTab().'.'.$pl->getId().'!=\''.$this->selectedConf['hideifnotadmin'].'\'';
        }

        if (isset($this->selectedConf['hideifnotroladmin'])
           &&!in_array($this->selectedConf['hideifnotroladmin'], $_SESSION["__IDROL"])) {
            $aConds[] = ' '.$pl->getTab().'.'.$pl->getId().'!=\''.$this->selectedConf['hideifnotroladmin'].'\'';
        }

        if (sizeof($aMiniConds)>0 )$aConds[] = '('.implode(" or ", $aMiniConds).')';
        $aMiniConds = array();
        if (isset($this->selectedConf['pltCond'])) {
            $pltCondiciones = explode("|", $this->selectedConf['pltCond']);
            if (isset($this->selectedConf['pltCondVConf'])) {
                if (in_array('select', explode('|', $this->selectedConf['pltCondVConf']))) {
                    if (sizeof($pltCondiciones)>0) {
                        for ($i=0;$i<sizeof($pltCondiciones);$i++) {
                            $aMiniConds[] = ' '.$pl->getTab().'.'.$pltCondiciones[$i];
                        }
                    }
                    $aConds[] = '('.implode(" or ", $aMiniConds).')';
                }
            } else {
                if (sizeof($pltCondiciones)>0) {
                    for ($i=0;$i<sizeof($pltCondiciones);$i++) {
                        $aMiniConds[] = ' '.$pl->getTab().'.'.$pltCondiciones[$i];
                    }
                }
                $aConds[] = '('.implode(" or ", $aMiniConds).')';
            }
        }



        if (sizeof($aConds)>0) return " where ".implode(' and ', $aConds);
        else return "";
    }

    protected function doSQL()
    {
        /*
         * ALAYN
         *
         * Le quito el limit a esta SQL para poder utilizar la páginación en el botón volver...
         */
        unset($this->selectedConf['limit']);

        $this->conPrinc = new con($this->getSQL());
        $this->_numResultados = $this->conPrinc->getNumRows();
        return !$this->conPrinc->error();
    }

    public function loadslideabbleFields()
    {
        if ( isset($this->selectedConf['slideabble']) ) {
            $tmp = explode(",", $this->selectedConf['slideabble']);
            $this->_slideabbleFields = array();
            foreach ($tmp as  $no) {
                if (trim($no)=="") continue; //si vacio fuera
                if (preg_match("/\*/", $no)) { // si tiene asteriskos ...
                    $exp = str_replace("*", ".", $no);
                    foreach ($this->plantillaPrincipal->aFields as $field=>$foo) {
                        if (preg_match("/".$exp."/", $field)) {
                            $this->_slideabbleFields[] = $field;
                        }
                    }
                }
                $this->_slideabbleFields[] = $no;
            }
        }else $this->_slideabbleFields = array();
    }
    public function getslideabbleFields()
    {
        if ( ($this->_slideabbleFields==false
           || !is_array($this->_slideabbleFields))) {
               $this->loadslideabbleFields();
        }
        return $this->_slideabbleFields;
    }
    public function isslideabbleField($idx)
    {
        return (in_array($idx, $this->getslideabbleFields()));
    }

    protected function drawTableContents($cAux=false, $csv = false)
    {
        $pl = $this->plantillaPrincipal;
        $flagEntrada = true;
        $rAux = false;
        if ($this->_numResultados<1) {
            echo '<tr><td class="tablon_edit" colspan="2">'
                 . $this->l->l('No hay')
                 . self::getMLval($this->selectedConf['tit'], $this->selectedConf, 'tit') . '</td></tr>';
            iError::warn(
                $this->l->l("No hay")
                . self::getMLval($this->selectedConf['tit'], $this->selectedConf, 'tit')
            );
            return true;
        }
        while ($currentRow = $this->conPrinc->getResult()) {
            if ($flagEntrada) $flagEntrada = false;
            else {
                echo '<tr><td class="cabecera" colspan="2">&nbsp;</td></tr>';
            }
            if ($this->hasOptions()
               && isset($this->selectedConf['showoptions'])
               && $this->selectedConf['showoptions']=="1") {
                echo '<tr><td class="cabecera">'
                     . $this->l->l('Más opciones para ').' <strong>'
                     . $pl->getEntidad() . '</strong></td><td class="tablon_edit_options">'
                     . $this->drawOptions($currentRow['__ID'])
                     . '</td></tr>';
            }
            for ($i=0; $i < $pl->getNumFields(); $i++) {
                $tagConf = $pl->fields[$i]->getTagConf();
                if (isset($tagConf['cabecera_separator'])) {
                    if ($i == 0) {
                        echo '<tr><td class="cabecera">&nbsp;</td><td>&nbsp;</td></tr>';
                    }
                    $separator = explode('|', $tagConf['cabecera_separator']);
                    $separator_text = explode('|', $tagConf['cabecera_separator_text']);
                    if (count($separator) == count($separator_text)) {
                        foreach ($separator as $key => $sep) {
                            echo '<tr><td class="cabecera_'.$sep.'" colspan="2">'.$separator_text[$key].'</td></tr>';
                        }
                    }
                }

                if ($this->isNoShown($pl->fields[$i]->getIndex())) continue;
                $pl->fields[$i]->setCurrentID($currentRow['__ID']);
                if ($pl->fields[$i]->isclone()) {
                    echo '<tr>';
                    echo '<td class="cabecera">'
                         . $pl->fields[$i]->getAlias()
                         . (($pl->fields[$i]->isRequired())?' *':'')
                         .'</td>';
                    echo '<td id="'.basename($pl->getFile())
                         . '::' . $pl->fields[$i]->getSQLFLD()
                         . '::' . $currentRow['__ID'] . '_clone" ';
                    echo     ' class="tablon_edit"';
                    echo ' type="' . $pl->fields[$i]->getType().'"';
                    echo ' md5="' . md5($currentRow[$pl->fields[$i]->getAlias()]) .'"';
                    if ($pl->fields[$i]->hasHiddenConds()) {
                        echo ' dohide="true"';
                    }
                    echo '>';
                    echo '<div class="contValor">'
                         . $pl->fields[$i]->drawTableValueEdit($currentRow[$pl->fields[$i]->getAlias()], 'clone')
                         . '</div>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '<tr';
                if ($this->isslideabbleField($pl->fields[$i]->getIndex())) {
                    echo ' class="slidedTR"';
                    $extraTD = '';
                }
                echo '>';
                if ($pl->fields[$i]->getDescriptionTextForField() !== false) {
                    $toAddToTD = ' rowspan= "2" ';
                    $extraTD = '</td></tr><tr><td class="textoMenor textoIndent">'
                             . $pl->fields[$i]->getDescriptionTextForField().'';
                } else {
                    $toAddToTD = '';
                    $extraTD = '';
                }
                echo '<td class="cabecera">'
                     . $pl->fields[$i]->getAlias()
                     . (($pl->fields[$i]->isRequired())?' *':'')
                     . (($pl->fields[$i]->iscloneInfo())? ' '.$pl->fields[$i]->iscloneInfo():'');
                if ($this->isslideabbleField($pl->fields[$i]->getIndex())) {
                    echo ' <img  src="./icons/more.png" osrc="./icons/more.png" csrc="./icons/less.png"'
                         . ' class="buttonSlide" alt="slide" title="'
                         . $this->l->l("activar/desactivar editor").'" />';
                }

                $lres = array();
                if (isset($this->selectedConf['translate'])) {
                    $lres =  $this->translatefield($pl->fields[$i]->getSQLFLD());
                }

                if (isset($lres['0'])) {

                    echo ' <img  src="./icons/more.png" class="'.$lres['0'].'_edit_td1"  />';
                }

                echo '</td>';


                echo '<td id="' . basename($pl->getFile())
                     . '::' . $pl->fields[$i]->getSQLFLD()
                     . '::' . $currentRow['__ID'] . '" ';
                echo ' class="tablon_edit ';

                if (isset($lres['0'])) {
                    echo $lres['0']."_edit_td2";
                }
                echo '"  ' . ((isset($lres['1']))? 'lang="' . $lres['1'] . '"' : '')
                     . '  type="' . $pl->fields[$i]->getType() . '"';


                $md5 = '';
                if ($pl->fields[$i]->getType() == 'select') {
                    $json = $pl->fields[$i]->processReturnJSONValue(
                        $currentRow[$pl->fields[$i]->getAlias()],
                        $pl,
                        false
                    );
                    $md5 = md5($json);
                } else {
                    if (isset($currentRow[$pl->fields[$i]->getAlias()])) {
                        $md5 = md5($currentRow[$pl->fields[$i]->getAlias()]);
                    }
                }
                echo ' md5 ="' . $md5 . '"';

                if ($pl->fields[$i]->hasHiddenConds()) {
                    echo ' dohide="true"';
                }
                echo '>';
                echo '<div class="contValor">';
                if ($this->isNoEdit($pl->fields[$i]->getIndex())   || isset($this->selectedConf["onlyshow"])  ) {
                    $func = "drawTableValue";
                    echo "<p>".$pl->fields[$i]->$func($currentRow[$pl->fields[$i]->getAlias()])."</p>";

                } else {
                    $func = "drawTableValueEdit";

                    if ($pl->fields[$i]->hasSubFields()) {
                        for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {

                            $v = $currentRow[$pl->fields[$i]->subFields[$j]->getAlias()];
                            //var_dump($v);
                            $pl->fields[$i]->subFields[$j]->setValue($v);

                        }
                    }

                    if (isset($currentRow[$pl->fields[$i]->getAlias()])) {
                        echo $pl->fields[$i]->$func($currentRow[$pl->fields[$i]->getAlias()]);
                    } else {
                        echo $pl->fields[$i]->$func(null);
                    }

                }
                echo '</div>';
                echo '<div class="opsSave">';
                if (!$this->isNoEdit($pl->fields[$i]->getIndex())) {
                    echo '<img src="./icons/document-save.png" class="autosaver '
                         . ( ($pl->fields[$i]->isclone())? 'clone':'' )
                         . '" alt="'.$this->l->l('Salvar').'" />';
                    if (($pl->fields[$i] instanceof tablon_FLDimg || $pl->fields[$i] instanceof tablon_FLDfile)
                       && !($pl->fields[$i]->isRequired())
                    ) {
                        echo '<img src="./icons/delete.png" class="delete" alt="'.$this->l->l('borrar').'" />';
                    }
                    echo '<img src="./icons/undo.png" class="undo" alt="'.$this->l->l('deshacer').'"/>';
                    echo '<img src="./icons/loader.gif" class="loader" alt="'.$this->l->l('loading...').'"/>';
                    echo '<img src="./icons/cancel.png" class="cancel" alt="'.$this->l->l('error').'"/>';
                    echo '<p class="info"><img src="./icons/info.png" alt="'.$this->l->l('ok').'"/><span></span></p>';
                }
                echo '</div>';
                if (($cAux!=false)) {
                    if (isset($cAux[$i]))
                    echo "<div class=\"fright\"><p>".$cAux[$i]."</p></div>";
                    else echo "<div>&nbsp;</div>";
                }
                if ($pl->fields[$i]->getDescriptionTextForField() !== false) {
                    echo '<div class="clearboth textoIndent topmargin">'
                         . $pl->fields[$i]->getDescriptionTextForField().'</div>';
                }

                echo '</td>';

                echo '</tr>';



            }
            $rAux = $currentRow;
            $this->rAux = $currentRow;
            if ($this->hasOptions()
               && isset($this->selectedConf['showoptions'])
               && $this->selectedConf['showoptions']=="1") {
                echo '<tr><td class="cabecera">'
                     . $this->l->l('Más opciones para ') . ' <strong>'
                     . $pl->getEntidad()
                     . '</strong></td><td class="tablon_edit_options">'
                     . $this->drawOptions($currentRow['__ID']).'
                </td></tr>';
            }

        }
        echo '<tr ><td class="tablon_edit" colspan="2"></td></tr>';

        if ($rAux!==false) echo '<tr id="'.basename($pl->getFile()).'::'.$rAux['__ID'].'" >';
        else echo '<tr>';

        echo '<td colspan="2"></td></tr>';
    }


    public function drawGeneralOps()
    {


        echo '<ul id="optsTablon" class="tablon_edit_opts">';
        echo '<li class="optsIni">&nbsp;</li>';
        if (isset($this->selectedConf['del'])) {
            if (sizeof($this->_aIds)==1) {
                echo '<li class="optsTablonedit" src="./icons/_delete.png" ><a class="optsLink" title="'
                     . $this->l->l('Eliminar').'"  >';
                echo '<img src="./icons/_delete.png" class="'
                     . ((isset($this->rAux) && $this->rAux!==false)? 'deleteRow':'')
                     . '"  />';
                echo $this->l->l('Eliminar');
                echo '</a></li>';
            }
        }


        if ($this->_numResultados>0) {
            if (!isset($this->selectedConf['salvartodos']) || $this->selectedConf['salvartodos'] != "0") {
                echo '<li class="optsTablonedit"  id="saveAllButton" >
                        <a class="optsLink" title="'.$this->l->l('Salvar_Todos').'" >
                        <img  src="./icons/_guardar.png" alt="'.$this->l->l('Salvar_Todos').'"  />
                        '.$this->l->l('Salvar_Todos').'
                        </a>
                    </li>';
            }
            if (!isset($this->selectedConf['autosalvado']) || $this->selectedConf['autosalvado'] != "0") {
                echo '<li class="optsTablonedit"  id="autosaveLI" >
                        <input type="checkbox" id="autosaveButton" style="display:none;"/>
                        <a class="optsLink" title="'.$this->l->l('Auto-salvado').'" >
                        <img  src="./icons/_tic_off.png" selsrc="./icons/_tic_on.png"
                              noselsrc="./icons/_tic_off.png" alt="'.$this->l->l('Auto-salvado').'"  />
                        '.$this->l->l('Auto-salvado').'
                        </a>
                    </li>';
            }
        }

        echo '<li class="optsEnd">&nbsp;</li>';
        if (!isset($_GET['__nb__'])) {
            echo '<li class="opts backButton">'.$this->drawBackLink().'</li>';
        }
        echo '</ul>';


    }

    public function doAuxtd($c)
    {
        $pl = $this->plantillaPrincipalCompare;
        $cAux = array();
        while ($r = $c->getResult()) {
            for ($i=0;$i<$pl->getNumFields();$i++) {
                if ($this->isNoShown($pl->fields[$i]->getIndex())) continue;

                $cAux[] = ''.$pl->fields[$i]->drawTableValue($r[$pl->fields[$i]->getAlias()]).'';

            }

        }
        return $cAux;
    }

    protected function hasOptions()
    {

        return (
        isset($this->selectedConf['ops'])
        );
    }

    protected function drawOptions($id)
    {
        $ret = "";
        if ((isset($this->selectedConf['ops']))&&(sizeof($this->selectedConf['ops'])>0)) {
            foreach ($this->selectedConf['ops'] as $op=>$foo) {
                if (!isset($this->conf[$op])) {
                    iError::warn("No existe la sección [".$op."] en el fichero de configuración.");
                    continue;
                }
                $url = krm_menu::getURL();
                $url .= $this->getHistoryURL();
                $url .= 'tSec['.$this->currentSection.'::'.$op.']='.$id;
                $ret .= '<a href="'.$url.'" class="opts';
                if (isset($this->conf[$op]['markerClass'])) $ret .= ' '.$this->conf[$op]['markerClass'];
                $ret .= '" title="'.self::getMLval($this->conf[$op]['tit'], $this->conf[$op], 'tit').'" >';
                if (isset($this->conf[$op]['img'])) {
                    $ret .= '<img src="./icons/' . $this->conf[$op]['img'] . '"'
                         . ' alt="' . self::getMLval($this->conf[$op]['tit'], $this->conf[$op], 'tit') . '" />';
                }
                if (!isset($this->selectedConf['hide_label'])) {
                    $ret .= self::getMLval($this->conf[$op]['tit'], $this->conf[$op], 'tit');
                }

                $ret .= '</a>';
            }
        }
        /*if (isset($this->selectedConf['del'])) {
         $ret .= '<img src="./icons/eraser.png" alt="Borrar" class="deleteRow" />';
         }*/

        return $ret;
    }

    public function draw()
    {
        echo $this->drawHelpDesc();
        echo $this->drawTitle();
        if (!$plantilla = $this->getPlt()) {
            iError::error("Plantilla no encontrada");
            return false;
        }

        $this->plantillaPrincipal = new tablon_plantilla($plantilla);

        $cAux = false;
        if (isset($this->selectedConf['compareplt'])
           && (file_exists($this->rutaPlantillas.$this->selectedConf['compareplt']))) {
            $this->plantillaPrincipalCompare
                = new tablon_plantilla($this->rutaPlantillas.$this->selectedConf['compareplt']);
            $comp = new con($this->getSQL($this->plantillaPrincipalCompare));
            $cAux = $this->doAuxtd($comp);
        }

        $pl = $this->plantillaPrincipal;

        if ($pl->hasJs())
                while ($this->aJs[] = $pl->getJS());
        if ($pl->hasCss())
                while ($this->aCss[] = $pl->getCSS());

        echo '<div id="tablonContainer">';
        if (isset($this->selectedConf['translate'])) {

            $this->translate();
        }
        echo '<table id="tablon_edit" class="tablon" genero="'.$pl->getGenero().'" entidad="'.$pl->getEntidad().'">';
        if (!$this->doSQL()) {
            $this->drawErrors();
        } else {
            $this->drawTableContents($cAux);
        }
        echo '</table>';

        $this->drawGeneralOps($cAux);

        echo '<form id="hiddenFields">';
        if ( isset($this->selectedConf['logincond'])
           && in_array('update', explode('|', $this->selectedConf['logincondVConf']))) {
            echo '<input type="hidden" name="'.$this->selectedConf['logincond'].'" value="'.$_SESSION["__ID"].'" />';
        }

        if (isset($this->selectedConf['idTabcond'])) {
            if (in_array('updateTriggersOnly', explode('|', $this->selectedConf['idTabcondVConf']))) {
                echo '<input type="hidden" name="'
                     . $this->selectedConf['idTabcond'].'" value="'
                     . $this->currentValue.'" onlyTriggers="true"/>';
            } elseif (in_array('update', explode('|', $this->selectedConf['idTabcondVConf']))) {
                echo '<input type="hidden" name="'
                     . $this->selectedConf['idTabcond'].'" value="'
                     . $this->currentValue.'" />';
            }
        }
        if (isset($this->selectedConf['pltCond']) && isset($this->selectedConf['pltCondVConf'])) {
            $pltCondiciones = explode("|", $this->selectedConf['pltCond']);
            if (in_array('update', explode('|', $this->selectedConf['pltCondVConf']))) {
                if (sizeof($pltCondiciones)>0) {
                    for ($i=0;$i<sizeof($pltCondiciones);$i++) {
                        /* TODO mirar el igual y el distinto de en las condiciones*/
                        $fr = explode('is', $pltCondiciones[$i], 2);
                        if (isset($fr[1])&&trim($fr[1])=="null") {

                        } else {
                            if (strpos($pltCondiciones[$i], "=") !== false) {
                                list($c, $v)=explode('=', $pltCondiciones[$i], 2);
                                echo '<input type="hidden" name="'.$c.'" value="'.trim($v).'" />';
                            }
                            if (strpos($pltCondiciones[$i], "is") !== false) {
                                list($c, $v)=explode('is', $pltCondiciones[$i], 2);
                                echo '<input type="hidden" name="'.trim($c).'" value="'.trim($v).'" />';
                            }
                        }
                    }
                }
            }
        }
        echo '</form>';
        echo '</div>';


    }
    static function foo()
    {
    }
}
