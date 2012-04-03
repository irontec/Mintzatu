<?php
/**
 * Fichero principal de la clase tablon,
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

define("RUTA_PLANTILLAS", dirname(__FILE__)."/../../../configuracion/tablon/");
define("RUTA_HELP", dirname(__FILE__)."/../../../configuracion/tablon_help/");

class tablon extends contenidos
{
    protected $currentSection = 0;
    protected $currentFather = false;
    protected $currentValue = false;
    protected $lastValue = false;
    protected $history = array();
    /**
     * Objeto que contiene la consulta principal de tablón
     *
     * @var object objeto consulta a la base de datos.
     */
    protected $conPrinc = false;
    protected $noShowFields = false;
    protected $noEditFields = false;
    protected $noEditFieldsOnNew = false;
    protected $showLimitFields = false;
    protected $totalRows = 0;
    protected $notCurrentValue=false;

    protected $rutaPlantillas = RUTA_PLANTILLAS;

    public $backLink;
    public $aJs = array(
        "../modules/tablon/scripts/tablon.js",
        "../modules/tablon/scripts/jq.ajaxfileupload.js",
        "../modules/tablon/scripts/jeditable.js",
        "../modules/tablon/scripts/jq.taresizer.js",
        "../scripts/jquery/jquery.livequery.js",
        "../modules/tablon/scripts/jq.customFile.js",
        "../scripts/jquery/ui.datepicker.js",
        "../modules/tablon/scripts/tablon_multiedit.js",
        "../modules/tablon/scripts/jquery.mousewheel.js",
        "../modules/tablon/scripts/jquery.timepicker.js",
        "../modules/tablon/scripts/colorpicker.js"

    );
    public $aCss = array(
        "../modules/tablon/css/tablon.css", "../css/datepicker.css",
        "../modules/tablon/css/colorpicker.css",
        "../scripts/libs/jquery-ui-1.8/css/smoothness/jquery-ui-1.8.custom.css"
    );
    public $plantillaPrincipal = false;

    public $selectedConf = null;

    protected $l;


    protected $_emailSystem=false;
    /**
     * método constructor para tablón.
     *
     * @param array $conf datos provenientes del fichero de configuración
     */
    function __construct($conf)
    {
        $this->l = new k_literal(KarmaRegistry::getInstance()->get('lang'));
        if (K_DATELANG != "en") {
            $this->aJs[] = '../scripts/jquery/i18n/ui.datepicker-'.K_DATELANG.'.js';
        }

        $this->rutaPlantillas = dirname(__FILE__) . "/../../../configuracion/tablon/";
        $this->conf = $conf;
        $this->fixOps();
        $this->setCurrentSection();
        if (isset($this->selectedConf['translate'])) {
            $this->aJs[] = "../modules/tablon/scripts/jquery.translate-1.2.6.min.js";
        }
        $this->aJs[] = "../modules/tablon/scripts/date.js";
        $this->aJs[] = "../modules/tablon/scripts/jquery.autocomplete.js";
        $this->aJs[] = "../modules/tablon/scripts/tablon_dateformat.js";
    }

    /**
     * Inicializa el array de opciones de la sección
     */
    protected function fixOps()
    {
        foreach ($this->conf as $sec => $cont) {
            if ($sec === "main") {
                continue;
            }
            if (isset($cont['ops'])) {
                $aOps = explode(",", $cont['ops']);
                $this->conf[$sec]['ops'] = array();
                foreach ($aOps as $op) {
                    if (isset($this->conf[$op])) {
                        $this->conf[$sec]['ops'][$op] = true;
                    }
                }
            }
        }
    }

    protected function getLastValue()
    {
        /*SACA EL FAMOSO Y OLVIDADO IDPRECOND*/
        $arr = array_values($this->history);
        return isset($arr[sizeof($arr)-2]['vlr'])? $arr[sizeof($arr)-2]['vlr']:"0";
    }

    /**
     * Calcula y setea los valores de
     * 	$currentSection
     * 	$currentFather
     * 	$currentValue
     * 	$lastValue
     */
    protected function setCurrentSection()
    {
        if ((isset($_GET['tSec'])) && (is_array($_GET['tSec']))) {
            foreach ($_GET['tSec'] as $idx => $vlr) {
                list($sec, $op) = explode("::", $idx);
                if ( (isset($this->conf[$sec]['ops'][$op])) && (isset($this->conf[$op])) ) {

                    $this->history[$sec] = array('op' => $op, 'vlr' => $vlr);
                    $this->currentSection = $op;
                    $this->currentFather = $sec;
                    $this->currentValue = $vlr;
                    $this->lastValue = $this->getLastValue();
                }
            }
        }
        $this->selectedConf = $this->conf[$this->currentSection];
    }

    public function getCurrentClassName()
    {
        if (empty($this->selectedConf['class'])) {
            return false;
        } else {
            return $this->selectedConf['class'];
        }
    }



    protected function getActTitle()
    {
        global $kMenu;
        $link = "";
        foreach ($this->history as $sec => $cont) {
            $tit = '';
            $link .= '&amp;tSec['.$sec.'::'.$cont['op'].']='.$cont['vlr'];

            if (($miniplt = $this->_getPlt($sec))!==false) {
                $miniPlt = new tablon_plantilla($miniplt, true);
                $sql = 'select '.$miniPlt->getDefaultFLD().' from '.$miniPlt->getTab().' where ';
                $aIds = explode(",", $cont['vlr']);
                foreach ($aIds as $idx=>$id) {
                    $aIds[$idx] = $miniPlt->getID().'=\''.tablon_FLD::cleanMysqlValue($id).'\'';
                }
                $sql .= "(".implode(" or ", $aIds).")";
                $con = new con($sql);
                if (!$con->getError()) {
                    $aTits = array();
                    while ($r = $con->getResult()) {
                        $aTits[] = $r[$miniPlt->getDefaultFLD()];
                    }
                    $tit .= htmlentities(implode(", ", $aTits));
                } else {
                    iError::warn(
                        "Error obteniendo el defaultFLD (campo '"
                        . $miniPlt->getDefaultFLD()
                        . "') de la BBDD"
                    );
                }
            }
            return $tit;
        }
    }

    protected function drawHelpDesc()
    {
        return "";
    }

    public function enable_email_system()
    {
        $this->_emailSystem = true;
    }

    public function drawMigas()
    {
        global $kMenu;
        $link = "";
        echo "<div id='newMigas'>";
        if (($this->history) != NULL) {
            echo $this->l->l('Navigation').": ";
        }
        $tam = sizeof($this->history);
        $inc = 1;
        foreach ($this->history as $sec => $cont) {
            $tit = '<a href="?op='.$kMenu->selectedURL.$link.'">'.self::getMLval($this->conf[$sec]['tit'], $this->conf[$sec], 'tit').'</a> > ';
            $link .= '&amp;tSec['.$sec.'::'.$cont['op'].']='.$cont['vlr'];

            if (($miniplt = $this->_getPlt($sec))!==false) {
                $miniPlt = new tablon_plantilla($miniplt, true);
                $sql = "select ".$miniPlt->getDefaultFLD()." as defFLD from ".$miniPlt->getTab()." where ";
                $aIds = explode(",", $cont['vlr']);
                foreach ($aIds as $idx=>$id) {
                    $aIds[$idx] = $miniPlt->getID().'=\''.tablon_FLD::cleanMysqlValue($id).'\'';
                }
                $sql .= "(".implode(" or ", $aIds).")";
                $con = new con($sql);

                                if (!$con->getError()) {
                                    $aTits = array();
                                    while ($r = $con->getResult()) {
                                            $aTits[] = utf8_decode($r['defFLD']);
                                    }
                                    $tit .= htmlentities(implode(", ", $aTits));
                                } else {
                                    iError::warn(
                                        "Error obteniendo el defaultFLD (campo '"
                                        . $miniPlt->getDefaultFLD()
                                        . "') de la BBDD"
                                    );
                                }
            }
            echo $this->_drawTitle($tit);
            if ($inc != $tam) {
                $inc++; echo " > ";
            }
        }
        $this->backLink = '?op='.$kMenu->selectedURL.$link;
        echo $this->_drawTitle('<span class="tituloFinal">'.self::getMLval($this->selectedConf['tit'], $this->selectedConf, 'tit').'</span>');
        echo '</div>';
    }

    protected function drawTitle()
    {
        global $kMenu;
        return "";
    }

    protected function _getPlt($sec)
    {
        if (!file_exists($this->rutaPlantillas.$this->conf[$sec]['plt'])) {
            return false;
        }
        return $this->rutaPlantillas.$this->conf[$sec]['plt'];
    }

    protected function getPlt()
    {
        return $this->_getPlt($this->currentSection);
    }

    protected function hasOptions()
    {
        return ($this->_isDeletable() || (isset($this->selectedConf['ops'])));
    }
    protected function isPlaceEditable()
    {
        return (isset($this->selectedConf['self_editable']) && $this->selectedConf['self_editable']);
    }
    protected function _isDeletable()
    {
        return (isset($this->selectedConf['del']) && $this->selectedConf['del']);
    }

    protected function getHistoryURL($limit = 0)
    {
        $ret = '';
        if (sizeof($this->history)==0) return false;
        $limitHist = sizeof($this->history)-$limit;

        foreach ($this->history as $sec=>$aSec) {
            if ($limitHist==0) break;
            $limitHist--;
            $ret .= 'tSec['.$sec.'::'.$aSec['op'].']='.$aSec['vlr'].'&amp;';
        }
        return $ret;
    }

    protected function drawOptions($id)
    {
        $ret = "";
        $i=0;
        if ((isset($this->selectedConf['ops']))&&(sizeof($this->selectedConf['ops'])>0)) {
            foreach ($this->selectedConf['ops'] as $op=>$foo) {
                if (!isset($this->conf[$op])) {
                    iError::warn("No existe la sección [".$op."] en el fichero de configuración.");
                    continue;
                }

                if ((isset($this->conf[$op]['class'])) &&($this->conf[$op]['class'] == "tablon_multiedit")) {
                    continue; /*LANDER*/
                }
                $i++;
                $url = krm_menu::getURL();
                $url .= $this->getHistoryURL();
                $url .= 'tSec['.$this->currentSection.'::'.$op.']='.$id;
                $ret .= '<a href="'.$url.'" class="opts';
                if (isset($this->conf[$op]['markerClass'])) $ret .= ' '.$this->conf[$op]['markerClass'];

		$isPopup = false;
		if ((isset($this->conf[$op]['type'])) && ($this->conf[$op]['type'] == "popup") ) {
			$isPopup = true; // luego habrá que meter más atributos al enlace
			$ret .= " popup";
		}

        if (isset($this->conf[$op]['plt'])) $ret .= '" id="'.$this->conf[$op]['plt'].'::'.$id;
        $ret .= '" title="'.self::getMLval($this->conf[$op]['tit'], $this->conf[$op], 'tit').'" ';
		if ($isPopup) {
			$ret .= ' rel="'.$this->conf[$op]['class'].'" relID="'.$id.'" ';
		}
		
		//IVOZ-NG 
		if (isset($this->selectedConf['logingrupocond']) && $_SESSION["__ID"]!='1')
			$ret .= ' id_grupo="'.$_SESSION["__GRUPO_VINCULADO"].'" ';

		$ret .= '>';
                if (isset($this->conf[$op]['img'])) {
                    $ret .= '<img src="./icons/'.$this->conf[$op]['img'].'" alt="'.self::getMLval($this->conf[$op]['tit'], $this->conf[$op], 'tit').'" />';
                }
                if (!isset($this->selectedConf['hide_label'])) {
                    $ret .= self::getMLval($this->conf[$op]['tit'], $this->conf[$op], 'tit');
                }

                $ret .= '</a>';
            }
        }

        if (isset($this->selectedConf['openView'])) { //LANDER
            $i++;
            $ret .= '<img src="./icons/viewmag.png" alt="openView" id="'
                 . $this->selectedConf['openView'].'" class="openView" />';
        }

        if (isset($this->selectedConf['preView'])) { //LANDER
            $i++;
            $ret .= '<a target="__blank" href="'
                 . $this->selectedConf['preView']
                 . $id.'" class="nomove"> <img src="./icons/viewmag.png"  /></a>';
        }

        if (isset($this->selectedConf['newFrom'])) { //EIDER
            $i++;
            $url = "new::".$this->selectedConf['newFrom']."::".$id;
            if (isset($this->selectedConf['idcond'])) {
                $url .= "::".$this->selectedConf['idcond']." = '".$this->currentValue."'";
            }
            $ret .= '<img src="./icons/copy.png" alt="new from this" title="new from this" id="'
                 . $url.'" class="duplicateEntity" />';
        }

        if ($this->_isDeletable()) {
            $i++;
            $exCss = "";
            if (isset($this->selectedConf['delreconfirm'])) {
                $exCss = " reconfirm ";
            }
            $ret .= '<img src="./icons/eraser.png" alt="'
                 . $this->l->l('Eliminar').'" title="'
                 . $this->l->l('Eliminar').'" class="deleteRow nomove'
                 . $exCss.'" />';
        }

        if ($i<=0) return false;
        return $ret;
    }

    protected function getPag($absolute = false)
    {
        // Si existe límite manual, lo invoco desde aquí y sobreescribo desde ya el limit original
        $this->selectedConf['limit'] = $this->getManualLimit();

        if (isset($_GET['pag'])) {
            $cPage = (int)$_GET['pag'];
            if ($cPage == 0) return 0;
            if ($absolute) return ($cPage);
            return $this->selectedConf['limit']*($cPage-1);
        }
        return 0;
    }


    protected function buildSearchConds()
    {
        $aConds = array();
        $aHaving = array();
        $aCondSearch = array();
        $aHaveSearch = array();

        if (!isset($_SESSION['search'][$this->currentSection])) {
            $_SESSION['search'][$this->currentSection] = array();
        }
        if (!isset($_SESSION['search'][$this->currentSection][$this->currentFather])) {
            $_SESSION['search'][$this->currentSection][$this->currentFather] = array();
        }
        if (!isset($_SESSION['search'][$this->currentSection][$this->currentFather][$this->currentValue])) {
            $_SESSION['search'][$this->currentSection][$this->currentFather][$this->currentValue] = array();
        }

        $sess = &$_SESSION['search'][$this->currentSection][$this->currentFather][$this->currentValue];
        if ((isset($_POST['submit'])) && ($_POST["submit"] == 'cancel') ) {
            $sess = array();
            return(array($aConds, $aHaving));
        }

        $pl = $this->plantillaPrincipal;
        $searchFields = explode(",", $this->selectedConf['search']);

        for ($i=0; $i < $pl->getNumFields(); $i++) {
            if (!in_array($pl->fields[$i]->getIndex(true), $searchFields)) {
                continue;
            }
            $currentFldSearch = $pl->fields[$i]->getSQLFLDSearch();

            $var = '';
            if (isset($_REQUEST[$currentFldSearch])) {
                $var = $_REQUEST[$currentFldSearch];
            } else if (isset($sess[$currentFldSearch])) {
                $var = $sess[$currentFldSearch];
            }

            if ( ($var != "") && ($var != NULL)  && ($var != "NULL") ) {
                $pl->fields[$i]->setSearchValue($var);
                $sess[$currentFldSearch] = $pl->fields[$i]->getSearchValue();
                $searchField = $pl->fields[$i]->getSearchOp();
                if (is_array($searchField)) {
                    $currentCond = "("
                                 . $pl->fields[$i]->getSQLFLDSearch(true) . ' ' . $searchField['op'] . ' '
                                 . implode(
                                        " or " . $pl->fields[$i]->getSQLFLDSearch(true) . ' '.$searchField['op'].' ',
                                        $searchField['vals']
                                   )
                                 . ")";
                } else {
                    $currentCond = $pl->fields[$i]->getSQLFLDSearch(true) . $searchField;
                }
                switch ($pl->fields[$i]->getSearchVarType()) {
                    case 'aCondSearch':
                        $aCondSearch[] = $currentCond;
                        break;
                    case 'aHaveSearch':
                        $aHaveSearch[] = $currentCond;
                        break;
                }
            } else {
                if (isset($sess[$currentFldSearch])) {
                    unset($sess[$currentFldSearch]);
                }
            }
        }

        if (sizeof($aCondSearch)>0) {
            $aConds[] = ' ('.implode(' and ', $aCondSearch).')';
        }
        if (sizeof($aHaveSearch) > 0) {
            $aHaving[] = ' ('.implode(' and ', $aHaveSearch).')';
        }
        return array($aConds, $aHaving);
    }

    protected function buildConds()
    {

        $aConds = array();
        $aHaving = array();
        $pl = $this->plantillaPrincipal;
        if ($del = $pl->getDeletedFLD()) {
            $aConds[] = ' '.$pl->getTab().'.'.$del.'=\'0\'';
        }
        if (isset($this->selectedConf['idcond']) && $this->notCurrentValue===false) {
            //inteligencia
            
        		if (strpos('.', $this->selectedConf['idcond'])>=0) {
        			$aConds[] = ' '.$this->selectedConf['idcond'].'=\''.$this->currentValue.'\'';		
        		} else {
        			$aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['idcond'].'=\''.$this->currentValue.'\'';
        		}
        	
            
        }
        if (isset($this->selectedConf['idprecond'])) {
            $aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['idprecond'].'=\''.$this->lastValue.'\'';
        }
        if (isset($this->selectedConf['logincond'])
           && in_array('select', explode('|', $this->selectedConf['logincondVConf']))
           && $_SESSION["__ID"]!='1' ) {
            $aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['logincond'].'=\''.$_SESSION["__ID"].'\'';
        }
	//IVOZ-NG 
	if (isset($this->selectedConf['logingrupocond']) && $_SESSION["__ID"]!='1' && isset($_SESSION['__GRUPO_VINCULADO'])){
            $aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['logingrupocond'].'=\''.$_SESSION["__GRUPO_VINCULADO"].'\'';
        }
        if (isset($this->selectedConf['hideifnotadmin'])
           &&($this->selectedConf['hideifnotadmin']!=$_SESSION["__ID"])) {
            $aConds[] = ' '.$pl->getTab().'.'.$pl->getId().'!=\''.$this->selectedConf['hideifnotadmin'].'\'';
        }
        if (isset($this->selectedConf['hideifnotroladmin'])
           && !in_array($this->selectedConf['hideifnotroladmin'], $_SESSION["__IDROL"])) {
            $aConds[] = ' '.$pl->getTab().'.'.$pl->getId().'!=\''.$this->selectedConf['hideifnotroladmin'].'\'';
        }
        if (isset($this->selectedConf['logincond_tabla'])) {
            $aCmps = explode(",", $this->selectedConf['logincond_tabla']);
            $aCondTmp = array();
            foreach ($aCmps as $cmp) $aCondTmp[] = $cmp.'=\''.$_SESSION["__ID"].'\'';
            $aConds[] = '('.implode(' or ', $aCondTmp).')';
        }
        if (method_exists($this, "getCustomConds")) {
            $aCustomConds = $this->getCustomConds();
            foreach ($aCustomConds as $f) $aConds[] .= " ".$f;
        }
        if (isset($this->selectedConf['pltCond'])) {
            $pltCondiciones = explode("|", $this->selectedConf['pltCond']);
            if (isset($this->selectedConf['pltCondVConf'])) {
                if (in_array('select', explode('|', $this->selectedConf['pltCondVConf']))) {
                    if (sizeof($pltCondiciones)>0) {
                        for ($i=0; $i < sizeof($pltCondiciones); $i++) {
                            $aConds[] = ' '.$pl->getTab().'.'.$pltCondiciones[$i];
                        }
                    }
                }
            } else {
                if (sizeof($pltCondiciones)>0) {
                    for ($i=0; $i < sizeof($pltCondiciones); $i++) {
                        $aConds[] = ' '.$pl->getTab().'.'.$pltCondiciones[$i];
                    }
                }
            }
        }

        /* Lo mismo que pltCond, pero con condiciones que no son de la tabla del plt actual, sino de otra tabla */
        if (isset($this->selectedConf['pltTabCond'])) {
            $pltCondiciones = explode("|", $this->selectedConf['pltTabCond']);
            if (isset($this->selectedConf['pltTabCondVConf'])) {
                if (in_array('select', explode('|', $this->selectedConf['pltTabCondVConf']))) {
                    if (sizeof($pltCondiciones)>0) {
                        for ($i=0; $i < sizeof($pltCondiciones); $i++) {
                            $aConds[] = ' '.$pltCondiciones[$i];
                        }
                    }
                }
            } else {
                if (sizeof($pltCondiciones)>0) {
                    for ($i=0; $i < sizeof($pltCondiciones); $i++) {
                        $aConds[] = ' '.$pltCondiciones[$i];
                    }
                }
            }
        }
        if (isset($this->selectedConf['idTabcond'])) {
            $aConds[] = ' '.$this->selectedConf['idTabcond'].'=\''.$this->currentValue.'\'';
        }
        if (isset($this->selectedConf['idTabprecond'])
           && in_array('select', explode('|', $this->selectedConf['idTabprecondVConf']))) {
            $aConds[] = ' '.$this->selectedConf['idTabprecond'].'=\''.$this->lastValue.'\'';
        }
        if (isset($this->selectedConf['fieldCondFather'])
           && isset($this->selectedConf['fieldCondSon'])
           && isset($this->selectedConf['fatherTab'])
           && isset($this->selectedConf['fatherId'])) {
            $aConds[] = ' ' . $pl->getTab() . '.'
                        . $this->selectedConf['fieldCondSon']
                        . '= (select '.$this->selectedConf['fieldCondFather']
                        . ' from '.$this->selectedConf['fatherTab']
                        . ' where '
                        . $this->selectedConf['fatherId'].' = '.$this->currentValue.' limit 1 ) ';
        }
        if (isset($this->selectedConf['search'])) {
            list($aConds2, $aHaving2) = $this->buildSearchConds();
            $aConds = array_merge($aConds, $aConds2);
            $aHaving = array_merge($aHaving, $aHaving2);
        }
        return array(
                        ((sizeof($aConds)>0)? " where " . implode(' and ', $aConds) : false),
                        ((sizeof($aHaving)>0)? " having " . implode(' and ', $aHaving) : false)
                    );
    }

    protected function getSQL(&$pl = false, $nofields = false)
    {
        if ($pl === false) {
            $pl = $this->plantillaPrincipal;
        }
        $sql = "select SQL_CALC_FOUND_ROWS ";
        if ($pl->getDistinct() === true) {
            $sql .= " distinct ";
        }
        $pajar = array(
          'GHOST',
          'DATE',
          'TAGS',
          'MULTISELECT',
          'MULTISELECTNG',
          'MULTISELECTIVOZ',
	  'MULTISELECTIVOZNOREAL',
          'MULTISELECTEXTERNAL',
          'MULTISELECTNOREAL',
          'MULTISELECTMULTI',
          'DATETIME'
       );

        $sql .= $pl->getTab() . "." . $pl->getID() . " as __ID";

        if (method_exists($this, "getCustomSelectFields")) {
            $aFields = $this->getCustomSelectFields();
            foreach ($aFields as $f) $sql .= ", " . $f;
        }

        for ($i=0;$i<$pl->getNumFields();$i++) {

            if (in_array($pl->fields[$i]->getIndex(), $this->getNoShownFields())) {
                continue;
            }

            if (!$pl->fields[$i]->getInSQL() ) {
                $sql .= ", " . $pl->fields[$i]->getSQL($pl->getTab());
                continue;
            }

            /*
             * Si el campo tiene el atributo "fromAnotherTab" a true,
             * obtener su sql sin la tabla del plt actual por delante ya que viene de otra tabla.
             */
            if ($pl->fields[$i]->ifFieldOfAnotherTab()) {
                $sql .= ", ".$pl->fields[$i]->getSQL($pl->getTab());
                continue;
            }

            if ($tmpSql = $pl->fields[$i]->getSQL($pl->getTab())) {
                $sql .= ", ".((!in_array($pl->fields[$i]->getRealType(), $pajar))? $pl->getTab() . "." : "");
                $sql .= $tmpSql;
            }

            //Las imagenes y los ficheros tienen subfields, también los añadimos a la select...

            if ($pl->fields[$i]->hasSubFields()) {
                for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
                    if ($tmpSql = $pl->fields[$i]->subFields[$j]->getSQL($pl->getTab())) {
                        $sql .= "," . $tmpSql;
                    }
                }
            }
        }
        $sql .= ' FROM ' . $pl->getTab();
        $leftTabs = $pl->getALeftTabs();

        if (isset($leftTabs) && is_array($leftTabs) && !empty($leftTabs)) {
            $leftTab = $leftTabs['lefttab'];
            $leftCond = $leftTabs['leftcond'];
            $leftWhere = $leftTabs['leftwhere'];

            if (sizeof($leftTab)>0 && sizeof($leftCond)>0 && sizeof($leftTab)==sizeof($leftCond)) {
                for ($i=0; $i < sizeof($leftTab); $i++) {
                    if ($i!=0) {
                        $sql .= " ";
                    }
                    $sql .= " LEFT JOIN " . $leftTab[$i] . " ON (" . $leftCond[$i] . ") ";
                    if ($this->selectedConf['class'] == 'tablon_multirelsearchable' && isset($this->selectedConf['extrafield'])) {
                        $sql .= ' AND '.$leftTab[0].'.'.$this->selectedConf['extrafield'].' = \''.$this->selectedConf['extravalue'].'\'';
                    }
                }
            }
        }

        $aCondicionesmasaHavings = $this->buildConds();
        if (is_array($aCondicionesmasaHavings)) {
            list($condiciones, $having) = $aCondicionesmasaHavings;
        } else {
            // Solo conds normales, no having...
            $condiciones = $aCondicionesmasaHavings;
            $having=false;
        }

        if (sizeof($leftWhere)>0) {
            $preWord = ($condiciones == "")? "WHERE" : "AND";
            foreach ($leftWhere as $l) {
                if ($l == "" || !$l) continue;
                $condiciones .= ' ' . $preWord . ' '.$l;
                $preWord = "AND";
            }
        }
        $sql .= $condiciones;

    // WTF?!?!?!??!
    /*    if (isset($this->selectedConf['group'])    ) {
                $sql .= ' group by '.$this->selectedConf['group'].' ';
        }*/

        if (($pl->getGroupBy()!==false) || (isset($this->selectedConf['group'])) ) {
            if ((isset($this->selectedConf['group'])) && (trim($this->selectedConf['group'])!="" )) {
                $gr = $this->selectedConf['group'];
            }
            if (trim($pl->getGroupBy()!="")) {
                $gr = $pl->getGroupBy();
            }
            $sql .= " group by  ".$gr;
        }
        if ($having!== false) {
            $sql .= " $having ";
        }
        if ((isset($_GET['order'])) && (isset($pl->fields[(int)$_GET['order']])) ) {

             $searchFld = $pl->fields[(int)$_GET['order']]->getIndex();
             if (method_exists($pl->fields[(int)$_GET['order']], "getCl")) {
                if ($pl->fields[(int)$_GET['order']]->getCl() == "ghost") $searchFld = "`".$pl->fields[(int)$_GET['order']]->getAlias()."`";
	     }

            $sql .= ' order by '.$searchFld;
            if ((isset($_GET['orderType'])) && ($_GET['orderType']=="desc")) {
                $sql .= ' desc ';
            }

        } elseif ($pl->getOrderBy() || isset($this->selectedConf['order'])) {

            if (isset($this->selectedConf['order'])    ) {
                $sql .= ' order by '.$this->selectedConf['order'].' ';
            } else {
                $sql .= ' order by '.$pl->getOrderBy().' ';
            }
        }


        if (isset($this->selectedConf['limit']) && !isset($_GET['CSV']) ) {
            $sql .= ' limit '.$this->getPag().",".$this->selectedConf['limit'];
        }
        if (isset($_GET['DEBUG'])) iError::warn("<textarea>".$sql."</textarea>");

        return $sql;
    }

    protected function getCreateSQL()
    {
        $pl = $this->plantillaPrincipal;
        $sql = "create table ".$pl->getTab()."(\n".$pl->getID()." mediumint unsigned not null auto_increment";
        for ($i=0;$i<$pl->getNumFields();$i++) {
            if ($pl->fields[$i]->getSQLType()!==false) { // comprobación para campos ghost
                $sql .= ",\n".$pl->fields[$i]->getSQL("", false)." ".$pl->fields[$i]->getSQLType();
                if ($pl->fields[$i]->hasSubFields()) {
                    for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
                        //TODO campos dependientes
                    }
                }
            }
        }
        if ($del = $pl->getDeletedFLD()) {
            $sql .= ",\n".$del." enum('0','1') not null default '0'";

        }
        $sql .= ",\nprimary key(".$pl->getID().")\n) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        return $sql;
    }

    protected function doSQL()
    {
        $this->conPrinc = new con($this->getSQL());
        if ($this->conPrinc->error()) return false;
        $cCont = new con("SELECT FOUND_ROWS() as cont");
        $rCont = $cCont->getResult();
        $this->totalRows = $rCont['cont'];
        return true;
    }

    /*
     * Dibuja el cuerpo de la tabla "tablon"
     */
    protected function drawTableContents($checkBox = false, $csv = false)
    {
        $pl = $this->plantillaPrincipal;
        $par = 0;

        while ($currentRow = $this->conPrinc->getResult()) {
            echo '<tr';
            echo ($par%2==0)?' class="par"':' class="impar"';
            $par++;
            if (!$csv) echo ' id="'.$pl->getBaseFile().'::'.$currentRow['__ID'].'" ';
            echo '>';

            if ((isset($this->selectedConf['numlines'])) && (!$csv)) {
                echo "<td class=\"numline\">$par</td>";
            }

            if ($checkBox) {
                if (is_string($checkBox)) { // Estoy en multiselect, lo dibujo por cojones
                    echo '<td index="0" class="multiselect" id="ms::'.$currentRow['__ID'].'">';
                    echo '<input type="checkbox"';
                    if ($currentRow[$checkBox] != NULL) echo ' checked="checked" value="'.$currentRow[$checkBox].'"';
                    echo '  /></td>';
                } else {
                    if (!$csv) { // tablon normal, dibujo si no estoy en modo csv
                        echo '<td index="0" class="multiselect" id="ms::'.$currentRow['__ID'].'">';
                        echo '<input type="checkbox" /></td>';
                    }
                }
            }

            $cont = 1;
            for ($i=0;$i<$pl->getNumFields();$i++) {
                if ($this->isNoShown($pl->fields[$i]->getIndex(true))) continue;
                if ($csv && $this->isNoCSV($pl->fields[$i]->getIndex(true))) continue;

                $pl->fields[$i]->setCurrentID($currentRow['__ID']);
                echo '<td index="'.($cont).'" ';
                $cont++;
                if (!$csv) {
                    echo ' id="'.basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$currentRow['__ID'].'" class="';
                }
                $lres = array();
                if (isset($this->selectedConf['translate'])) {
                        $lres =  $this->translatefield($pl->fields[$i]->getSQLFLD());

                }
                if (($this->isPlaceEditable())  && (!$this->isNoEdit($pl->fields[$i]->getIndex())) && (!$csv)) {

                    if ($pl->fields[$i]->getCl()&&(trim($pl->fields[$i]->getCl())!="")) {
                         echo $pl->fields[$i]->getCl();
                    } else {
                        echo 'editable ';
                        if (isset($lres['0'])) {
                            echo $lres['0'];
                        }
                    }
                    if ($pl->fields[$i]->isRequired()) {
                        echo ' required ';
                    }
                    if ($pl->fields[$i]->getOCl()) {
                        echo ' '.$pl->fields[$i]->getOCl().' ';
                    }
                    if ($pl->fields[$i]->getDependencia()!=false) {
                        echo ' '.$pl->fields[$i]->getDependencia().' ';
                    }
                    echo ' "';
                    $conf = $pl->fields[$i]->getTagConf();
                    if (isset($conf['max'])) {
                        echo ' maxlength="'.$conf['max'].'"';
                    }
                    echo ' type="' . $pl->fields[$i]->getType().'"  '
                         . ((isset($lres['1']))? 'lang="'.$lres['1'].'"':'').' >';
                } else {
                    if (isset($lres['0'])) {
                        echo $lres['0'];
                    }
                    if (!$csv) {
                        echo '">';
                    } else {
                        echo '>';
                    }
                }


                if ($pl->fields[$i]->hasSubFields()) {
                    for ($j=0; $j < $pl->fields[$i]->sizeofsubFields; $j++) {
                        $value = $currentRow[$pl->fields[$i]->subFields[$j]->getAlias()];
                        $pl->fields[$i]->subFields[$j]->setValue($value);

                    }
                }

                if ($this->isShowLimit($pl->fields[$i]->getIndex())) {
                    echo text_utils::text_limit(
                        $pl->fields[$i]->drawTableValue($currentRow[$pl->fields[$i]->getAlias()]),
                        $this->showLimitFields[$pl->fields[$i]->getIndex()],
                        "..."
                    );
                } else {
                    if (isset($currentRow[$pl->fields[$i]->getAlias()])) {
                        if (!$csv) { 
                            echo $pl->fields[$i]->drawTableValue($currentRow[$pl->fields[$i]->getAlias()]);
                        } else {
                            echo utf8_decode($pl->fields[$i]->drawTableValue($currentRow[$pl->fields[$i]->getAlias()]));
                        }
                    } else {
                        echo $pl->fields[$i]->drawTableValue(null);
                    }
                }
                echo '</td>';
                for ($j=0; $j < $pl->fields[$i]->sizeofsubFields; $j++) {

                    if (in_array($pl->fields[$i]->subFields[$j]->getIndex(), $this->getNoShownFields())) continue;
                    echo '<td index="'.$cont.'" ';
                    $cont++;
                    if (!$csv) {
                       echo ' id="'.basename($pl->getFile())
                            . '::' . $pl->fields[$i]->subFields[$j]->getSQLFLD()
                            . '::' . $currentRow['__ID'].'"';
                    }
                    echo '>';
                    echo $pl->fields[$i]
                            ->subFields[$j]
                            ->drawTableValue($currentRow[$pl->fields[$i]->subFields[$j]->getAlias()], $currentRow[$pl->fields[$i]->getAlias()])
                        . '</td>';
                }
            }
            if (($this->hasOptions()) && (!$csv)) {
                if ($opts = $this->drawOptions($currentRow['__ID'])) echo '<td>'.$opts.'</td>';
            }
            echo '</tr>';
        }
        if ((!$csv) && (isset($this->selectedConf['totales'])) ) {
            echo '<tr class="totales">';
            for ($i=0;$i<$pl->getNumFields();$i++) {
                if ($this->isNoShown($pl->fields[$i]->getIndex())) continue;
                echo '<td>' . $pl->fields[$i]->getTotal() . '</td>';
            }
            if (($this->hasOptions()) && (!$csv)) {
                if ($opts = $this->drawOptions($currentRow['__ID'])) echo '<td>'.$opts.'</td>';
            }
            echo '</tr>';
        }
        if (!$csv) $this->drawTRModel($checkBox);
    }

    /*
     * Dibuja TRModel, que luego se utiliza como "plantilla"
     * para ser clonado al añadir nuevas filas en la tabla "tablon"....
     */
    protected function drawTRModel($checkBox, $direct=false)
    {
        $pl = $this->plantillaPrincipal;
        $ret = "";
        $ret.= '<tr id="trModel" class="tablonClone" >';
        if ($checkBox) {
            $ret.= '<td class="multiselect" id="ms::%id%"><input type="checkbox" /></td>';
        }

        for ($i=0; $i < $pl->getNumFields(); $i++) {
            if (in_array($pl->fields[$i]->getIndex(), $this->getNoShownFields())) continue;
            if ($this->isNoShown($pl->fields[$i]->getIndex())) continue;
            $ret.= '<td id="'.basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::%id%" class="';
            if (($this->isPlaceEditable())  && (!$this->isNoEdit($pl->fields[$i]->getIndex()))) {
                if ($pl->fields[$i]->getCl()&&(trim($pl->fields[$i]->getCl())!="")) $ret.= $pl->fields[$i]->getCl();
                else{
                    $ret.= 'editable  ';
                }
                $ret.= ' "';
                $ret.= ' type="' . $pl->fields[$i]->getType().'">';
            } else {
                $ret.= '">';
            }
            $ret.= '</td>';
            for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
                if (in_array($pl->fields[$i]->subFields[$j]->getIndex(), $this->getNoShownFields())) continue;
                $ret.= '<td id="'.basename($pl->getFile())
                       . '::'.$pl->fields[$i]->subFields[$j]->getSQLFLD()
                       . '::%id%"></td>';
            }
        }
        if ($this->hasOptions()) {
            if ($opts = $this->drawOptions('%id%') ) $ret.= '<td>'.$opts.'</td>';
        }
        $ret.= '</tr>';
        if (!$direct) echo $ret;
        else return $ret;
    }

    protected function drawSearcher()
    {
        if (isset($_GET['DEBUG'])) {
            echo '<form action="'.krm_menu::getURL().$this->getHistoryURL().'&DEBUG='.$_GET['DEBUG'].'" method="post">';
        } else {
            echo '<form action="'.krm_menu::getURL().$this->getHistoryURL().'" method="post">';
        }
        echo '<input type="hidden" name="do" value="search" />';
        echo '<input type="hidden" name="manualLimit" value="0" />';
        echo '<div id="tablonsearchnew" >';
        echo '<table id="tablonsearchnewtable" class="tablon">';
        echo '<tr class="tablon_tr_header"><th class="colspaned" colspan="2">';

        echo '<a id="__hide_filtrado" class="actionOpenClose"><img src="./icons/viewmag-.png" />'
             . $this->l->l('Ocultar Filtrado').'</a>';
        echo '</th></tr>';
        $pl = $this->plantillaPrincipal;
        $searchFields = explode(",", $this->selectedConf['search']);
        for ($i=0;$i<$pl->getNumFields();$i++) {
            if (!in_array($pl->fields[$i]->getIndex(true), $searchFields)) continue;
            $val = $pl->fields[$i]->getSearchValue();

            echo '<tr><td class="">'.$pl->fields[$i]->getAlias().'</td>';
            echo '<td';
            if ($pl->fields[$i]->searchValue!="") echo ' class="used"';
            echo ' class="box"';
            echo '>';

            if ($pl->fields[$i] instanceof tablon_FLDenum) {
                $pl->fields[$i]->setNullable($this->l->l("Seleccionar"));
                $pl->fields[$i]->setDefault(false);
            }
            if (method_exists($pl->fields[$i], "setEditForSearch")) {
                $pl->fields[$i]->setEditForSearch(true);
            }
            echo $pl->fields[$i]->drawTableValueEdit($val);
            if ( ($helpSearchStr = $pl->fields[$i]->getSearchHelp()) !== false) {
                echo '<img class="helpsearch" src="./icons/info.png" title="'
                     . str_replace("\"", "'", $helpSearchStr).'" />';

            }
            if (method_exists($pl->fields[$i], "setEditForSearch")) $pl->fields[$i]->setEditForSearch(false);
            echo '</td></tr>';
        }
        echo '<tr><td colspan="2" class="colspaned act centrar">'
             . '<p class="nopadding left"><button name="submit" value="search">'
             . '<img src="./icons/viewmag.png" />&nbsp;'
             . $this->l->l("Buscar")
             . '</button><button name="submit" value="cancel">'
             . '<img src="./icons/cancel.png" />&nbsp;'
             . $this->l->l('Cancelar Filtrado')
             . '</button></p></td></tr>';
        echo '</table>';
        echo '</div>';
        if (method_exists($this, "showOnlySelected")) {
            echo '<input type="hidden" name="mrs_only_selected" value="'.(($this->showOnlySelected())? ' 1':'0').'" />';
        }
        echo '</form>';
    }

    public function loadNoShownFields()
    {
        if (isset($this->selectedConf['noshow']) || isset($this->selectedConf['show'])) {
            $realNoShowFields = array();
            $calculatedNoShowFields = array();
            if (isset($this->selectedConf['noshow'])) {
                $tmpnoshow = explode(",", $this->selectedConf['noshow']);
                foreach ($tmpnoshow as  $no) {
                    if (trim($no)=="") continue; //si vacio fuera
                    if (preg_match("/\*/", $no)) { // si tiene asteriskos ...
                        $exp = str_replace("*", ".", $no);
                        foreach ($this->plantillaPrincipal->aFields as $field=>$foo) {
                            if (preg_match("/".$exp."/", $field)) {
                                $realNoShowFields[] = $field;
                            }
                        }
                    }
                    $realNoShowFields[] = $no;
                }
            }
            if (isset($this->selectedConf['show'])) {
                $aShow = explode(",", $this->selectedConf['show']);
                foreach ($this->plantillaPrincipal->aFields as $field=>$foo) {
                    if (!in_array($field, $aShow)) {
                        $calculatedNoShowFields[] = $field;
                    }
                }
            }
            $this->noShowFields = array_merge($realNoShowFields, $calculatedNoShowFields);
        } else {
            $this->noShowFields = array();
        }

    }

    public function getNoShownFields()
    {
        if (!is_array($this->noShowFields)) {
            $this->loadNoShownFields();
        }
        return $this->noShowFields;
    }

    public function isNoShown($idx)
    {
        return (in_array($idx, $this->getNoShownFields()));
    }

    public function isNoCSV($idx)
    {
        if (!isset($this->selectedConf['nocsv'])) return false;
        return in_array($idx, explode(",", $this->selectedConf['nocsv']));
    }

    public function getNoEditFields()
    {
        if ($this->noEditFields===false) {
            $this->noEditFields = array();
            if (isset($this->selectedConf['noedit'])) {
                $this->noEditFields = explode(",", $this->selectedConf['noedit']);
            }
            $showLimitedFields = $this->getShowLimitFields();
            if ($showLimitedFields) {
                $limitFields = array();
                foreach ($showLimitedFields as $key => $value) {
                    $limitFields[] = $key;
                }
                $this->noEditFields = array_merge($this->noEditFields, $limitFields);
            }
        }
        return $this->noEditFields;
    }

    public function getNoEditOnNewFields()
    {
        if ($this->noEditFieldsOnNew===false) {
            $this->noEditFieldsOnNew = array();
            if (isset($this->selectedConf['noeditonnew'])) {
                $this->noEditFieldsOnNew = explode(",", $this->selectedConf['noeditonnew']);
            }

        }
        return $this->noEditFieldsOnNew;
    }

    public function getShowLimitFields()
    {
        if ($this->showLimitFields===false) {
            $this->showLimitFields = array();
            if (isset($this->selectedConf['show_length'])) {
                $limiteds = explode(",", $this->selectedConf['show_length']);
                foreach ($limiteds as $limited) {
                    $aLimited = explode("::", $limited);
                    $this->showLimitFields[$aLimited[0]] = $aLimited[1];
                }
            }
        }
        return $this->showLimitFields;
    }

    public function isNoEdit($idx)
    {
        return (in_array($idx, $this->getNoEditFields()));
    }

    public function isNoEditOnNew($idx)
    {
        return (in_array($idx, $this->getNoEditFieldsOnNew()));
    }

    public function isShowLimit($idx)
    {
        $showLimitFields = $this->getShowLimitFields();
        return (isset($showLimitFields[$idx]));
    }

    protected function drawPageLimit()
    {
        if ((isset($this->selectedConf['limit'])) && ($this->totalRows > $this->selectedConf['limit'])) {
            $url = krm_menu::getURL().$this->getHistoryURL();
            if (isset($_GET['order']) && $_GET['order']!="") {
                $url.='order='.$_GET['order'].'&amp;';
            }
            if (isset($_GET['orderType']) && $_GET['orderType']!="") {
                $url.='orderType='.$_GET['orderType'].'&amp;';
            }
            $url.='pag=';
            $currentPag = $this->getPag(true);
            if (!$currentPag) {
                $currentPag = 1;
            }
            if (!isset($this->selectedConf['limitinputonly']) || $this->selectedConf['limitinputonly']!="1") {
                echo '<div class="contenedorPaginado"><ul class="paginado">';
                echo '<li><a href="'.$url.'1"><img src="./icons/firstPage.png"  alt="First Page"/></a></li>';
                echo '<li><a href="'
                     . $url.(($currentPag <= 1)? 1:($currentPag - 1))
                     . '"><img src="./icons/prevPage.png"  alt="Previus Page"/></a></li>';
                for ($i=($currentPag - 10);$i<($currentPag + 10);$i++) {
                    if ($i<1) continue;
                    if ($i>ceil($this->totalRows/$this->selectedConf['limit'])) break;
                    if ($i==$currentPag) {
                        echo '<li class="currentPage">'.$i.'</li>';
                        continue;
                    }
                    echo '<li><a href="'.$url.$i.'">'.$i.'</a></li>';

                }
                echo '<li><a href="'.$url
                     . (($currentPag==ceil($this->totalRows/$this->selectedConf['limit']))?
                         ceil($this->totalRows/$this->selectedConf['limit']) :
                         ($currentPag + 1))
                     . '"><img src="./icons/nextPage.png" alt="Next Page"/></a></li>';
                echo '<li><a href="'.$url
                     .ceil($this->totalRows/$this->selectedConf['limit'])
                     .'"><img src="./icons/lastPage.png" alt="Last Page" /></a></li>';
                echo '</ul></div>';
            }
            if (isset($this->selectedConf['limitinput']) && $this->selectedConf['limitinput']=="1") {
                echo "<p>"
                    . $this->l->l('Seleccione la página a la que quiere acceder') . ":&nbsp;&nbsp;"
                    . "<input type='text' name='inputpaginado' class='inputpaginado' size='3' maxlength='5'/>"
                    . "<input type='hidden' name='rutapaginado' value= '".$url."' />"
                    . "<input type='button' name='botonpaginado' class='botonpaginado' value='Ir' size = '4'/>"
                    . "</p>";
            }
        }
        if (isset($this->selectedConf['showlimit'])) {
            $replace = array(
                "%total%"=>$this->totalRows,
                "%limit%"=>$this->selectedConf['limit'],
                "%from%"=>$this->getPag(),
                "%to%"=>min(
                    $this->totalRows,
                    ($this->getPag()+$this->selectedConf['limit'])
                ),
                "%shown%"=>(min($this->totalRows, ($this->getPag() + $this->selectedConf['limit'])) - $this->getPag())
            );
            echo "<p>".str_replace(array_keys($replace), $replace, $this->selectedConf['showlimit'])."</p>";
        }
    }

    protected function drawErrors()
    {
        iError::error($this->conPrinc->getError());
        switch($this->conPrinc->getErrorNumber()) {
            case 1146:
                iError::warn("<textarea rows='7' cols='40' class='warn'>".$this->getCreateSQL()."</textarea>");
                break;
            case 1054:
                if (preg_match("/column '(.*)' in/", $this->conPrinc->getError(), $r)) {
                    $pl = $this->plantillaPrincipal;
                    $warnAux = "";
                    for ($i=0;$i<$pl->getNumFields();$i++) {
                        if ($r[1] == $pl->fields[$i]->getIndex()) {
                            $warnAux =  $pl->fields[$i]->getSQLType();
                            break;
                        }
                    }
                    $warnText = "alter table ".$this->plantillaPrincipal->getTab()." add ".$r[1]." ".$warnAux;
                    iError::warn("<textarea rows='7' cols='40' class='warn'>".$warnText."</textarea>");
                }
                break;
        }
    }

    protected function is_tablon_edit($op)
    {
        return ((isset($this->conf[$op]))
               && (isset($this->conf[$op]['class']))
               && (($this->conf[$op]['class']=="tablon_edit")
               ||($this->conf[$op]['class']=="tablon_multiedit")));
    }

    protected function selected_have_opts_tablon_edit()
    {
        if (!isset($this->selectedConf['ops'])) return false;
        $aOps = array();
        foreach ($this->selectedConf['ops'] as $op=>$foo) {
            if ($this->is_tablon_edit($op)) $aOps[] = $op;
        }

        return $aOps;
    }

    public function translatefield($f)
    {
        list($w, $field) = explode("::", $this->selectedConf['translate']);
        if ($f == $w) return array('trans_idenField');

        $lang = preg_replace("/".$field."/", "", $f);
        if (!$lang) return false;
        return array('trans', $lang);
    }

    public function translate()
    {
        list($w, $field) = explode("::", $this->selectedConf['translate']);
        if ($w!=""&&$field!=""&&$w!=false&&$field!=false) {
            echo '<input type="hidden" id="trans_idenField" value="'.$w.'" >';
            echo '<input type="hidden" id="trans_from" value="'.$this->selectedConf['translate_from'].'" >';
            echo '<input type="hidden" id="trans_matchField" value="'.$field.'" >';
            //echo '<p id="takata" s ></p>';
            echo '<div id="trans_container"><p id="trans_result">translator</p>'
                 . '<button id="trans_switcher">fixed position</button></div>';
        }
    }

    public function get_timezone_HTMLselect($sel=false)
    {
        $html = '<select id="comboTimezone" class="comboTimezone" name="k_timezone">';
        foreach ($this->timezones as $timezone) {
            $html.= '<option value="'.$timezone['id_zone'].'"  '
                    . ((isset($sel) && $sel !== false
                      && ($sel == $timezone['timezone']
                      || $sel == $timezone['id_zone'])) ?
                      'selected="selected"':
                      '')
                    . ' >'.$timezone['code'].' '.$timezone['timezone'].'</option>';
        }
        $html.= '</select>';
        return $html;
    }

    protected function drawTimezoneSelect()
    {
        $select = $this->get_timezone_HTMLselect(K_TIMEZONE);
        echo '<div class="calendarOptionsBox" ><form method="post" action="" >';
        echo '<p><span>' . date(K_DATEFORMAT).'</span> <span class="rhour">'
             . date('H').'</span>:<span class="rmin">'
             . date('i').'</span>:<span class="rsec">'
             . date('s').'</span>';
        echo $select.' <input type="submit" name="change timezone" value="change timezone" />';
        echo '</p></form></div>';
    }

    protected function getManualLimit()
    {
        if (!isset($this->selectedConf['manualLimit'])) return $this->selectedConf['limit'];
        if (isset($_REQUEST['manualLimit'])) {
            $manualLimit = (int)$_REQUEST['manualLimit'];
            if ($manualLimit>0) $_SESSION['manualLimit'][$this->currentSection] = $manualLimit;
        }
        if (isset($_SESSION['manualLimit'][$this->currentSection])) {
            return $_SESSION['manualLimit'][$this->currentSection];
        }
        return $this->selectedConf['limit'];
    }

    /*
    * Dibuja selector del campo limit
    */
    protected function drawManualLimit()
    {
        echo '<div class="manualLimit" >';
        echo $this->l->l("Límite de registros"). ': '
             . '<input type="text" maxlength="4" id="manualLimit" value="'
             . $this->getManualLimit().'" />';
        echo '</div>';
    }


    /*
     * Dibuja las opciones de la tabla
     */
    protected function drawGeneralOpsTablon($pl, $position = 'down')
    {
        $multiops = (($this->selected_have_opts_tablon_edit())
                    || $this->_isDeletable()
                    || (isset($this->selectedConf['csv'])));
        $generalops = ( (sizeof($this->history)>0) || (isset($this->selectedConf['new']) && $this->selectedConf['new']));
        if ($multiops) {
            echo '<ul class="optsTablon">';
            if ($position === 'down') {
                $flechita = 'flechita.png';
            } else {
                $flechita = 'flechita_down.png';
            }
            echo ' <li><img src="icons/' . $flechita
                 . '" class="flechita" alt="Seleccione multiples opciones para actualización masiva" /></li>';
            if (($ops = $this->selected_have_opts_tablon_edit())!==false) {
                $mOps=array();
                foreach ($ops as $op) {
                    $url = krm_menu::getURL();
                    $url .= $this->getHistoryURL();
                    $url .= 'tSec['.$this->currentSection.'::'.$op.']=%id%';
                    $class="opts";
                    $mas = "";
                    if ($this->conf[$op]['class'] == "tablon_edit") {
                        $li = '<li class="opts multiopsh hidden"><a href="'
                              . $url.'" '.$mas.' class="'.$class.'" title="'.self::getMLval($this->conf[$op]['tit'], $this->conf[$op], 'tit').'">';
                        if (isset($this->conf[$op]['img'])) {
                            $li .= '<img src="./icons/_edit.png" alt="'.self::getMLval($this->conf[$op]['tit'], $this->conf[$op], 'tit').'" />';
                        }
                        $li .= self::getMLval($this->conf[$op]['tit'], $this->conf[$op], 'tit');
                        $li .= '</a></li>';
                        $mOps[] = $li;
                    }
                    if ($this->conf[$op]['class'] == "tablon_multiedit") {
                        $class = " tablon_multiedit";
                        $mas = 'campo="'.$this->conf[$op]['campo'].'"';
                        $mas.= 'plt="'.$this->conf[$op]['plt'].'"';

                        $li =  '<li class="opts multiopsh hidden"><a href="'.$url.'" '
                               . $mas.' class="'.$class.'" title="'
                               . self::getMLval($this->conf[$op]['tit'], $this->conf[$op], 'tit').'" removeafterupdate="'
                               . ((isset($this->conf[$op]['removeAfterUpdate']))?
                                    $this->conf[$op]['removeAfterUpdate']:
                                    "")
                                . '">';
                        if (isset($this->conf[$op]['img'])) {
                            /*
                             * Modificamos el icono para que el botón de editar siempre sea el mismo.
                             * Si queremos hacer que lo coja desde archivo de configuración cambiamos
                             * la linea comentada a continuación...
                             */
                            //echo '<img src="./icons/'.$this->conf[$op]['img']
                            //     . '" alt="'.$this->conf[$op]['tit'].'" />';
                            $li .= '<img src="./icons/_edit.png" alt="'.self::getMLval($this->conf[$op]['tit'], $this->conf[$op], 'tit').'" />';
                        }
                        $li .= self::getMLval($this->conf[$op]['tit'], $this->conf[$op], 'tit');
                        $li .= '</a></li>';
                        $mOps[]=$li;
                    }
                }
                $size = sizeof($mOps);
                if ($size<=2) {
                    echo str_replace("hidden", "", implode(" ", $mOps));
                } else {
                    echo str_replace("hidden", "", $mOps[0]);
                    echo "<ul id=\"hiddenMultiops\">";
                    echo str_replace("", "", $mOps[0]);
                    for ($i=1;$i<$size;$i++) {
                        echo $mOps[$i];
                    }
                    echo "</ul>";
                    echo '<li class="optsMore" ><a class="optsLink" title="'
                         . $this->l->l('Desplegar opciones de actualización masiva.')
                         . '" ><img id="moreopts" src="./icons/window_nofullscreen.png"'
                         . ' csrc="./icons/window_nofullscreen.png" osrc="./icons/window_fullscreen.png" alt=""/>'
                         . '</a></li>';
                }
            }
            if ((isset($this->selectedConf['csv'])) && ((bool)$this->selectedConf['csv'])) {
                $url = krm_menu::getURL();
                $url .= $this->getHistoryURL();
                echo '<li class="opts">'
                     . '<a href="'.$url.'&amp;CSV=1" class="optsLink" title="'
                     . $this->l->lstr('csv', false, $pl->getEntidad()).'">'
                     . '<img src="./icons/_download.png" alt="Download" />'
                     . ((isset($this->selectedConf['csv_txt']))?
                             $this->l->lstr('Descargar en texto', false, $pl->getEntidad()) :
                             $this->l->lstr('csv', false, $pl->getEntidad())
                     )
                     .'</a></li>';
            }

            if ($pl->anyFLDemail&&$this->_emailSystem) {
                echo '<li class="opts"><a class="optsLink" title="'
                     . $this->l->l('Enviar Email').'" ><img src="./icons/_delete.png" alt="" id="multiSendEmail" />'
                     . $this->l->l('Enviar Email').'</a></li>';
            }
            if ($this->_isDeletable()) {
                echo '<li class="opts"><a class="optsLink" title="'
                     . $this->l->lstr('Eliminar', false, $pl->getEntidad())
                     . '" ><img src="./icons/_delete.png" alt="Eliminar" id="multiDelete"  objetoElim="'
                     . $pl->getEntidad().'" />'
                     . $this->l->lstr('Eliminar', false, $pl->getEntidad()).'</a></li>';
            }
            if (!$generalops) echo '</ul>';
        }
        if ($generalops) {
            if (!$multiops) {
                echo '<ul class="optsTablon">';
                echo '<li class="optsIni " >&nbsp;</li>';
            } else {
                echo '<li class="sep " >&nbsp;</li>';

            }
            if (isset($this->selectedConf['new']) && $this->selectedConf['new']) {
                $aNoEdit = $this->getNoEditOnNewFields();
                if (!empty($aNoEdit)) {
                    $param3 = implode(",", $aNoEdit);
                } else {
                    $param3 = '0';
                }

                $newOpts = explode(',', $this->selectedConf['new']);
                foreach ($newOpts as $newOpt) {
                    switch($newOpt) {
                        case 'toline':
                            echo '<li class="opts" id="new::'
                                 . basename($pl->getFile()).'::'.$param3.'"><a class="optsLink" title="'
                                 . $this->l->lstr('Nuev', $pl->getGenero(), $pl->getEntidad())
                                 . '"><img src="./icons/_new.png" alt="'
                                 . $this->l->l('Nuevo').'" id="newToline" />'
                                 . $this->l->lstr('Nuev', $pl->getGenero(), $pl->getEntidad())
                                 . '</a></li>';
                            break;
                        case 'editPlt':
                            $opcionEdit = $this->selectedConf['editPlt'];
                            $pltEdit = basename($this->conf[$opcionEdit]['plt']);
                            echo '<li class="opts" id="new::'.$pltEdit.'::show::'
                                 . basename($pl->getFile()).'::'
                                 . $param3.'"><a class="optsLink" title="'
                                 . $this->l->lstr('Nuev', $pl->getGenero(), $pl->getEntidad())
                                 . '"><img src="./icons/_new.png" alt="'
                                 . $this->l->l('Nuevo').'" id="newInlineEdit" />'
                                 . $this->l->lstr('Nuev', $pl->getGenero(), $pl->getEntidad())
                                 . '</a></li>';
                            break;
                        case 'zip':
                            if (isset($this->selectedConf['newzip_label'])) {
                                $label = $this->selectedConf['newzip_label'];
                            } else {
                                $label = $this->l->lstr('Nuev', $pl->getGenero(), $pl->getEntidad() . " zip/rar/tgz");
                            }
                            echo '<li class="opts" id="newzip::'
                                 . basename($pl->getFile()) . '::'
                                 . $param3.'"><a class="optsLink" id="newZip" title="'
                                 . $label
                                 .'"><img src="./icons/_newZip.png" alt="Nuevo"  />'
                                 . $label
                                 .'</a></li>';
                            break;
                        case 'cmis':

                            if (!file_exists("../configuracion/cmis_karma_conf.cfg")) {
                                iError::error("Fichero de definición de perfiles cmis [cmis_karma_conf.cfg] no encontrado.");
                                break;
                            }
                            $this->aJs[] = "../modules/tablon/scripts/jquery.FileTree.js";
                            $this->aCss[] = "../modules/tablon/css/jqueryFileTree.css";
                            if (isset($this->selectedConf['newcmis_label'])) {
                                $label = $this->selectedConf['newcmis_label'];
                            } else {
                                $label = $this->l->lstr('Nuev', $pl->getGenero(), $pl->getEntidad() . " fichero desde CMIS");
                            }
                            if (!isset($this->selectedConf['newcmis_field'])) {
                                iError::error("Falta la directiva newcmis_field en el fichero de configuración");
                                break;
                            }
                            if (!isset($this->selectedConf['newcmis_profile'])) {
                                iError::error("Falta la directiva newcmis_profile en el fichero de configuración");
                                break;
                            }

                            echo '<li ';
                            echo ' data-profile="'.$this->selectedConf['newcmis_profile'].'" ';
                            echo ' rel="'.$this->selectedConf['newcmis_field'].'" ';
                            echo 'class="opts" id="newcmis::'
                                 . basename($pl->getFile()) . '::'
                                 . $param3.'"><a class="optsLink" id="newCMIS" title="'
                                 . $label
                                 .'"><img src="./icons/_newCmis.png" alt="Nuevo"  />'
                                 . $label
                                 .'</a></li>';
                            break;
                        case 'newFrom':
                            if (isset($this->selectedConf['newFrom']) && !empty($this->selectedConf['newFrom'])) {
                                $newFromPlt = "";
                                if (!file_exists($this->rutaPlantillas.$this->selectedConf['newFrom'])) break;
                                $newFromPlt = $this->rutaPlantillas.$this->selectedConf['newFrom'];
                                $nfPlt = new tablon_plantilla($newFromPlt);
                                if (isset($this->selectedConf['newFromFld'])
                                    && !empty($this->selectedConf['newFromFld'])) {
                                    $selectFld = "".$this->selectedConf['newFromFld']." = '".$this->currentValue."'";
                                } else {
                                    $selectFld = "".$this->selectedConf['idcond']." = '".$this->currentValue."'";
                                }

                                //newFromUnique
                                if (isset($this->selectedConf['newFromUnique'])
                                    && !empty($this->selectedConf['newFromUnique'])) {
                                    $nfUnique = $this->selectedConf['newFromUnique'];
                                } else {
                                    $nfUnique = "null";
                                }

                                echo '<li class="opts" id="new::'
                                     . basename($newFromPlt).'::'
                                     . $selectFld.'::'.$nfUnique.'"><a class="optsLink" title="'
                                     . $this->l->lstr('Nuev', $pl->getGenero(), $pl->getEntidad())
                                     . '"><img src="./icons/_new.png" alt="'.$this->l->l('Nuevo')
                                     . '" id="newFrom" />'
                                     . $this->l->lstr('Nuev', $nfPlt->getGenero(), $nfPlt->getEntidad())
                                     .'</a></li>';
                            }
                            break;
                        case 'inline':
                        default:
                            echo '<li class="opts" id="new::'
                                 . basename($pl->getFile()).'::'.$param3.'"><a class="optsLink" title="'
                                 . $this->l->lstr('Nuev', $pl->getGenero(), $pl->getEntidad())
                                 . '"><img src="./icons/_new.png" alt="'
                                 . $this->l->l('Nuevo').'" id="newInline" />'
                                 . $this->l->lstr('Nuev', $pl->getGenero(), $pl->getEntidad())
                                 . '</a></li>';
                            break;
                    }
                }

            }
            echo '<li class="optsEnd">&nbsp;</li>';
            if (sizeof($this->history)>0) {
                echo '<li class="opts backButton">'.$this->drawBackLink().'</li>';
            }
            echo '</ul>';
        }
    }

    public function executeMenu($op)
    {
        $this->notCurrentValue = true;
        if (!$plantilla = $this->getPlt()) {
            iError::error("Plantilla no encontrada");
            return false;
        }
        $this->plantillaPrincipal = new tablon_plantilla($plantilla);
        if (isset($this->selectedConf['addplt'])) {
            $pltaux = $this->selectedConf['addplt'];
            $this->plantillaPrincipal->addPlt($this->rutaPlantillas, $pltaux);
        }
        $pl = $this->plantillaPrincipal;
        $sql = $this->doSQL();

        $ret = array();
        while ($r = $this->conPrinc->getResult()) {
            $tret = array();
            for ($i=0;$i<$pl->getNumFields();$i++) {
                if ($this->isNoShown($pl->fields[$i]->getIndex())) continue;
                $tret[$pl->fields[$i]->getSQLFLD()] = $pl->fields[$i]->drawTableValue($r[$pl->fields[$i]->getAlias()]);
                $url="&amp;";
                $url .= 'tSec[0::'.$op.']='.$r['__ID'];
                $tret['__execurl'] = $url;
            }
            $ret[] = $tret;
        }
        return $ret;
    }

    public function draw()
    {
        if (!$plantilla = $this->getPlt()) {
            iError::error("Plantilla no encontrada");
            return false;
        }
        $this->plantillaPrincipal = new tablon_plantilla($plantilla);
        if (isset($this->selectedConf['addplt'])) {
            $pltaux = $this->selectedConf['addplt'];
            $this->plantillaPrincipal->addPlt($this->rutaPlantillas, $pltaux);
        }
        $pl = $this->plantillaPrincipal;
        if ($pl->hasJs())
                while ($this->aJs[] = $pl->getJS());
        if ($pl->hasCss())
                while ($this->aCss[] = $pl->getCSS());

        $multiops = (($this->selected_have_opts_tablon_edit())
                    || $this->_isDeletable()
                    || (isset($this->selectedConf['csv'])));
        $csv = ((isset($_GET['CSV'])) && ($_GET['CSV']=="1"));
        /*filtrado¿?*/
        $sql = $this->doSQL();

        if (isset($this->selectedConf['search'])) {
            echo '<p id="__user_filtrado"><img src="./icons/viewmag.png" alt="'
                 . $this->l->l('ut_filtrado').'" />'
                 . $this->l->l('act_filtrado').'</p>';
            $this->drawSearcher();
        }

        if (isset($this->selectedConf['upLimit'])) {
            $this->drawPageLimit();
        }


        if (isset($this->selectedConf['manualLimit'])) {
            $this->drawManualLimit();

        }

            if (isset($this->selectedConf['upOpts'])) {
                $this->drawGeneralOpsTablon($pl, 'up');
            }

        if (isset($this->selectedConf['translate'])) {

            $this->translate();
        }
        /*filtrado¿?*/
        echo '<div id="tablonContainer">';
        if (isset($this->selectedConf['timezoneselect']) && $this->selectedConf['timezoneselect']=="true") {
            $this->timezones = datemanager::get_timezones();
            $this->drawTimezoneSelect();

        }



        if ($csv) {
                ob_end_clean();
                if (isset($this->selectedConf['csv_txt'])) {
                    ob_start();
                    header("Content-type: text/plain; charset=UTF-8");
                    header(
                        "Content-Disposition: attachment; filename=\""
                        . i::clean(self::getMLval($this->selectedConf['tit'], $this->selectedConf, 'tit')).".txt\";"
                    );
                } else {
                    header("Content-type: application/vnd.ms-excel");
                    header(
                        "Content-Disposition:  filename=\""
                        . i::clean(self::getMLval($this->selectedConf['tit'], $this->selectedConf, 'tit')).".xls\";"
                    );
                }

        }
        echo '<table'.(($csv)? '>':' id="tablon" class="tablon"  genero="'
             . $pl->getGenero().'" entidad="'
             . $pl->getEntidad().'">');

        if (!$csv) {
            echo $this->plantillaPrincipal->drawHead(
                ($this->hasOptions()&&($opts = $this->drawOptions("%id%"))),
                $this->getNoShownFields(),
                $multiops,
                krm_menu::getURL().$this->getHistoryURL(),
                ((isset($this->selectedConf['numlines'])) && (!$csv))
            );
        } elseif($this->selectedConf['csvHead'] == '1') {
            echo $this->plantillaPrincipal->drawCsvHead();
        }

        if (!$sql) {
            $this->drawErrors();
        } else {
            $this->drawTableContents($multiops, $csv);

        }
        echo '</table>';
        if ($csv) {
            if (isset($this->selectedConf['csv_txt'])) {
                $buf = ob_get_contents();
                ob_end_clean();
                echo strip_tags(str_replace("</tr>", "\n", $buf));
            }
            exit();
        }

        $this->drawGeneralOpsTablon($pl, 'down');

        $this->drawPageLimit();
        /*
            hidden fields for ajax new.
        */
        //LANDER
        if (isset($this->selectedConf['onnew'])) {
            echo '<input type="hidden" name="tablononnew" id="tablononnew" value="'.$this->selectedConf['onnew'].'" />';
        }
        echo '<form id="hiddenFields">';

        $formForUpdate = "";

        if (isset($this->selectedConf['idcond'])) {
            $idcondField = '<input type="hidden" name="'
                         . trim($this->selectedConf['idcond'])
                         . '" value="'.$this->currentValue.'" />';
            echo $idcondField;
            $formForUpdate .= $idcondField;
        }

        if (isset($this->selectedConf['idprecond'])) {
            $idprecondField = '<input type="hidden" name="'
                            . trim($this->selectedConf['idprecond']).'" value="'
                            . $this->lastValue.'" />';
            echo $idprecondField;
            $formForUpdate .= $idprecondField;
        }

        if ( isset($this->selectedConf['logincond'])
           && in_array('insert', explode('|', $this->selectedConf['logincondVConf']))) {
            echo '<input type="hidden" name="'
                 . trim($this->selectedConf['logincond'])
                 . '" value="'.$_SESSION["__ID"]
                 . '" />';
        }
	
	//IVOZ-NG 
	if (isset($this->selectedConf['logingrupocond']) && $_SESSION["__ID"]!='1'){
            echo '<input type="hidden" name="'.$this->selectedConf['logingrupocond'].'" value="'.$_SESSION["__GRUPO_VINCULADO"].'" />';
	} elseif ( isset($this->selectedConf['logingrupocond']) ) {
            echo '<input type="hidden" name="'.$this->selectedConf['logingrupocond'].'" value="'.$this->selectedConf['dfltLoginGrupoForAdmin'].'" />';
	}

        if ( isset($this->selectedConf['logincond'])
           && in_array('update', explode('|', $this->selectedConf['logincondVConf']))) {
            $formForUpdate .= '<input type="hidden" name="'
                           . trim($this->selectedConf['logincond'])
                           . '" value="' . $_SESSION["__ID"]
                           . '" />';
        }

        if (isset($this->selectedConf['idTabcond'])) {
            /* Si las condiciones de cuándo aplicar el idTabCond viene para sólo triggers o para todo caso. */
            if (in_array('insertTriggersOnly', explode('|', $this->selectedConf['idTabcondVConf']))) {
                echo '<input type="hidden" name="'
                     . trim($this->selectedConf['idTabcond']).'" value="'
                     . $this->currentValue.'" onlyTriggers="true"/>';
            } elseif (in_array('insert', explode('|', $this->selectedConf['idTabcondVConf']))) {
                echo '<input type="hidden" name="'
                     . trim($this->selectedConf['idTabcond'])
                     .'" value="'.$this->currentValue.'" />';
            }
            if (in_array('updateTriggersOnly', explode('|', $this->selectedConf['idTabcondVConf']))) {
                $formForUpdate .= '<input type="hidden" name="'
                               . trim($this->selectedConf['idTabcond'])
                               . '" value="'.$this->currentValue
                               . '" onlyTriggers="true"/>';
            } elseif (in_array('update', explode('|', $this->selectedConf['idTabcondVConf']))) {
                $formForUpdate .= '<input type="hidden" name="'
                               . trim($this->selectedConf['idTabcond'])
                               . '" value="'.$this->currentValue.'" />';
            }
        }

        if (isset($this->selectedConf['idTabprecond'])) {
            if (
                in_array(
                    'insert',
                    explode('|', $this->selectedConf['idTabprecondVConf'])
                )
                ||
                in_array(
                    'insertTriggersOnly',
                    explode('|', $this->selectedConf['idTabprecondVConf'])
                )
            ) {
                if (in_array('insertTriggersOnly', explode('|', $this->selectedConf['idTabprecondVConf']))) {
                    echo '<input type="hidden" name="'
                         . trim($this->selectedConf['idTabprecond'])
                         . '" value="'.$this->lastValue
                         . '" onlyTriggers="true"/>';
                } else {
                    echo '<input type="hidden" name="'
                         . trim($this->selectedConf['idTabprecond'])
                         . '" value="'.$this->lastValue.'" />';
                }
            }

            if (
                in_array(
                    'update',
                    explode('|', $this->selectedConf['idTabprecondVConf'])
                )
                ||
                in_array(
                    'updateTriggersOnly',
                    explode('|', $this->selectedConf['idTabprecondVConf'])
                )
            ) {
                if (in_array('updateTriggersOnly', explode('|', $this->selectedConf['idTabprecondVConf']))) {
                    $formForUpdate .= '<input type="hidden" name="'
                                   . trim($this->selectedConf['idTabprecond'])
                                   . '" value="'.$this->lastValue
                                   . '" onlyTriggers="true"/>';
                }else
                    $formForUpdate .= '<input type="hidden" name="'
                                   . trim($this->selectedConf['idTabprecond'])
                                   . '" value="'.$this->lastValue.'" />';
            }
        }
        if ( isset($this->selectedConf['logincondOnInsert'])) {
            switch (true) {
                case (isset($this->selectedConf['logincondOnInsertSessionIdxName'])):
                    echo '<input type="hidden" name="'
                         . trim($this->selectedConf['logincondOnInsert']).'" value="'
                         . $_SESSION[$this->selectedConf['logincondOnInsertSessionIdxName']].'" />';
                    break;
                default:
                    // ToDo otras jartadas varias...
                    break;
            }
        }
        if (isset($this->selectedConf['pltCond'])) {
            $pltCondiciones = explode("|", $this->selectedConf['pltCond']);
            /* Si las condiciones de cuándo aplicar el pltCond vienen para sólo triggers o para todo caso. */
            if (isset($this->selectedConf['pltCondVConf'])) {
                $aPltCondVConf = explode('|', $this->selectedConf['pltCondVConf']);
                if (in_array('insert', $aPltCondVConf)
                    || in_array('insertTriggersOnly', $aPltCondVConf)
                ) {
                    if (sizeof($pltCondiciones)>0) {
                        for ($i=0;$i<sizeof($pltCondiciones);$i++) {
                            /* TODO mirar el igual y el distinto de en las condiciones*/
                            $fr = explode('is', $pltCondiciones[$i], 2);
                            if (isset($fr[1])&&trim($fr[1])=="null") {

                            } else {
                                if (preg_match("/=/", $pltCondiciones[$i])) {
                                    list($c, $v)=explode('=', $pltCondiciones[$i], 2);
                                    if (in_array('insertTriggersOnly', $aPltCondVConf)) {
                                        echo '<input type="hidden" name="'
                                             . trim($c).'" value="'
                                             . trim($v).'" onlyTriggers="true" />';
                                    } else {
                                        echo '<input type="hidden" name="'
                                             . trim($c).'" value="'
                                             . trim($v).'" />';
                                    }
                                }
                                if (preg_match("/is/", $pltCondiciones[$i])) {
                                    list($c, $v)=explode('is', $pltCondiciones[$i], 2);
                                    if (in_array('insertTriggersOnly', $aPltCondVConf)) {
                                        echo '<input type="hidden" name="'
                                             . trim($c).'" value="'
                                             . trim($v).'" onlyTriggers="true" />';
                                    } else {
                                        echo '<input type="hidden" name="'
                                             . trim($c).'" value="'
                                             . trim($v).'" />';
                                    }
                                }
                            }
                        }
                    }
                }
                if (in_array('udpate', $aPltCondVConf)
                    || in_array('updateTriggersOnly', $aPltCondVConf)
                ) {
                    if (sizeof($pltCondiciones)>0) {
                        for ($i=0;$i<sizeof($pltCondiciones);$i++) {
                            /* TODO mirar el igual y el distinto de en las condiciones*/
                            $fr = explode('is', $pltCondiciones[$i], 2);
                            if (isset($fr[1])&&trim($fr[1])=="null") {

                            } else {
                                if (preg_match("/=/", $pltCondiciones[$i])) {
                                    list($c, $v)=explode('=', $pltCondiciones[$i], 2);
                                    if (in_array('updateTriggersOnly', $aPltCondVConf)) {
                                        $formForUpdate .= '<input type="hidden" name="'
                                                       . trim($c) . '" value="'
                                                       . trim($v) . '" onlyTriggers="true" />';
                                    } else {
                                        $formForUpdate .= '<input type="hidden" name="'
                                                       . trim($c) . '" value="'
                                                       . trim($v).'" />';
                                    }
                                }
                                if (preg_match("/is/", $pltCondiciones[$i])) {
                                    list($c, $v)=explode('is', $pltCondiciones[$i], 2);
                                    if (in_array('updateTriggersOnly', $aPltCondVConf)) {
                                        $formForUpdate .= '<input type="hidden" name="'
                                                       . trim($c).'" value="'
                                                       . trim($v).'" onlyTriggers="true" />';
                                    } else {
                                        $formForUpdate .= '<input type="hidden" name="'
                                                       . trim($c).'" value="'
                                                       . trim($v).'" />';
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if (sizeof($pltCondiciones)>0) {
                    for ($i=0;$i<sizeof($pltCondiciones);$i++) {
                        /* TODO mirar el igual y el distinto de en las condiciones*/
                        $fr = explode('is', $pltCondiciones[$i], 2);
                        if (isset($fr[1])&&trim($fr[1])=="null") {

                        } else {
                            if (preg_match("/=/", $pltCondiciones[$i])) {
                                list($c, $v)=explode('=', $pltCondiciones[$i], 2);
                                echo '<input type="hidden" name="'.trim($c).'" value="'.trim($v).'" />';
                            }
                            if (preg_match("/is/", $pltCondiciones[$i])) {
                                list($c, $v)=explode('is', $pltCondiciones[$i], 2);
                                echo '<input type="hidden" name="'.trim($c).'" value="'.trim($v).'" />';
                            }
                        }
                    }
                }
            }
        }

        /*
         * Lo mismo que pltCond pero con condiciones que no vinene sobre la
         * tabla del plt, sino sobre otras (de las left join por ejemplo)
         */
        if (isset($this->selectedConf['pltTabCond'])) {
            $pltCondiciones = explode("|", $this->selectedConf['pltTabCond']);
            if (isset($this->selectedConf['pltTabCondVConf'])) {
                $aPltTabCondVConf = explode('|', $this->selectedConf['pltTabCondVConf']);
                if (in_array('insert', $aPltTabCondVConf)
                    || in_array('insertTriggersOnly', $aPltTabCondVConf)) {
                    if (sizeof($pltCondiciones)>0) {
                        for ($i=0;$i<sizeof($pltCondiciones);$i++) {
                            /* TODO mirar el igual y el distinto de en las condiciones*/
                            $fr = explode('is', $pltCondiciones[$i], 2);
                            if (isset($fr[1])&&trim($fr[1])=="null") {

                            } else {
                            if (preg_match("/=/", $pltCondiciones[$i])) {
                                list($c, $v)=explode('=', $pltCondiciones[$i], 2);
                                if (in_array('insertTriggersOnly', $aPltTabCondVConf)) {
                                    echo '<input type="hidden" name="'
                                         . trim($c) . '" value="'
                                         . trim($v).'" onlyTriggers="true" />';
                                } else {
                                    echo '<input type="hidden" name="'
                                         . trim($c) . '" value="'
                                         . trim($v).'" />';
                                }
                            }
                                if (preg_match("/is/", $pltCondiciones[$i])) {
                                    list($c, $v)=explode('is', $pltCondiciones[$i], 2);
                                    if (in_array('insertTriggersOnly', $aPltTabCondVConf)) {
                                        echo '<input type="hidden" name="'
                                             . trim($c).'" value="'
                                             . trim($v).'" onlyTriggers="true" />';
                                    } else {
                                        echo '<input type="hidden" name="'
                                             . trim($c).'" value="' . trim($v)
                                             . '" />';
                                    }
                                }
                            }
                        }
                    }
                }
                if (in_array('udpate', $aPltTabCondVConf)
                   || in_array('updateTriggersOnly', $aPltTabCondVConf)) {
                    if (sizeof($pltCondiciones)>0) {
                        for ($i=0;$i<sizeof($pltCondiciones);$i++) {
                            /* TODO mirar el igual y el distinto de en las condiciones*/
                            $fr = explode('is', $pltCondiciones[$i], 2);
                            if (isset($fr[1])&&trim($fr[1])=="null") {

                            } else {
                                if (preg_match("/=/", $pltCondiciones[$i])) {
                                    list($c, $v)=explode('=', $pltCondiciones[$i], 2);
                                    if (in_array('updateTriggersOnly', $aPltTabCondVConf)) {
                                        $formForUpdate .= '<input type="hidden" name="'
                                                       . trim($c) . '" value="'
                                                       . trim($v).'" onlyTriggers="true" />';
                                    } else {
                                        $formForUpdate .= '<input type="hidden" name="'
                                                       . trim($c) . '" value="'
                                                       . trim($v).'" />';
                                    }
                                }
                                if (preg_match("/is/", $pltCondiciones[$i])) {
                                    list($c, $v)=explode('is', $pltCondiciones[$i], 2);
                                    if (in_array('updateTriggersOnly', $aPltTabCondVConf)) {
                                        $formForUpdate .= '<input type="hidden" name="'
                                                       . trim($c) . '" value="' . trim($v)
                                                       . '" onlyTriggers="true" />';
                                    } else {
                                        $formForUpdate .= '<input type="hidden" name="'
                                                       . trim($c) . '" value="' . trim($v)
                                                       . '" />';
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if (sizeof($pltCondiciones)>0) {
                    for ($i=0;$i<sizeof($pltCondiciones);$i++) {
                        /* TODO mirar el igual y el distinto de en las condiciones*/
                        $fr = explode('is', $pltCondiciones[$i], 2);
                        if (isset($fr[1])&&trim($fr[1])=="null") {

                        } else {
                            if (preg_match("/=/", $pltCondiciones[$i])) {
                                list($c, $v)=explode('=', $pltCondiciones[$i], 2);
                                echo '<input type="hidden" name="'.trim($c).'" value="'.trim($v).'" />';
                            }
                            if (preg_match("/is/", $pltCondiciones[$i])) {
                                list($c, $v)=explode('is', $pltCondiciones[$i], 2);
                                echo '<input type="hidden" name="'.trim($c).'" value="'.trim($v).'" />';
                            }
                        }
                    }
                }
            }
        }
        echo '</form>';
        if ($formForUpdate!="") {
            echo "<form id='hiddenFieldsUp'>".$formForUpdate."</form>";
        }
        echo '</div>';
    }

    static function generateCache($args, $public = false, $doReturn = false,$noDownload = false)
    {
        $kRegistry = KarmaRegistry::getInstance();
        if (!$kRegistry->isDefined('lang')) {
            new krm_menu();
        }
        $literal = new k_literal($kRegistry->get('lang'));

        list($type, $plantilla, $campo, $valor) = tablon_AJAXjeditable::decodeId($args, $cmps = 4, "/");
        switch($type) {
            case "img":
                if ($public === false) {
                    if (!file_exists($plantilla)) {
                        umask(0000);
                        if (!mkdir($plantilla, 0755)) {
                            die("Imposible crear directorio cache para la plantilla [".$plantilla."].");
                        }
                    } else {
                        if (!is_dir($plantilla)) die("Existe el elemento [".$plantilla."] pero es un fichero.");
                        if (!is_writeable($plantilla)) die("El directorio [".$plantilla."] no es escribible.");
                    }
                    chdir($plantilla);
                }
                $pl = new tablon_plantilla(tablon_AJAXjeditable::setPlantillaPath($plantilla));
                if (($idFLD = $pl->findField($campo)) === false) die("no se encuentra el campo");
                $nCampo = $pl->fields[$idFLD]->getSQLFLD();
                $nCampoNombreImagen = $pl->fields[$idFLD]->subFields[$pl->fields[$idFLD]->img_name]->getSQLFLD();
                if ($pl->fields[$idFLD]->img_size !== false) {
                    $nCampoSizeImagen = $pl->fields[$idFLD]->subFields[$pl->fields[$idFLD]->img_size]->getSQLFLD();
                    $aExtra = array($nCampoSizeImagen);
                } else {
                    $nCampoSizeImagen = false;
                    $aExtra = array();
                }


                $ret = $pl->getIdFromUnique($nCampoNombreImagen, $valor, $aExtra);

                if ($ret===false) {
                    if ($doReturn) return false;
                    die($literal->l("No existe el fichero"));
                }

                if ($nCampoSizeImagen === false) {
                    $id = $ret[0];
                    $size = false;
                } else {
                    list($id, $size) = $ret;
                }


                $file = "_".$nCampo."_".$id;
                $fileMime = $file.'_mime_type';
                $fileName = (isset($_GET['thumb']))? $file."_thumb":$file;
                if ($public !== false) {
                    /* Es una llamada desde la zona pública, en $public vendrá el prefijo */
                    $w = $public['w'];
                    $h = $public['h'];
                    $fileName = $file."_".$public['prefix'];
                    $noResizeIfSmaller = false;
                } else {
                    $noResizeIfSmaller = true;
                    $w = $h = 70;
                }
                $eTag = md5($fileName.$size);

                if ( (!isset($_GET['nocache'])) && ($doReturn == false) ) i::checkETAG($eTag);

                if ($size !== false) { // Si existe el campo size, se realiza la comprobación por cache
                    if ((@filesize($file)!=$size)) {
                        foreach (glob($file."*") as $filen) {
                           @unlink($filen);
                        }
                        @unlink($fileName."_thumb");
                    }
                }

                if ((isset($_GET['nocache']))
                       || (!((@file_exists($file))
                       && (@file_exists($fileMime))
                       && (@filesize($file)==$size)
                       && (@file_exists($fileName))))) {

                    $pl->loadSingleField($idFLD, $id, true);
                    file_put_contents($file, $pl->fields[$idFLD]->getValue());

                    if ($fileName!=$file) { //thumb
                        $im = new i_image($file);
                        if (!$im->setNewDim($w, $h, $noResizeIfSmaller)) {
                            @unlink($fileName);
                            symlink($file, $fileName);
                        } else {
                            @unlink($fileName);
                            $im->prepare();
                            $im->imResize($fileName);
                        }
                        unset($im);
                    } else {
                        foreach (glob($file."_*") as $filen) {
                           @unlink($filen);
                        }

                        @unlink($fileName."_thumb");

                        /* BORRAR TODOS LOS FICHEROS DEL MISMO PATRON*/
                    }
                    @unlink($fileMime);
                    $mime = i::mime_content_type($file);
                    $mimeFileName = i::clean($mime);
                    if (!file_exists($mimeFileName)) {
                        file_put_contents($mimeFileName, $mime);
                    }
                    @symlink($mimeFileName, $fileMime);

                }

                if (file_exists($fileName)) {
                    if ($doReturn && file_exists($fileName)) {
                        return array(
                            $valor,
                            $fileName,
                            array(
                            'ETag'=>$eTag,
                            'Content-Length'=>filesize($fileName),
                            'Content-Disposition'=>"inline; filename=\"{$valor}\"",
                            'Content-Type'=>file_get_contents($fileMime),
                            )
                        );
                    }
                    header('ETag: "'.$eTag.'"');
                    header("Content-Length: ".filesize($fileName));
                    header("Content-Type: ".file_get_contents($fileMime));
                    header("Content-Disposition: inline; filename=\"{$valor}\";");
                    readfile($fileName);
                }
                exit();
                break;
            case "file":
            // TODO
                if ($public === false) {
                    if (!file_exists($plantilla)) {
                        umask(0000);
                        if (!mkdir($plantilla, 0755)) {
                            die("Imposible crear directorio cache para la plantilla [".$plantilla."].");
                        }
                    } else {
                        if (!is_dir($plantilla)) die("Existe el elemento [".$plantilla."] pero es un fichero.");
                        if (!is_writeable($plantilla)) die("El directorio [".$plantilla."] no es escribible.");
                    }
                    chdir($plantilla);
                }

                $pl = new tablon_plantilla(tablon_AJAXjeditable::setPlantillaPath($plantilla));
                if (($idFLD = $pl->findField($campo)) === false) die("no se encuentra el campo");
                $nCampo = $pl->fields[$idFLD]->getSQLFLD();
                $nCampoNombreFichero = $pl->fields[$idFLD]->subFields[$pl->fields[$idFLD]->file_name]->getSQLFLD();
                $ret = $pl->getIdFromUnique(
                    $nCampoNombreFichero,
                    $valor,
                    array(
                        $pl->fields[$idFLD]->subFields[$pl->fields[$idFLD]->file_size]->getSQLFLD(),
                        $pl->fields[$idFLD]->subFields[$pl->fields[$idFLD]->file_type]->getSQLFLD(),
                        $pl->fields[$idFLD]->subFields[$pl->fields[$idFLD]->file_name]->getSQLFLD()
                    )
                );
                $tab = $pl->getTab();
                if ($ret===false) die($literal->l("No existe el fichero"));
                list($id, $size, $type, $url) = $ret;
                $file = $tab."_data_".$id;
                $fileMime = $file.'_'.$type;
                $fileName = (isset($_GET['thumb']))? $file."_thumb":$file;
                if ($dir = $pl->fields[$idFLD]->isfilesystem()) {
                    $r = $pl->fields[$idFLD]->getdir($file);
                    $pl->fields[$idFLD]->makedir($r);
                    $file = $pl->fields[$idFLD]->ruta.$file;
                    unset($_SESSION['file']);
                } else {
                    if ((isset($_GET['nocache']))
                       || (!( (@file_exists($file))
                       && (@filesize($file)==$size)
                       && (@file_exists($fileName))))) {
                        $fd = fopen($file, "wb");
                        $sql = "SELECT uncompress(data) as data
                                    FROM vertedero
                                    WHERE id_fich='".$tab."_data_".$id."'
                                    ORDER BY id";

                        $c = new con($sql);
                        if ($c->getNumRows() <=0 ) {
                            fclose($fd);
                            unlink($file);
                            die("Error ideterminado");
                        } else {
                            while ($row = $c->getResult()) {
                                fwrite($fd, ($row['data']));
                            }
                            fclose($fd);
                            chmod($file, 0666);
                        }
                    }
                }
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($file));
                header("Content-type: ".i::mime_content_type($file));
                header('Content-Disposition: attachment; filename="'.$cache.$url.'"');
                readfile($file);
                exit();
                break;
            case "filefs":
                $pl = new tablon_plantilla(tablon_AJAXjeditable::setPlantillaPath($plantilla));
                if (($idFLD = $pl->findField($campo)) === false) die("no se encuentra el campo");
                $nCampo = $pl->fields[$idFLD]->getSQLFLD();
                $nCampoNombreFichero = $pl->fields[$idFLD]->subFields[$pl->fields[$idFLD]->file_name]->getSQLFLD();
                $ret = $pl->getIdFromUnique(
                    $nCampoNombreFichero,
                    $valor,
                    array(
                        $pl->fields[$idFLD]->subFields[$pl->fields[$idFLD]->file_size]->getSQLFLD(),
                        $pl->fields[$idFLD]->subFields[$pl->fields[$idFLD]->file_type]->getSQLFLD(),
                        $pl->fields[$idFLD]->subFields[$pl->fields[$idFLD]->file_name]->getSQLFLD()
                    )
                );
                if ($ret===false) die($literal->l("No existe el fichero"));
                list($id, ) = $ret;
                $file = $pl->fields[$idFLD]->getRealPath($id);
                if (!file_exists($file)) die($literal->l("No existe el fichero"));
                $type = i::mime_content_type($file);
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".filesize($file));
                header("Content-type: ".$type);
                if ( (!preg_match("/image/", $type)) && (!$noDownload) ) {
                    header('Content-Disposition: attachment; filename="'.$valor.'"');
                }
                readfile($file);
                exit();
                break;
        }
    }

    static function foo()
    {
    }

    protected function getHistory()
    {
        return $this->history;
    }
}
