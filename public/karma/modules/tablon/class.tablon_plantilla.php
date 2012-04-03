<?php
/**
 * Fichero de clase para la carga de una plantilla de tablon [.plt]
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_plantilla
{
    public $aFields = array();
    public $fields = array();
    public $fieldsRel = array();
    public $aJS = array();
    public $jsIterator = 0;
    public $aCSS = array();
    public $cssIterator = 0;
    public $onnew = array();

    private $_file;
    private $_tab;
    private $_distinct = false;
    private $_orderBy=false;
    private $_groupBy = false;
    private $_leftTabs = array("lefttab"=>array(),"leftcond"=>array(),"leftwhere"=>array());
    private $_genero;
    private $_entity;
    private $_id;
    private $_deleted = false;
    private $_sizeofFields;
    private $_defaultFld;
    private $_trigger = false;
    private $_triggerOn = array();
    private $_triggerParams;
    private $_triggerParamsEsp = false;
    private $_conds;
    private $_onInsert = false;
    private $_refreshWhen = array();
    private $_notifyWhenInsert = array();
    private $_notifyWhenInsertStr = false;
    private $_oPlt = false;
    private $_preForm;
    private $_relTab;
    private $_relId;
    private $_relIdMain = false;
    private $_relIdMass = false;
    private $_relExcludeCurrentId = false;

    protected $l;
    public $oncopy = false;
    public $nocopy = false;
    public $anyFLDemail;


    /*
     * Para suplir el comportamiendo de las unique index compuestas (de + de 1 columna) de mysql,
     * dado que en este caso MySQL considera los valores
     * null diferentes entre sí. Es decir, si tenemos, por ejemplo:
     *         - Tenemos un unique de las columnas col1 y col2
     *         - En la tabla de BBDD tenemos las siguientes filas de valores para dichas columnas:
     *         col1    col2
     *         ----    ----
     *          2         5
     *          2         6
     *          3         null
     *          null     null
     *         - Al intentar insertar (2,5), (2, 6) daría error.
     *         - Al insertar (3, null) o (null, null) en cambio no.
     * Por lo  que:
     *         - $unique_index_multi: Será el array de arrays con los índices de los campos que actuarán como conjunto de índice único (puede haber muchos unique...)
     *         - $unique_null_use: si queremos el comportamiendo de MySQL valor "nulls_different" (por defecto), si queremos que los null se tomen como
     * valores iguales valor "nulls_equal"
     *         - $unique_error_msj: si queremos que en el mensaje de error de duplicidad, aparecezcan listados los nombres de campos que nosostros indiquemos
     * en el plt (un string).
     */
    private $_uniqueIndexMulti = false;
    private $_uniqueNullUse = false;
    private $_uniqueErrorMsj = false;

    protected $_kRegistry = null;

    /*private $pretrigger = false;
    private $pretriggerOn = array();
    private $pretriggerParams;*/

    function __construct($file, $noFields = false)
    {
        $this->_kRegistry = KarmaRegistry::getInstance();
        if (!$this->_kRegistry->isDefined('lang')) {
            new krm_menu();
        }
        $this->l = new k_literal($this->_kRegistry->get('lang'));

        $this->_file = $file;
        if (!file_exists($this->_file)) {
            return false;
        }

        $aFields = parse_ini_file($this->_file, true);

        $this->_tab = $aFields['::main']['tab'];
        if (isset($aFields['::main']['lefttabs'])) {
            if (!empty($aFields['::main']['lefttabs'])) {
                $this->_leftTabs['lefttab'] = explode("|", $aFields['::main']['lefttabs']);
                if (isset($aFields['::main']['leftconds'])) {
                    if (!empty($aFields['::main']['leftconds'])) {
                        $this->_leftTabs['leftcond'] = explode("|", $aFields['::main']['leftconds']);
                    }
                    if (!empty($aFields['::main']['leftwhere'])) {
                        $this->_leftTabs['leftwhere'] = explode("|", $aFields['::main']['leftwhere']);
                    }
                }
            }

        }
        if (isset($aFields['::main']['distinct'])) {
            if (!empty($aFields['::main']['distinct'])) {
                $this->_distinct = true;
            }
        }
        if (isset($aFields['::main']['trigger'])) {
            if (isset($aFields['::main']['triggerOn']) && !empty($aFields['::main']['triggerOn'])) {
                $this->_triggerOn = explode("|", $aFields['::main']['triggerOn']);
                $this->_trigger = $aFields['::main']['trigger'];
                if (isset($aFields['::main']['triggerParams']) && !empty($aFields['::main']['triggerParams'])) {
                    $this->_triggerParams = $aFields['::main']["triggerParams"];
                }
                if (isset($aFields['::main']['triggerParamsEspecial']) && !empty($aFields['::main']['triggerParamsEspecial'])) {
                    $this->_triggerParamsEsp = $aFields['::main']["triggerParamsEspecial"];
                }
            }

        }
        if (isset($aFields['::main']['oninsert'])) {
            $this->_onInsert = $aFields['::main']['oninsert'];

        }
        if (isset($aFields['::main']['oncopy'])) { /*EIDER*/
            $this->oncopy = $aFields['::main']['oncopy'];
            if (isset($aFields['::main']['nocopy'])) {
                $this->nocopy = $aFields['::main']['nocopy'];
            }
        }
        $this->_genero = $aFields['::main']['genero'];



        if (isset($aFields['::main']['entity'. '::'. $this->_kRegistry->get('lang')])) {
            $this->_entity = $aFields['::main']['entity'. '::'. $this->_kRegistry->get('lang')];
        } else {
            $this->_entity = $aFields['::main']['entity'];
        }


        if (isset($aFields['::main']['reltab'])) $this->_relTab = $aFields['::main']['reltab'];
        if (isset($aFields['::main']['relid'])) $this->_relId = $aFields['::main']['relid'];
        if (isset($aFields['::main']['relidmain'])) $this->_relIdMain = $aFields['::main']['relidmain'];
        if (isset($aFields['::main']['relidmass'])) $this->_relIdMass = $aFields['::main']['relidmass'];
        if (isset($aFields['::main']['relexcludeCurrentid'])) $this->_relExcludeCurrentId = $aFields['::main']['relexcludeCurrentid'];

        if (isset($aFields['::main']['preform'])) {
            $preFormLiteral = KarmaRegistry::getInstance()->get('translator')->translate($aFields['::main']['preform'], $aFields['::main'], 'preform');
            $this->_preForm = ((isset($aFields['::main']['preform']))? $preFormLiteral:false);
        } else {
            $this->_preForm = false;
        }



        if (isset($aFields['::main']['order'])) {
            $this->_orderBy = $aFields['::main']['order'];
        }
        if (isset($aFields['::main']['groupby'])) {
            $this->_groupBy = $aFields['::main']['groupby'];
        }
        if (isset($aFields['::main']['defaultFLD'])) {
            $this->_defaultFld = $aFields['::main']['defaultFLD'];
            if (preg_match("/::/", $this->_defaultFld)) list(, $this->_defaultFld) = explode("::", $this->_defaultFld);
        }
        if (isset($aFields['::main']['fixSlashes'])) {
            $this->_fixSlashes = $aFields['::main']['fixSlashes'];
        }
        if (isset($aFields['::main']['delete'])) $this->_deleted = $aFields['::main']['delete'];
        $this->_id = $aFields['::main']['id'];

        if (isset($aFields['::main']['deleted'])) $this->_deleted = $aFields['::main']['deleted'];

        if (isset($aFields['::main']['onnew'])) {
            $this->loadOnNewFields($aFields);
        }

        if (isset($aFields['::main']['conds'])) {
            $this->_conds = explode(',', $aFields['::main']['conds']);
        }
        if (isset($aFields['::main']['refreshwhen'])) {
            $this->_refreshWhen = explode(',', $aFields['::main']['refreshwhen']);
        }

        if (isset($aFields['::main']['notifywheninsert'])) {
            $this->_notifyWhenInsert = explode(",", $aFields['::main']['notifywheninsert']);
        }

        if (isset($aFields['::main']['notifywheninsert_str'])) {
            $this->_notifyWhenInsertStr = $aFields['::main']['notifywheninsert_str'];
        }

        if (isset($aFields['::main']['oplt'])) {
            $this->_oPlt = $aFields['::main']['oplt'];
        }

        if (isset($aFields['::main']['unique_multi'])) {
            $this->_uniqueIndexMulti = array();
            $aIndUniques = explode("|", $aFields['::main']['unique_multi']);
            if (isset($aFields['::main']['unique_null_use'])) {
                $aUniqueUse = array();
                $aUniqueUse = explode("|", $aFields['::main']['unique_null_use']);
                $this->_uniqueNullUse = array();
            }
            if (isset($aFields['::main']['unique_error_fields'])) {
                $aUniqueErrorMsj = array();
                $aUniqueErrorMsj = explode("|", $aFields['::main']['unique_error_fields']);
                $this->_uniqueErrorMsj = array();
            }
            for ($i=0;$i<count($aIndUniques);$i++) {
                $this->_uniqueIndexMulti[$i] = explode(",", $aIndUniques[$i]);
                if (isset($aFields['::main']['unique_null_use'])) {
                    if (isset($aUniqueUse[$i]) && !empty($aUniqueUse[$i])) {
                        $this->_uniqueNullUse[$i] = $aUniqueUse[$i];
                    } else {
                        $this->_uniqueNullUse[$i] = $aUniqueUse[count($aUniqueUse)-1];
                    }
                }
                if (isset($aFields['::main']['unique_error_fields'])) {
                    if (isset($aUniqueErrorMsj[$i]) && !empty($aUniqueErrorMsj[$i])) {
                        $this->_uniqueErrorMsj[$i] = $aUniqueErrorMsj[$i];
                    } else {
                        $this->_uniqueErrorMsj[$i] = $aUniqueErrorMsj[count($aUniqueErrorMsj)-1];
                    }
                }
            }

        }

        if ($noFields) return;

        /*
         * Si en el plt hay condiciones de mostrado de campos.
         * Los campos a mostrar se decidirán en base a lo devuelto
         * por la función indicada en el "switcher" del switch.
         */
        $encontrado = false;
        if (isset($aFields['::main']['SWITCH'])) {
            $aSwitcher = explode("|", $aFields['::main']['SWITCH']);
            if (count($aSwitcher)>0) {
                $newArray=array();
                for ($i=0; $i<count($aSwitcher); $i++) {
                    $switcherIdx = $aSwitcher[$i];

                    if (isset($aFields[$switcherIdx])) {
                        $fldSwitch = $aFields[$switcherIdx];
                        if (isset($fldSwitch['SWITCH']) && $fldSwitch['SWITCH'] == "start" && isset($fldSwitch['SWITCHER'])) {
                            $switcher = $fldSwitch['SWITCHER'];
                            //$start=true;
                            if (isset($fldSwitch['SWITCHER_params'])) {
                                $switcherParams = $fldSwitch['SWITCHER_params'];
                                $retval = call_user_func($switcher, $switcherParams);
                            } else {
                                $retval = call_user_func($switcher);
                            }
                            $search=$retval."$$".$switcherIdx;
                            $start=$end= false;
                            $encontrado = true;

                            foreach ($aFields as $key=>$arr) {
                                switch(true) {
                                    case $key==$switcherIdx:
                                        $start=true;
                                        //unset($aFields["SWITCH"]);
                                        break;
                                    case $key=="ENDSWITCH::".$switcherIdx:
                                        unset($aFields[$key]);
                                        $end=true;
                                        break;
                                    case $start&&!$end:
                                        if ($key==$search) {
                                            $newArray[$switcherIdx] = $arr;
                                            unset($aFields[$key]);
                                        } else {
                                            unset($aFields[$key]);
                                        }
                                        break;
                                }
                                if (isset($aFields[$key])) {
                                    $newArray[$key] = $arr;
                                }
                                continue;
                            }
                        } else {
                            unset($aFields[$switcherIdx]);
                            continue;
                        }
                    } else {
                        continue;
                    }
                }
                if (count($newArray)>0) {
                    $aFields = $newArray;
                }
            }
        }
        unset($aFields['::main']);
        $this->aFields = $aFields;
        $this->doFields();
    }

    public function loadOnNewFields($aFields)
    {
        $tmponnew = explode(",", $aFields['::main']['onnew']);
        $this->onnew = array();
        foreach ($tmponnew as  $no) {
            if (trim($no)=="") continue; //si vacio fuera
            if (preg_match("/\*/", $no)) { // si tiene asteriskos ...
                $exp = str_replace("*", ".", $no);
                foreach ($aFields as $field=>$foo) {
                    if (preg_match("/".$exp."/", $field)) {
                        $this->onnew[] = $field;
                    }
                }
            }
            $this->onnew[] = $no;
        }
    }

    public function getOplt()
    {
        return $this->_oPlt;
    }

    public function getNotifyWhenInsert()
    {
        if (sizeof($this->_notifyWhenInsert) > 0) return $this->_notifyWhenInsert;
        return false;
    }

    public function getNotifyWhenInsertStr()
    {
        if (sizeof($this->_notifyWhenInsertStr) > 0) return $this->_notifyWhenInsertStr;
        return false;
    }

    public function getRefreshwhen()
    {
        if (is_array($this->_refreshWhen) && !empty($this->_refreshWhen)) {
            return $this->_refreshWhen;
        } else {
            return false;
        }
    }

    public function hasJS()
    {
        return sizeof($this->aJS)>0;
    }

    public function getJS()
    {
        $retValue = false;
        if(isset($this->aJS[$this->jsIterator]))
        {
            $retValue = $this->aJS[$this->jsIterator];
        }
        $this->jsIterator++;
        return $retValue;
    }

    public function setJS($ajs)
    {
        $this->aJS = array_merge($this->aJS, $ajs);
    }

    public function hasCSS()
    {
        return sizeof($this->aCSS)>0;
    }

    public function getCSS()
    {
        $retValue = false;
        if(isset($this->aCSS[$this->cssIterator]))
        {
            $retValue = $this->aCSS[$this->cssIterator];
        }
        $this->cssIterator++;
        return $retValue;
    }

    public function setCSS($acss)
    {
        $this->aCSS = array_merge($this->aCSS, $acss);
    }

    /*
     * Cuidado!!! aquí se usan tanto $accionEjec como $id, aunque no lo parezca
     * ya que el $this->_triggerParams contiene el nombre del parametro que se quiere pasar...
     */
    public function getTrigger($id = "", $accionEjec = "")
    {
        if (isset($this->_triggerParams) && !empty($this->_triggerParams)) {
            $elParam = $this->_triggerParams;
            eval("\$elParam = \"$elParam\";");
            $this->_trigger = preg_replace("/\(.*\)/", "(" . $elParam . ")", $this->_trigger);
        }
        return $this->_trigger;
    }

    public function runTriggerNew($id = "", $accionEjec = "", $datos = false)
    {
        if ($this->_trigger !== false && isset($this->_triggerParamsEsp) && !empty($this->_triggerParamsEsp)) {
            $theTrigger = $this->_trigger;
            $theTrigger = preg_replace("/\(.*\)/", "", $this->_trigger);
            $retorno = call_user_func($theTrigger, $id, $accionEjec, $datos);
            return $retorno;
        }
        return false;
    }

    public function addPlt($path, $pl)
    {
        $aInfo = explode('::', $pl);
        if (file_exists($path . $aInfo[0])) {
            $aFields = parse_ini_file($path . $aInfo[0], true);
            unset($aInfo[0]);
            foreach ($aInfo as $field) {
                if (isset($aFields[$field])) {
                    $this->aFields[$field]=$aFields[$field];

                }
            }
            $this->doFields($aInfo);
        }
    }

    protected function doFields($field=false)
    {
        foreach ($this->aFields as $idx => $aFld) {
            if (isset($field)&&$field!=false) {
                if (!in_array($idx, $field)) continue;
            }
            if (preg_match("/::/", $idx)) continue;
            $objName = "tablon_FLD".strtolower($aFld['type']);
            if (!$this->anyFLDemail) $this->anyFLDemail = ($aFld['type']=="EMAIL")? true:false;

            if (isset($aFld['mutant']) && (bool)$aFld['mutant'] ) {
                if ((isset($aFld['mutant.getflag'])) && (isset($aFld['mutant.type'])) && (isset($_GET[$aFld['mutant.getflag']])) ) {
                    $objName = $aFld['mutant.type'];

                }
            }

            $fl = new $objName($aFld, $idx, $this->getBaseFile());

            if (method_exists($fl, "loadJS")) {

                $this->setJS($fl->loadJS());
            }


            if (method_exists($fl, "loadCSS"))
                $this->setCSS($fl->loadCSS());

            $this->fields[] = $fl;
        }
        $this->_sizeOfFields = sizeof($this->fields);

        /*
         * Cargando subfields y hidecond
         */
        foreach ($this->aFields as $idx => $aFld) {
            if (!preg_match("/::/", $idx)) continue;
            list($indice, $field) = explode("::", $idx);
            $fl = false;
            for ($i=0;$i<$this->_sizeOfFields;$i++) {
                if ($this->fields[$i]->getIndex() == $indice) {
                    $fl = $i;
                    break;
                }
            }

            if ($fl === false) continue;
            switch(true) {
                /*
                 * Determina que campos se esconden cuando
                 * el valor de $indice toma los valores listados en $aFld
                 */
                case $field=="__hidecond":
                    foreach ($aFld as $_fld => $_aFlds) {
                        $this->fields[$fl]->aHideConds[$_fld] = preg_split("/[|,]/", $_aFlds);
                    }
                    break;
                default:
                    $objName = "tablon_FLD".strtolower($aFld['type']);
                    $tmpOBJ = new $objName($aFld, $field, $this->getBaseFile());

                    if(method_exists($tmpOBJ, 'loadParentObj')) {
                        $tmpOBJ->loadParentObj($this->fields[$fl]);
                    }
                    $this->fields[$fl]->subFields[]  = $tmpOBJ;
                    if (method_exists($this->fields[$fl], "registerCustomSubField")) {
                        $this->fields[$fl]->registerCustomSubField(
                            $aFld['type'],
                            sizeof($this->fields[$fl]->subFields)-1
                        );
                    }
                    $this->fields[$fl]->sizeofsubFields++;
                    break;
            }
        }

    }
    
    public function drawCsvHead()
    {
        $ret = '<tr>';
        for ($i=0;$i<$this->_sizeOfFields;$i++) {
            $ret .= '<th '.$idLoc.'>';
            $ret .= utf8_decode($this->fields[$i]->getTitle());
            $ret .= '</th>';
        }
        $ret .= '</tr>';

        return $ret;
    }
    
    public function drawHead($opciones = false, $noshownFields = array(), $checkBox = false, $url = '', $numlines = false)
    {

        $ret = '<tr class="tablon_tr_header">';
        if ($numlines) {
            $ret .= '<th class="multiselect"></th>';
        }
        if ($checkBox) {
            if ((string)$checkBox == 'multisearchable') {
                $ret .= '<th class="multiselect" id="mlMASTER">'
                     . '<input type="checkbox" id="chk_msMASTER_multisearchable"/></th>';
            } elseif ((string)$checkBox == "2") {
                $ret .= '<th class="multiselect"></th>';
            } else {
                $ret .= '<th class="multiselect" id="mlMASTER"><input type="checkbox" id="chk_msMASTER"/></th>';
            }
        }
        for ($i=0;$i<$this->_sizeOfFields;$i++) {

            $currentIndex = $this->fields[$i]->getIndex(true);

            if (in_array($currentIndex, $noshownFields)) {
                continue;
            }

            $currentIndex = $this->fields[$i]->getIndex();
            if ((isset($_GET['order'])) && ((int)$_GET['order'] == $i)) {
                if ((isset($_GET['orderType'])) && ($_GET['orderType']=="desc")) {
                    $imgOrder = 'desc.gif';
                    $urlExtra = '';
                } else {
                    $imgOrder = 'asc.gif';
                    $urlExtra = 'orderType=desc&amp;';
                }
            } else {
                $imgOrder = 'order.gif';
                $urlExtra = '';
            }

            // Permitimos que una misma columna tenga ambos valores.... ¿Por que está esto aquí? :'(
            $dataLoc = "";
            if (isset($this->aFields[$currentIndex]['locSt']) && $this->aFields[$currentIndex]['locSt'] == "1") {
                $dataLoc .= " data-locst='true'";
            }             
            if (isset($this->aFields[$currentIndex]['locPath']) && $this->aFields[$currentIndex]['locPath']=="1") {
                $dataLoc .= " data-locpath='true'";
            }

            $ret .= '<th '.$dataLoc.'><a href="'.$url.$urlExtra.'order='.$i.'&amp;">';
            $ret .= '<img class="thImg" src="./icons/'.$imgOrder.'" alt="ordernar por '.$this->fields[$i]->getTitle().'" />';
            $ret .= '<span class="thSpan">' . $this->fields[$i]->getTitle() . '</span>';

            $ret .= '</a>';
            $ret .= '</th>';
            if ($this->fields[$i]->hasSubFields()) {
                for ($j=0;$j<$this->fields[$i]->sizeofsubFields;$j++) {
                    if (in_array($this->fields[$i]->subFields[$j]->getIndex(), $noshownFields)) continue;
                    $ret .= '<th>';
                    $ret .= $this->fields[$i]->subFields[$j]->getTitle();
                    $ret .= '</th>';
                }
            }
        }
        if ($opciones) $ret .= '<th>'.$this->l->l('Opciones').'</th>';
        $ret .= '</tr>';

        return $ret;
    }
    public function getTab()
    {
        return $this->_tab;
    }

    public function getDistinct()
    {
        return $this->_distinct;
    }

    public function getGroupBy()
    {
        return $this->_groupBy;
    }

    public function getOrderBy()
    {
        return $this->_orderBy;
    }

    public function getALeftTabs()
    {
        return $this->_leftTabs;
    }

    public function getLeftTabs()
    {
        return $this->_leftTabs['lefttab'];
    }

    public function getLeftConds()
    {
        return $this->_leftTabs['leftcond'];
    }
    public function getLeftWhere()
    {
        return $this->_leftTabs['leftwhere'];
    }

    /*
    *    Sets left tab, left cond (on), left where (for main query)
    */
    public function setALeftTabs($t, $c, $w)
    {
        $this->_leftTabs['lefttab'][] = $t;
        $this->_leftTabs['leftcond'][] = $c;
        $this->_leftTabs['leftwhere'][] = $w;
    }

    public function getID()
    {
        return $this->_id;
    }

    public function isFixSlashes()
    {
        return $this->_fixSlashes? $this->_fixSlashes: false;
    }


    public function getPreForm()
    {
        return $this->_preForm;
    }

    public function getrelID()
    {
        return $this->_relId;
    }

    public function getrelTab()
    {
        return $this->_relTab;
    }

    public function getrelIDmain()
    {
        return $this->_relIdMain;
    }

    public function getrelIDmass()
    {
        return $this->_relIdMass;
    }

    public function getrelExcludeCurrentID()
    {
        return $this->_relExcludeCurrentId;
    }

    public function getNumFields()
    {
        return $this->_sizeOfFields;
    }

    public function getDefaultFLD()
    {
        return $this->_defaultFld;
    }

    public function getDeletedFLD()
    {
        return $this->_deleted;
    }

    public function getFile()
    {
        return $this->_file;
    }

    public function getBaseFile()
    {
        return basename($this->_file);
    }

    public function getEntidad()
    {

        return $this->_entity;
    }

    public function getGenero()
    {
        return $this->_genero;
    }

    public function findField($sqlFLD)
    {
        for ($i=0;$i<$this->_sizeOfFields;$i++) {
            if ($sqlFLD == $this->fields[$i]->getIndex()) return $i;
        }
        return false;
    }

    public function saveSingleField($idFLD, $value, $id, $idSubFLD = false, $conds=array())
    {

        $force = false;
        if (method_exists($this->fields[$idFLD], "getForceUpdate")) {
            $force = $this->fields[$idFLD]->getForceUpdate();
        }
        if (isset($this->_uniqueIndexMulti) && $this->_uniqueIndexMulti!==false && is_array($this->_uniqueIndexMulti)) {
            $aliasColumns = array();
            $aDatos = array("indexFld"=>$this->fields[$idFLD]->getIndex(),"valorFLD"=>$value);
            $arrayKey = false;
            $response = new stdClass();
            $response->aliasColumns = $aliasColumns;
            $response->arrayKey = $arrayKey;
            $esUnico = $this->uniqueCheckUpdate($aDatos, $id, $response);
            $aliasColumns = $response->aliasColumns;
            $arrayKey = $response->arrayKey;
            if ($esUnico === false) {
                if ($this->_uniqueErrorMsj === false) {
                    $uniqueAliasStr = implode(", ", $aliasColumns);
                } else {
                    $uniqueAliasStr = $this->_uniqueErrorMsj[$arrayKey];
                }
                return array("1062","Duplicated entry for uninque index declared on columns: '".$uniqueAliasStr."'");
            }
        }


        if ($this->fields[$idFLD]->getSQLType() !== false
           || ($this->fields[$idFLD]->getSQLType()===false
           && $force===true)) {

            if ($idSubFLD !== false) {

                $fl = &$this->fields[$idFLD]->subFields[$idSubFLD];
                if (!$fl->getIsReal()) {
                    return false;
                }

                return array($fl->getSQLFLD(), $fl->getMysqlValue($value));
            }
            $fl = &$this->fields[$idFLD];
            $aValues = array();
            if (method_exists($this->fields[$idFLD], "preInsertCheckValue")) {
                if (($ret = $this->fields[$idFLD]->preInsertCheckValue($value))!==true) {
                    return $ret;
                }
            }
            $valMysql = $fl->getMysqlValue($value);
            // si devuelve array, es error... fatal, pero bueno.
            if (is_array($valMysql)) {
                return $valMysql;
            }
            if (empty($valMysql) || $valMysql == "''" || $valMysql == '') {
                $valEmpty = $fl->valueOnEmpty();
                $valMysql = $valEmpty;
            }
            if ($valMysql != "noInsertBecauseFile"
               && $valMysql != "noInsertBecauseFileEsp"
               && (!is_array($valMysql) || (is_array($valMysql) && $valMysql[0] != "noInsertBecauseFileEsp"))) {
                if (!(i::detectUTF8($valMysql))) {
                    $valMysql = utf8_encode($valMysql);
                }
                $aValues[] = $fl->getSQLFLD().'='.$valMysql.'';
                if ($clean = $this->fields[$idFLD]->toClean()) {
                    $aClean = $this->fields[$idFLD]->toCleanUnique();
                    if ($aClean!==false && is_array($aClean)) {
                        $aValues[] = $clean
                                    . "= '"
                                    . $this->doClean(
                                        $clean,
                                        $valMysql,
                                        tablon_FLD::cleanMySQLValue($id),
                                        $this->_id,
                                        $aClean
                                    )
                                    ."'";
                    } else {
                        $aValues[] = $clean
                                    . "= '"
                                    . $this->doClean(
                                        $clean,
                                        $valMysql,
                                        tablon_FLD::cleanMySQLValue($id),
                                        $this->_id
                                    )
                                    . "'";
                    }
                }
            }

            for ($i=0;$i<$fl->sizeofsubFields;$i++) {
                //echo "*Campo dependiente";

                $ret = $this->saveSingleField($idFLD, $value, $id, $i);

                if (is_array($ret)) {
                    if (empty($ret[1]) || $ret[1] == '') {
                        $valEmpty = $fl->valueOnEmpty();
                        $ret[1] = $valEmpty;

                    }
                    $aValues[] = $ret[0].'='.$ret[1].'';
                }
            }


            if ($this->getTrigger($id)!==false) {
                if (isset($this->_triggerParamsEsp) && $this->_triggerParamsEsp !== false) {
                    if (in_array('before_update', $this->_triggerOn)) {
                        $_datosForTrigger = array(
                            "dsdPlt" => $this,
                            "idFLD" => $idFLD,
                            "value" => $value,
                            "id" => $id,
                            "idSubFLD" => $idSubFLD,
                            "aValues" => $aValues,
                            "valMysql" => $valMysql
                        );
                        if ($conds!==false && is_array($conds)) {
                            $_datosForTrigger['conds'] = $conds;
                        }
                        $accionEjec = 'before_update';
                        $cad = $this->runTriggerNew($id, $accionEjec, $_datosForTrigger);
                        if (is_array($cad)) {
                            return $cad;
                        }
                        if (is_string($cad) && $cad=="DoNoUpdateBecauseTriggerSays") {
                            return $value;
                        }
                    }
                } else {
                    if (in_array('before_update', $this->_triggerOn)) {
                        $accionEjec = 'before_update';
                        $funcionTrigger = $this->getTrigger($id, $accionEjec).';';
                        eval("\$cad = $funcionTrigger");
                        if (is_array($cad)) {
                            return $cad;
                        }
                    }
                }
            }

            if ($fl->getTrigger($id)!==false) {
                if (isset($fl->triggerParamsEsp) && $fl->triggerParamsEsp !== false) {
                    if (in_array('before_update', $fl->getTriggerOn())) {
                        $_datosForTrigger = array(
                            "dsdPlt" => $this,
                            "idFLD" => $idFLD,
                            "value" => $value,
                            "id" => $id,
                            "idSubFLD" => $idSubFLD,
                            "aValues" => $aValues,
                            "valMysql" => $valMysql
                        );
                        if ($conds!==false && is_array($conds)) {
                            $_datosForTrigger['conds'] = $conds;
                        }
                        $accionEjec = 'before_update';
                        $cad = $fl->runTriggerNew($id, $accionEjec, $_datosForTrigger);
                        if (is_array($cad)) {
                            return $cad;
                        }
                        if (is_string($cad) && $cad=="DoNoUpdateBecauseTriggerSays") {
                            return $value;
                        }
                    }
                } else {
                    if (in_array('before_update', $fl->getTriggerOn())) {
                        $accionEjec = 'before_update';
                        $funcionTrigger = $fl->getTrigger($id, $accionEjec).';';
                        eval("\$cad = $funcionTrigger");
                        if (is_array($cad)) {
                            return $cad;
                        }
                    }
                }
            }

            if (sizeof($aValues)<=0) {
                if ($this->getTrigger($id)!==false) {
                    if (isset($this->_triggerParamsEsp) && $this->_triggerParamsEsp !== false) {
                        if (in_array('update', $this->_triggerOn)) {
                            $_datosForTrigger = array(
                                "dsdPlt" => $this,
                                "idFLD" => $idFLD,
                                "value" => $value,
                                "id" => $id,
                                "idSubFLD" => $idSubFLD,
                                "aValues" => $aValues,
                            "valMysql" => $valMysql
                            );
                            if ($conds!==false && is_array($conds)) {
                                $_datosForTrigger['conds'] = $conds;
                            }
                            $accionEjec = 'update';
                            $cad = $this->runTriggerNew($id, $accionEjec, $_datosForTrigger);
                            if (is_array($cad)) {
                                return $cad;
                            }
                        }
                    } else {
                        if (in_array('update', $this->_triggerOn)) {
                            $accionEjec = 'update';
                            $funcionTrigger = $this->getTrigger($id, $accionEjec).';';
                            eval("\$cad = $funcionTrigger");
                            if (is_array($cad)) {
                                return $cad;
                            }
                        }
                    }
                }

                if ($fl->getTrigger($id)!==false) {
                    if (isset($fl->triggerParamsEsp) && $fl->triggerParamsEsp !== false) {
                        if (in_array('update', $fl->getTriggerOn())) {
                            $_datosForTrigger = array(
                                "dsdPlt" => $this,
                                "idFLD" => $idFLD,
                                "value" => $value,
                                "id" => $id,
                                "idSubFLD" => $idSubFLD,
                                "aValues" => $aValues,
                                "valMysql" => $valMysql
                            );
                            if ($conds!==false && is_array($conds)) {
                                $_datosForTrigger['conds'] = $conds;
                            }
                            $accionEjec = 'update';
                            $cad = $fl->runTriggerNew($id, $accionEjec, $_datosForTrigger);
                            if (is_array($cad)) {
                                return $cad;
                            }
                        }
                    } else {
                        if (in_array('update', $fl->getTriggerOn())) {
                            $accionEjec = 'update';
                            $funcionTrigger = $fl->getTrigger($id, $accionEjec).';';
                            eval("\$cad = $funcionTrigger");
                            if (is_array($cad)) {
                                return $cad;
                            }
                        }
                    }
                }
                if (is_array($valMysql) && $valMysql[0] == "noInsertBecauseFileEsp" && $valMysql[1] == "error") {
                    return array($valMysql[2], $valMysql[3]);
                }
                return $fl->drawTableValue($value);
            }
            /*
             * Si el campo tiene el atributo "fromAnotherTab" a true,
             * quiere decir que el valor viene de otra tabla (un left join, por ejemplo),
             * por lo que no hay que realizar el update sobre este campo.
             * Si se quiere modificar habrá que utilizar triggers, que si puede dispararlos.
             */
            if ($this->fields[$idFLD]->ifFieldOfAnotherTab() !== true) {
                $aValuesT = array();
                foreach ($conds as $cond => $val) {
                    if (preg_match("/^triggerCond_(.*)/", $cond, $forTrigger)) {
                        $aValuesT[] = $cond . " = " . $val;
                    } else {
                        $aValues[] = $cond . " = " . $val;
                    }
                }

                $sql = 'update '.$this->_tab.' set ';
                $sql .= implode(',', $aValues);
                $sql .= ' where '.$this->_id.'=\''.tablon_FLD::cleanMySQLValue($id).'\'';
                $con = new con($sql);
                if ($con->error()) return array($con->getErrorNumber(), $con->getError());
            }

            if ($this->getTrigger($id)!==false) {
                if (isset($this->_triggerParamsEsp) && $this->_triggerParamsEsp !== false) {
                    if (in_array('update', $this->_triggerOn)) {
                        $_datosForTrigger = array(
                            "dsdPlt" => $this,
                            "idFLD" => $idFLD,
                            "value" => $value,
                            "id" => $id,
                            "idSubFLD" => $idSubFLD,
                            "aValues" => $aValues,
                            "aValuesT" => $aValuesT
                        );
                        if ($conds!==false && is_array($conds)) {
                            $_datosForTrigger['conds'] = $conds;
                        }
                        $accionEjec = 'update';
                        $cad = $this->runTriggerNew($id, $accionEjec, $_datosForTrigger);
                        if (is_array($cad)) {
                            return $cad;
                        }
                    }
                } else {
                    if (in_array('update', $this->_triggerOn)) {
                        $accionEjec = 'update';
                        $funcionTrigger = $this->getTrigger($id, $accionEjec).';';
                        eval("\$cad = $funcionTrigger");
                        if (is_array($cad)) {
                            return $cad;
                        }
                    }
                }
            }

            if ($fl->getTrigger($id)!==false) {
                if (isset($fl->triggerParamsEsp) && $fl->triggerParamsEsp !== false) {
                    if (in_array('update', $fl->getTriggerOn())) {
                        $_datosForTrigger = array(
                            "dsdPlt" => $this,
                            "idFLD" => $idFLD,
                            "value" => $value,
                            "id" => $id,
                            "idSubFLD" => $idSubFLD,
                            "aValues" => $aValues,
                            "valMysql" => $valMysql
                        );
                        if ($conds!==false && is_array($conds)) {
                            $_datosForTrigger['conds'] = $conds;
                        }
                        $accionEjec = 'update';
                        $cad = $fl->runTriggerNew($id, $accionEjec, $_datosForTrigger);
                        if (is_array($cad)) {
                            return $cad;
                        }
                    }
                } else {
                    if (in_array('update', $fl->getTriggerOn())) {
                        $accionEjec = 'update';
                        $funcionTrigger = $fl->getTrigger($id, $accionEjec).';';
                        eval("\$cad = $funcionTrigger");
                        if (is_array($cad)) {
                            return $cad;
                        }
                    }
                }
            }

            //var_dump($aValues, $value);

            if (method_exists($this->fields[$idFLD], "afterupdate")) {
                $this->fields[$idFLD]->afterupdate($value, tablon_FLD::cleanMySQLValue($id), $this->_tab);
            }

            if (method_exists($fl, "processReturnJSONValue")) {
                return $fl->processReturnJSONValue($value, $this, tablon_FLD::cleanMySQLValue($id));
            }



            return $fl->drawTableValue($value);
        }


        return $fl->drawTableValue($value);
    }

    public function doDelete($id, $op='1', $conds=false)
    {
        if (($deleteField = $this->getDeletedFLD())===false || (($deleteField = $this->getDeletedFLD())!==false && $op=='1')) {
            $enCualOp = "before_delete";
            $condDevTrigger = "DoNoDeleteBecauseTriggerSays";
        } else {
            $enCualOp = "before_undelete";
            $condDevTrigger = "DoNoUndeleteBecauseTriggerSays";
        }
        if ($this->getTrigger($id)!==false) {
            if (isset($this->_triggerParamsEsp) && $this->_triggerParamsEsp !== false) {
                if (in_array($enCualOp, $this->_triggerOn)) {
                    $_datosForTrigger = array(
                        "dsdPlt" => $this,
                        "id" => $id,
                        "op" => $op,
                        "conds" => $conds
                    );
                    $accionEjec = $enCualOp;
                    $cad = $this->runTriggerNew($id, $accionEjec, $_datosForTrigger);
                    if (is_array($cad)) {
                        return $cad;
                    }
                    if (is_string($cad) && $cad==$condDevTrigger) {
                        return true;
                    }
                }
            } else {
                if (in_array($enCualOp, $this->_triggerOn)) {
                    $accionEjec = $enCualOp;
                    $funcionTrigger = $this->getTrigger($id, $accionEjec).';';
                    eval("\$cad = $funcionTrigger");
                    if (is_array($cad)) {
                        return $cad;
                    } elseif ($cad === "donotdelete" || $cad === "donotdelete_inuse") {
                        return $cad;
                    }
                }
            }
        }
        for ($i=0;$i<$this->_sizeOfFields;$i++) {
            if (in_array($enCualOp, $this->fields[$i]->getTriggerOn())) {
                $_datosForTrigger = array(
                    "dsdPlt" => $this,
                    "id" => $id,
                    "op" => $op,
                    "conds" => $conds
                );
                $accionEjec = $enCualOp;
                $cad = $this->fields[$i]->runTriggerNew("", $accionEjec, $_datosForTrigger);
                if (is_array($cad)) {
                    return $cad;
                } elseif (is_string($cad) && $cad==$condDevTrigger) {
                        return true;
                }
            }
        }
        if (($deleteField = $this->getDeletedFLD()) === false) {
            $sql = 'DELETE FROM '.$this->_tab.' WHERE ';
            $sql .= $this->_id . '=' . tablon_FLD::getMysqldelValue($id) . ' limit 1';
        } else {
            $sql = 'UPDATE ' . $this->_tab . ' SET ';
            $sql .= ' ' . $deleteField . '= "' . $op . '"';
            $sql .= ' WHERE ' . $this->_id . '= "' . tablon_FLD::cleanMysqlValue($id) . '"';
        }
        $con = new con($sql);
        foreach ($this->fields as $idf=>$vlrf) {
            if (method_exists($this->fields[$idf], "afterdelete")) {
                $this->fields[$idf]->afterdelete(tablon_FLD::cleanMySQLValue($id), $this->_tab, $this->getDeletedFLD());
            }
        }
        if (($deleteField = $this->getDeletedFLD())===false || (($deleteField = $this->getDeletedFLD())!==false && $op=='1')) {
            $enCualOp = "delete";
        } else {
            $enCualOp = "undelete";
        }
        //if (($deleteField = $this->getDeletedFLD())===false || (($deleteField = $this->getDeletedFLD())!==false && $op=='1')) {
        if ($this->getTrigger($id)!==false) {
            if (isset($this->_triggerParamsEsp) && $this->_triggerParamsEsp !== false) {
                if (in_array($enCualOp, $this->_triggerOn)) {
                    $_datosForTrigger = array(
                        "dsdPlt" => $this,
                        "id" => $id,
                        "op" => $op,
                        "conds" => $conds
                    );
                    $accionEjec = $enCualOp;
                    $cad = $this->runTriggerNew($id, $accionEjec, $_datosForTrigger);
                    if (is_array($cad)) {
                        return $cad;
                    }
                }
            } else {
                if (in_array($enCualOp, $this->_triggerOn)) {
                    $accionEjec = $enCualOp;
                    $funcionTrigger = $this->getTrigger($id, $accionEjec).';';
                    eval("\$cad = $funcionTrigger");
                    if (is_array($cad)) {
                        return $cad;
                    }
                }
            }
        }
        for ($i=0;$i<$this->_sizeOfFields;$i++) {
            if (in_array($enCualOp, $this->fields[$i]->getTriggerOn())) {
                $_datosForTrigger = array(
                    "dsdPlt" => $this,
                    "id" => $id,
                    "op" => $op,
                    "conds" => $conds
                );
                $accionEjec = $enCualOp;
                $cad = $this->fields[$i]->runTriggerNew("", $accionEjec, $_datosForTrigger);
                if (is_array($cad)) {
                    if (is_string($cad[0]) && $cad[0]==$condDevTrigger) {
                        return $cad[1];
                    }
                    return $cad;
                }
            }
        }

        /* TODO - controlar SQL error*/
        if ($con->error()) return array($con->getErrorNumber(), $con->getError());
        return true;
    }

    public function dounDelete($id, $conds=false)
    {
        return $this->doDelete($id, '0', $conds);

    }

    public function getIdFromUnique($uniqueField, $uniqueValue, $extraFields)
    {
        $sql = 'SELECT '.$this->_id;
        foreach ($extraFields as $fld) $sql .= ','.$fld;
        $sql .= ' FROM '.$this->_tab.' ';
        $leftTabs = $this->getALeftTabs();
        if (isset($leftTabs) && is_array($leftTabs) && !empty($leftTabs)) {
            $leftTab = $leftTabs['lefttab'];
            $leftCond = $leftTabs['leftcond'];
            $leftWhere = $leftTabs['leftwhere'];
            if (sizeof($leftTab)>0 && sizeof($leftCond)>0 && sizeof($leftTab)==sizeof($leftCond)) {
                for ($i=0;$i<sizeof($leftTab);$i++) {
                    if ($i!=0) {
                        $sql.=" ";
                    }
                    $sql .= " LEFT JOIN ".$leftTab[$i]." ON(".$leftCond[$i].") ";
                }
            }
        }
        $sql .= ' WHERE ' . $uniqueField . '= "' . tablon_FLD::cleanMysqlValue($uniqueValue) . '" ';
        if (($deleteField = $this->getDeletedFLD())!==false) {
            $sql .= ' AND ' . $this->_tab . '.' . $deleteField . '= "0" ';
        }

        if (isset($leftWhere) && is_array($leftWhere) && sizeof($leftWhere)>0 ) {

            $sql .= ' AND '.implode(" AND ", $leftWhere)." ";
        }
        $sql .= ' LIMIT 1';
        $con = new con($sql);
        //echo $sql;
        if ($con->getNumRows()!=1) return false;
        $r = $con->getResult();

        $ret = array($r[$this->_id]);
        foreach ($extraFields as $fld) $ret[] = $r[$fld];
        return $ret;
    }

    public function loadSingleField($idFLD, $id, $raw = false)
    {
        if ($raw && method_exists($this->fields[$idFLD], "getSQLFLDRaw")) {
            $sql = 'select '.$this->fields[$idFLD]->getSQLFLDRaw($this->_tab).' as '.$this->fields[$idFLD]->getSQLFLD();
        } else {
            $sql = 'select '.$this->fields[$idFLD]->getSQLFLD();
        }
        $sql .= ' from '.$this->_tab.' ';
        $leftTabs = $this->getALeftTabs();
        if (isset($leftTabs) && is_array($leftTabs) && !empty($leftTabs)) {
            $leftTab = $leftTabs['lefttab'];
            $leftCond = $leftTabs['leftcond'];
            $leftWhere = $leftTabs['leftwhere'];
            if (sizeof($leftTab)>0 && sizeof($leftCond)>0 && sizeof($leftTab)==sizeof($leftCond)) {
                for ($i=0;$i<sizeof($leftTab);$i++) {
                    if ($i!=0) {
                        $sql.=" ";
                    }
                    $sql .= " LEFT JOIN ".$leftTab[$i]." on(".$leftCond[$i].") ";
                }
            }
        }
        $sql .= ' where '.$this->_id.'=\''.tablon_FLD::cleanMysqlValue($id).'\'';
        if (($deleteField = $this->getDeletedFLD())!==false) $sql .= ' and '.$this->_tab.'.'.$deleteField.'=\'0\' ';
        if (isset($leftWhere) && is_array($leftWhere) && sizeof($leftWhere)>0) {
            $sql .= ' and '.implode(" and ", $leftWhere)." ";
        }
        //echo $sql."<br/>";
        $con = new con($sql);
        /* TODO - controlar SQL error*/

        if ($con->getNumRows()!=1) return false;
        $r = $con->getResult();
        $this->fields[$idFLD]->setValue($r[$this->fields[$idFLD]->getSQLFLD()]);
        return true;
    }

    public function doClean($c, $v, $id=false, $idc=false, $cleanUniqueFlds=false)
    {
        $cont=0;
        $v = i::clean($v);
        while ($this->existe($c, $v, $id, $idc, $cleanUniqueFlds)) {
            if ($cleanUniqueFlds!==false && is_array($cleanUniqueFlds)) {
                $slcFlds = " case ";
                $aCondFlds = array();
                for ($i=0;$i<count($cleanUniqueFlds);$i++) {
                    $slcFlds .= " when ".$cleanUniqueFlds[$i]." = '".$v."' then ".$cleanUniqueFlds[$i];
                    $aCondFlds[] = $cleanUniqueFlds[$i]." = '".$v."'";
                }
                $slcFlds .= " end";
                //$sql = "select ".$c." as url from ".$this->_tab." where ".$c." = '".$v."' ";
                $sql = "select ".$slcFlds." as url from ".$this->_tab." where ".implode(' or ', $aCondFlds)." ";
            } else {
                $sql = "select ".$c." as url from ".$this->_tab." where ".$c." = '".$v."' ";
            }
            $con = new con($sql);
            if ($con->error()) return false;
            if ($con->getNumRows()>0) {
                $r=$con->getResult();
                $a = explode('_', $r['url']);
                $z = $a[(sizeof($a)-1)];
                if (is_numeric($z)) {
                    $x = "_".$z;
                    $xx ="_".$cont;
                    $v = str_replace($x, $xx, $v);
                } else {
                    $v = $v."_".($cont);
                }
                $cont++;
            } else {
                return $v;
            }
        }
        return $v;
    }


    private function existe($c, $v, $id=false, $idc=false, $cleanUniqueFlds=false)
    {
        if ($cleanUniqueFlds!==false && is_array($cleanUniqueFlds)) {
            $cond = array();
            $cond2 = array();
            for ($i=0;$i<count($cleanUniqueFlds);$i++) {
                $cond[] = $cleanUniqueFlds[$i]."='".$v."'";
                if ( $id!==false && $idc!==false && $cleanUniqueFlds[$i]!=$c) {
                    $cond2[] = $cleanUniqueFlds[$i]."='".$v."'";
                }
            }
            if ( $id!==false && $idc!==false ) {
                $sql = "select ".$c.",".$id." from ".$this->_tab." where
                ((".implode(" or ", $cond).") and ".$idc." != '".$id."') or
                ((".implode(" or ", $cond2).") and ".$idc." = '".$id."')";
            } else {
                $sql = "select ".$c." from ".$this->_tab." where (".implode(" or ", $cond).") ";
            }
        } else {
            $sql = "select ".$c." from ".$this->_tab." where ".$c." = '".$v."' ";
            if ( $id!==false && $idc!==false ) $sql.= " and ".$idc." != '".$id."'";
        }
        $con = new con($sql);
        return ($con->getNumRows()!==0);
    }



    public function newRow($fields, $conds, $realValues = NULL)
    {
        $aFields = $aFieldsT = array();
        $aValues = $aValuesT = array();
        $aIdxEsp = array();
        $contReqs = 0;
        $contActualReqs = 0;
        $ejecutarTrigger = array();
        $after = array();
        $aErrores = array();
        if (isset($this->_uniqueIndexMulti) && $this->_uniqueIndexMulti!==false && is_array($this->_uniqueIndexMulti)) {
            $aliasColumns = array();
                        $arrayKey = false;
            $response = new stdClass();
            $response->aliasColumns = $aliasColumns;
            $response->arrayKey = $arrayKey;
            $esUnico = $this->uniqueCheck($fields, $response);
            $aliasColumns = $response->aliasColumns;
            $arrayKey = $response->arrayKey;
  //                 print_r($aliasColumns); <- Y esto?
            if ($esUnico === false) {
                if ($this->_uniqueErrorMsj === false) {
                    $uniqueAliasStr = implode(", ", $aliasColumns);
                } else {
                    $uniqueAliasStr = $this->_uniqueErrorMsj[$arrayKey];
                }
                return array("1062","Duplicated entry for uninque index declared on columns: '".$uniqueAliasStr."'");
            }
        }
        if ($this->getTrigger()!==false) {
            if (in_array('before_insert', $this->_triggerOn)) {
                $_datosForTrigger = array(
                    "dsdPlt" => $this,
                    "fields" => $fields,
                    "pltConds" => (isset($this->_conds)?$this->_conds:false),
                    "conds" => $conds,
                    "realValues" => $realValues
                );
                $accionEjec = 'before_insert';
                $cad = $this->runTriggerNew("", $accionEjec, $_datosForTrigger);
                if (is_array($cad)) {
                    if (is_string($cad[0]) && $cad[0]=="DoNoInsertBecauseTriggerSays") {
                        return $cad[1];
                    }
                    return $cad;
                }

            }
        }
        $pajar = array('TAGS');
        for ($i=0; $i < $this->_sizeOfFields; $i++) {
            if ($this->fields[$i]->isRequired()) $contReqs++;
            if ((isset($fields[$this->fields[$i]->getSQLFLD()])) && (!empty($fields[$this->fields[$i]->getSQLFLD()])) && ($this->fields[$i]->getSQLType()!==false)) {
                if ($this->fields[$i]->isRequired()) {
                    $contActualReqs++;
                }
                if ($fields[$this->fields[$i]->getSQLFLD()] == "'notoinsertbecausedisabled'") {
                    continue;
                }
                if (in_array($this->fields[$i]->getRealType(), $pajar) || $this->fields[$i]->ifFieldOfAnotherTab() === true) {
                    /*
                     * Si el campo tiene el atributo "fromAnotherTab" a true,
                     * quiere decir que el valor viene de otra tabla (un left join, por ejemplo),
                     * y como no va a seguir en la ejecución de la función,
                     * lo añadimos a la condición para lanzar el trigger
                     */
                    if (in_array('before_insert', $this->fields[$i]->getTriggerOn())) {
                        $_datosForTrigger = array(
                            "dsdPlt" => $this,
                            "fields" => $fields,
                            "pltConds" => (isset($this->_conds)?$this->_conds:false),
                            "conds" => $conds,
                            "realValues" => $realValues,
                            "idFLD" => $i
                        );
                        $accionEjec = 'before_insert';
                        $cad = $this->fields[$i]->runTriggerNew("", $accionEjec, $_datosForTrigger);
                        if (is_array($cad)) {
                            if (is_string($cad[0]) && $cad[0]=="DoNoInsertBecauseTriggerSays") {
                                //return $cad[1];
                                continue;
                            }
                            return $cad;
                        }
                    }
                }
                /*
                 * Si tiene el atributo "fromAnotherTab" a true,
                 * continuar en el bucle ya que no es un campo de esta tabla.
                 */
                if ($this->fields[$i]->ifFieldOfAnotherTab() === true) {
                    continue;
                }

                //Como solo interesa el "noInsertBecause*" le pasamos un valor nulo
                $valMysql = $this->fields[$i]->getMysqlValue(NULL);

                if (($clean = $this->fields[$i]->toClean()) && ($valMysql != "noInsertBecauseFileEsp")) {
                    $aFields[] = $clean;
                    $aClean = $this->fields[$i]->toCleanUnique();
                    if ($aClean!==false && is_array($aClean)) {
                        if (!isset($aTmpClean) || !is_array($aTmpClean)) {
                            $aTmpClean = array();
                        }
                        $cleanValue = $this->doClean($clean, $fields[$this->fields[$i]->getSQLFLD()], false, false, $aClean);
                        $tmpCont = 0;
                        while (in_array($cleanValue, $aTmpClean)) {

                            if ($tmpCont>50) die();
                            $a = explode('_', $cleanValue);
                            $z = $a[(sizeof($a)-1)];
                            if (is_numeric($z)) {
                                $x = "_".$z;
                                $xx ="_".$tmpCont;
                                $cleanValue = str_replace($x, $xx, $cleanValue);
                                $tmpCont++;
                            } else {
                                $cleanValue = $cleanValue."_".($tmpCont);
                            }
                        }
                        $aTmpClean[] = $cleanValue;
                    } else {
                        $cleanValue = $this->doClean($clean, $fields[$this->fields[$i]->getSQLFLD()]);
                    }
                    if ($cleanValue === false) {
                        return array("99","Error with clean field [$clean => ".$this->fields[$i]->getSQLFLD()."]");
                    }
                    if (empty($cleanValue) || $cleanValue == "''" || $cleanValue == '') {
                        $cleanValue = $this->fields[$i]->valueOnEmpty($cleanValue);
                    }
                    $aValues[] = "'".$cleanValue."'";

                }

                //$valMysql = $this->fields[$i]->getMysqlValue($fields[$this->fields[$i]->getSQLFLD()]);

                if ($valMysql!="noInsertBecauseFile" && $valMysql!="noInsertBecauseFileEsp") {
                        $aFields[] = $this->fields[$i]->getSQLFLD(1);
                        if (is_array($fields[$this->fields[$i]->getSQLFLD()])) {
                            // El valor devuelto es un array... eso es error, fuera
                            return $fields[$this->fields[$i]->getSQLFLD()];
                        }
                        if (!(i::detectUTF8($fields[$this->fields[$i]->getSQLFLD()]))) {
                            $fields[$this->fields[$i]->getSQLFLD()] = utf8_encode($fields[$this->fields[$i]->getSQLFLD()]);
                        }
                        if (empty($fields[$this->fields[$i]->getSQLFLD()]) || $fields[$this->fields[$i]->getSQLFLD()] == "''" || $fields[$this->fields[$i]->getSQLFLD()] == '') {
                            $fields[$this->fields[$i]->getSQLFLD()] = $this->fields[$i]->valueOnEmpty($fields[$this->fields[$i]->getSQLFLD()]);
                        }
                        $aValues[] = $fields[$this->fields[$i]->getSQLFLD()];
                }
                if ($valMysql == "noInsertBecauseFileEsp") {
                    $aIdxEsp[] = $i;
                }
                if (method_exists($this->fields[$i], "afterupdate")) {
                    $after[] = $i;
                }

                for ($j=0;$j<$this->fields[$i]->sizeofsubFields;$j++) {

                    if ((isset($fields[$this->fields[$i]->subFields[$j]->getSQLFLD()])) && (!empty($fields[$this->fields[$i]->subFields[$j]->getSQLFLD()]))) {
                        $aFields[] = $this->fields[$i]->subFields[$j]->getSQLFLD();
                        if (empty($fields[$this->fields[$i]->getSQLFLD()]) || $fields[$this->fields[$i]->getSQLFLD()] == "''" || $fields[$this->fields[$i]->getSQLFLD()] == '') {
                            $fields[$this->fields[$i]->getSQLFLD()] = $this->fields[$i]->valueOnEmpty($fields[$this->fields[$i]->getSQLFLD()]);
                        }
                        $aValues[] = $fields[$this->fields[$i]->subFields[$j]->getSQLFLD()];

                    }

                }
                if ($this->fields[$i]->getTrigger()!==false && $valMysql!="noInsertBecauseFileEsp") {
                    $ejecutarTrigger[] = $i;
                }
            }
        }

        for ($i=0;$i<sizeof($ejecutarTrigger);$i++) {
            $idxFld = $ejecutarTrigger[$i];
            if (in_array('before_insert', $this->fields[$idxFld]->getTriggerOn())) {
                $_datosForTrigger = array(
                    "dsdPlt" => $this,
                    "fields" => $fields,
                    "pltConds" => (isset($this->_conds)?$this->_conds:false),
                    "conds" => $conds,
                    "realValues" => $realValues,
                    "idFLD" => $i,
                    "aValues" => $aValues
                );
                $accionEjec = 'before_insert';
                $cad = $this->fields[$idxFld]->runTriggerNew("", $accionEjec, $_datosForTrigger);
                if (is_array($cad)) {
                    if (is_string($cad[0]) && $cad[0]=="DoNoInsertBecauseTriggerSays") {
                        //return $cad[1];
                        continue;
                    }
                    return $cad;
                }
            }
        }





        if (isset($this->_conds)) {

            foreach ($this->_conds as $co) {
                list($c, $v) = explode('=', $co, 2);
                $aFields[] = $c;
                $aValues[] = $v;
            }
        }


        foreach ($conds as $cond => $val) {
            if(empty($cond)) continue;
            if (preg_match("/^triggerCond_(.*)/", $cond, $forTrigger)) {
                $aFieldsT[] = $cond;
                $aValuesT[] = $val;
            } else {
                $aFields[] = $cond;
                $aValues[] = $val;
            }
        }


        if ($this->_onInsert) {
            $oninsert = explode('|', $this->_onInsert);
            if (is_array($oninsert)&&sizeof($oninsert)>0) {
                foreach ($oninsert as $oninsertfield) {
                    list($f, $v) = explode('=', $oninsertfield);
                    $aFields[] = $f;
                    $aValues[] = $v;
                }
            }
        }

        $sql = 'insert into '.$this->_tab.'('.implode(',', $aFields);
        $sql .= ') values ('.implode(',', $aValues).');';



        $con = new con($sql);

        if (sizeof($aErrores)>0) {
            return $aErrores;
        }

        /* TODO - controlar SQL error*/
        if ($con->error()) {
            return array($con->getErrorNumber(), $con->getError());
        }
        $idDev = $con->getId();
        if (is_array($aIdxEsp) && !empty($aIdxEsp)) {
            foreach ($aIdxEsp as $idx) {
                $retAfCM = $this->fields[$idx]->insertAfterCreateMain($idDev, $fields[$this->fields[$idx]->getSQLFLD()]);
                if (is_array($retAfCM) && $retAfCM[0] == "noInsertBecauseFileEsp" && $retAfCM[1] == "error") {
                    return  array($retAfCM[2], $retAfCM[3]);
                }
            }
        }
        if ($this->getTrigger($idDev)!==false) {
            if (isset($this->_triggerParamsEsp) && $this->_triggerParamsEsp !== false) {
                if (in_array('insert', $this->_triggerOn)) {
                    $_datosForTrigger = array(
                        "dsdPlt" => $this,
                        "fields" => $fields,
                        "pltConds" => (isset($this->_conds)?$this->_conds:false),
                        "conds" => $conds,
                        "realValues" => $realValues
                    );
                    $accionEjec = 'insert';
                    $cad = $this->runTriggerNew($idDev, $accionEjec, $_datosForTrigger);
                    if (is_array($cad)) {
                        if (is_string($cad[0]) && $cad[0]=="DoNoInsertBecauseTriggerSays") {
                            return $cad[1];
                        }
                        return $cad;
                    }
                }
            } else {
                if (in_array('insert', $this->_triggerOn)) {
                    $accionEjec = 'insert';
                    $funcionTrigger = $this->getTrigger($idDev, $accionEjec).';';
                    eval("\$cad = $funcionTrigger");
                    if (is_array($cad)) {
                        return $cad;
                    }
                }
            }
        }

        for ($i=0;$i<sizeof($ejecutarTrigger);$i++) {
            $idxFld = $ejecutarTrigger[$i];
            if (in_array('insert', $this->fields[$idxFld]->getTriggerOn())) {
                $accionEjec = 'insert';
                if (isset($this->fields[$idxFld]->triggerParamsEsp) && $this->fields[$idxFld]->triggerParamsEsp !== false) {
                    $_datosForTrigger = array(
                        "dsdPlt" => $this,
                        "fields" => $fields,
                        "pltConds" => (isset($this->_conds)?$this->_conds:false),
                        "conds" => $conds,
                        "realValues" => $realValues,
                        "idFLD" => $i
                    );
                    $cad = $this->fields[$idxFld]->runTriggerNew($idDev, $accionEjec, $_datosForTrigger);
                    if (is_array($cad) && $cad[0]=="DoNoInsertBecauseTriggerSays") {
                        $idDev = $cad[1];
                    } elseif (is_array($cad)) {
                        return $cad;
                    }
                } else {
                    $funcionTrigger = $this->fields[$idxFld]->getTrigger($idDev, $accionEjec).';';
                    eval("\$cad = $funcionTrigger");
                    if (is_array($cad)) {
                        return $cad;
                    }
                }
            }
        }

        if (sizeof($after)>0) {
            foreach ($after as $idFLD) {
                switch($this->fields[$idFLD]->getConstantTypeAjaxUpload()) {
                    case "_GET":
                        $this->fields[$idFLD]->afterupdate($_GET['FLD__'.$this->fields[$idFLD]->getSQLFLD(1)], $idDev, $this->_tab);
                        break;
                    case "_FILES":
                        $this->fields[$idFLD]->afterupdate($_FILES['FLD__'.$this->fields[$idFLD]->getSQLFLD(1)], $idDev, $this->_tab);
                        break;
                }
            }
        }
        return $idDev;
    }

    public function ifFieldRefres($idFLD, $id, $accion)
    {
        if ($this->fields[$idFLD]->getTrigger($id)!==false) {
            $aRefresh = $this->fields[$idFLD]->getTriggerRefresh();
            if ($aRefresh !== false) {
                if (in_array($accion, $aRefresh)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function ifRefreshwhen($accion)
    {
        if (is_array($this->_refreshWhen) && !empty($this->_refreshWhen)) {
            if (in_array($accion, $this->_refreshWhen)) {
                return true;
            }
        }
        return false;
    }

    public function getAllFieldsWithCondSQL($selcond, $conds=false)
    {
        $aConds = array();
        if ($conds)
            $aConds = explode("|", $conds);
        $aConds[] = $selcond;
        $sql = "select * from ".$this->getTab()." where ".implode(' and ', $aConds);
        if (($deleteField = $this->getDeletedFLD())!==false) $sql .= ' and '.$this->_tab.'.'.$deleteField.'=\'0\' ';
        return $sql;
    }

    /*
     * Comprueba que el conjunto de columnas declaradas como índice único,
     * cumplen la condición dependiendo del valor de $nullUse (manejo de valores null):
     * - nullUse = "nulls_equal": Los valores null se consideran iguales (null==null ->true). Por ejemplo
     *                                 - Tenemos un unique de las columnas col1 y col2
     *                                 - En la tabla de BBDD tenemos las siguientes filas de valores para dichas columnas:
     *                                     col1    col2
     *                                     ----    ----
     *                                      2         5
     *                                      2         6
     *                                      3         null
     *                                      null     null
     *                                 - Al intentar insertar (2,5), (2, 6), (3, null) o (null, null) daría error.
     *                                 - No daría error: cualquier otra inserción.
     * - nullUse = "nulls_different": Los valores null se consideran diferente (null==null ->false). Con el ejemplo anterior:
     *                                 - Al intentar insertar (2, 5) o (2,6) daría error.
     *                                 - Al insertar (3, null) o (null, null) no daría error ya que los valores null se consideran diferentes entre sí.
     *                                 - Al insertar cualquier otro valor diferente de las anteriores tampoco daría error.
     */

    /**
     *
     * @param <type> $fields
     * @param stdClass $pResponse (encapsula datos que antes iban por referencia)
     * @return <boolean>
     */
    public function uniqueCheck($fields, stdClass $pResponse)
    {
        for ($arrayKey = 0; $arrayKey < count($this->_uniqueIndexMulti); $arrayKey++) {
            $pResponse->arrayKey = $arrayKey;
            $selectFields = implode(", ", $this->_uniqueIndexMulti[$arrayKey]);
            $hayNulos = false;
            $aSqlConds = array();
            $pResponse->aliasColumns = array();
            for ($i=0; $i < count($this->_uniqueIndexMulti[$arrayKey]); $i++) {
                $indAct = $this->_uniqueIndexMulti[$arrayKey][$i];
                $pResponse->aliasColumns[] = $this->aFields[$indAct]['alias'];
                if (!isset($fields[$indAct]) || empty($fields[$indAct])) {
                    $aSqlConds[] = $indAct . " is NULL";
                    $hayNulos = true;
                } else {
                    $aSqlConds[] = $indAct . " = " . $fields[$indAct]."";
                }
            }
            $sqlCond = implode(" and ", $aSqlConds);
            $sql = "select ".$selectFields." from ".$this->_tab." where ".$sqlCond;
            if (($deleteField = $this->getDeletedFLD())!==false) {
                $sqlCond .= ' and '.$this->_tab.'.'.$deleteField.'=\'0\' ';
            }
            $con = new con($sql);
            if ($con->getNumRows()>0) {
                if ($this->_uniqueNullUse !== false && is_array($this->_uniqueNullUse) && isset($this->_uniqueNullUse[$arrayKey]) && $this->_uniqueNullUse[$arrayKey]=="nulls_equal") return false;
                elseif ($hayNulos !== true) return false;
            }
        }
        return true;
    }

    /*
     * Como la función anterior pero preparada para updateSingleField
     */

    /**
     *
     * @param <type> $datosFLD
     * @param <type> $_idRow
     * @param stdClass $pResponse (encapsula datos que antes iban por referencia)
     * @return <boolean>
     */
    public function uniqueCheckUpdate($datosFLD, $_idRow, stdClass $pResponse)
    {
        $numUniqueIndexMulti = sizeof(count($this->_uniqueIndexMulti));
        for ($arrayKey = 0; $arrayKey < $numUniqueIndexMulti; $arrayKey++) {
            if (!in_array($datosFLD["indexFld"], $this->_uniqueIndexMulti[$arrayKey])) {
                continue;
            }
            $pResponse->arrayKey = $arrayKey;
            $selectFields = implode(", ", $this->_uniqueIndexMulti[$arrayKey]);
            $sql = 'SELECT ' . $selectFields . ' FROM  ' . $this->_tab
                 . ' WHERE ' . $this->_id . " = '" . tablon_FLD::cleanMySQLValue($_idRow) . "'";
            if (($deleteField = $this->getDeletedFLD())!==false) {
                $sqlCond .= ' AND ' . $this->_tab . '.' . $deleteField . "= '0'";
            }
            $con = new con($sql);

            $datosAnt = $con->getResult();
            $datosAnt[$datosFLD["indexFld"]] = $datosFLD["valorFLD"];

            $aSqlConds = array();
            $hayNulos = false;
            $pResponse->aliasColumns = array();
            foreach ($datosAnt as $idx=>$vlr) {
                $pResponse->aliasColumns[] = $this->aFields[$this->_uniqueIndexMulti[$arrayKey][$idx]]['alias'];
                if (!isset($vlr) || empty($vlr)) {
                    $aSqlConds[] = $idx . " IS NULL";
                    $hayNulos = true;
                } else {
                    $aSqlConds[] = $idx . " = " . $vlr . "";
                }
            }

            $sqlCond = implode(" AND ", $aSqlConds);
            $sql = "SELECT " . $selectFields . " FROM " . $this->_tab . " WHERE " . $sqlCond;
            if (($deleteField = $this->getDeletedFLD())!==false) {
                $sqlCond .= ' AND ' . $this->_tab . '.' . $deleteField . " = '0'";
            }

            $con = new con($sql);
            if ($con->getNumRows()>0) {
                if ($this->_uniqueNullUse[$arrayKey] == "nulls_equal") {
                    return false;
                } elseif ($hayNulos !== true) {
                    return false;
                }
            }
        }
        return true;
    }

    public function updateRow($fields, $conds, $realValues = NULL)
    {

        $aFields = array();
        $aValues = array();
        $aIdxEsp = array();
        $contReqs = 0;
        $contActualReqs = 0;
        $ejecutarTrigger = array();
        $after = array();
        $aErrores = array();
        if (isset($this->_uniqueIndexMulti) && $this->_uniqueIndexMulti!==false && is_array($this->_uniqueIndexMulti)) {
            $aliasColumns = array();
                        $arrayKey = false;
            $response = new stdClass();
            $response->aliasColumns = $aliasColumns;
            $response->arrayKey = $arrayKey;
            $esUnico = $this->uniqueCheck($fields, $response);
            $aliasColumns = $response->aliasColumns;
            $arrayKey = $response->arrayKey;
  //                 print_r($aliasColumns); <- Y esto?
            if ($esUnico === false) {
                if ($this->_uniqueErrorMsj === false) {
                    $uniqueAliasStr = implode(", ", $aliasColumns);
                } else {
                    $uniqueAliasStr = $this->_uniqueErrorMsj[$arrayKey];
                }
                return array("1062","Duplicated entry for uninque index declared on columns: '".$uniqueAliasStr."'");
            }
        }
        if ($this->getTrigger()!==false) {
            if (in_array('before_insert', $this->_triggerOn)) {
                $_datosForTrigger = array(
                    "dsdPlt" => $this,
                    "fields" => $fields,
                    "pltConds" => (isset($this->_conds)?$this->_conds:false),
                    "conds" => $conds,
                    "realValues" => $realValues
                );
                $accionEjec = 'before_insert';
                $cad = $this->runTriggerNew("", $accionEjec, $_datosForTrigger);
                if (is_array($cad)) {
                    if (is_string($cad[0]) && $cad[0]=="DoNoInsertBecauseTriggerSays") {
                        return $cad[1];
                    }
                    return $cad;
                }

            }
        }
        $pajar = array('TAGS');
        for ($i=0;$i<$this->_sizeOfFields;$i++) {
            if ($this->fields[$i]->isRequired()) $contReqs++;
            if ((isset($fields[$this->fields[$i]->getSQLFLD()]))
               && (!empty($fields[$this->fields[$i]->getSQLFLD()]))
               && ($this->fields[$i]->getSQLType()!==false)) {

                if ($this->fields[$i]->isRequired()) {
                    $contActualReqs++;
                }
                if ($fields[$this->fields[$i]->getSQLFLD()] == "'notoinsertbecausedisabled'") {
                    continue;
                }
                if (in_array($this->fields[$i]->getRealType(), $pajar)
                   || $this->fields[$i]->ifFieldOfAnotherTab() === true) {
                    /*
                     * Si el campo tiene el atributo "fromAnotherTab" a true,
                     * quiere decir que el valor viene de otra tabla (un left join, por ejemplo),
                     * y como no va a seguir en la ejecución de la función,
                     * lo añadimos a la condición para lanzar el trigger
                     */
                    if (in_array('before_insert', $this->fields[$i]->getTriggerOn())) {
                        $_datosForTrigger = array(
                            "dsdPlt" => $this,
                            "fields" => $fields,
                            "pltConds" => (isset($this->_conds)?$this->_conds:false),
                            "conds" => $conds,
                            "realValues" => $realValues,
                            "idFLD" => $i
                        );
                        $accionEjec = 'before_insert';
                        $cad = $this->fields[$i]->runTriggerNew("", $accionEjec, $_datosForTrigger);
                        if (is_array($cad)) {
                            if (is_string($cad[0]) && $cad[0]=="DoNoInsertBecauseTriggerSays") {
                                //return $cad[1];
                                continue;
                            }
                            return $cad;
                        }
                    }
                }
                /*
                 * Si tiene el atributo "fromAnotherTab" a true,
                 * continuar en el bucle ya que no es un campo de esta tabla.
                 */
                if ($this->fields[$i]->ifFieldOfAnotherTab() === true) continue;

                //Como solo interesa el "noInsertBecause*" le pasamos un valor nulo
                $valMysql = $this->fields[$i]->getMysqlValue(NULL);

                if (($clean = $this->fields[$i]->toClean()) && ($valMysql != "noInsertBecauseFileEsp")) {
                    $aFields[] = $clean;
                    $aClean = $this->fields[$i]->toCleanUnique();
                    if ($aClean!==false && is_array($aClean)) {
                        if (!isset($aTmpClean) || !is_array($aTmpClean)) {
                            $aTmpClean = array();
                        }
                        $cleanValue = $this->doClean(
                            $clean,
                            $fields[$this->fields[$i]->getSQLFLD()],
                            false,
                            false,
                            $aClean
                        );
                        $tmpCont = 0;
                        while (in_array($cleanValue, $aTmpClean)) {

                            if ($tmpCont>50) die();
                            $a = explode('_', $cleanValue);
                            $z = $a[(sizeof($a)-1)];
                            if (is_numeric($z)) {
                                $x = "_".$z;
                                $xx ="_".$tmpCont;
                                $cleanValue = str_replace($x, $xx, $cleanValue);
                                $tmpCont++;
                            } else {
                                $cleanValue = $cleanValue."_".($tmpCont);
                            }
                        }
                        $aTmpClean[] = $cleanValue;
                    } else {
                        $cleanValue = $this->doClean($clean, $fields[$this->fields[$i]->getSQLFLD()]);
                    }
                    if ($cleanValue === false) {
                        return array("99","Error with clean field [$clean => ".$this->fields[$i]->getSQLFLD()."]");
                    }
                    if (empty($cleanValue) || $cleanValue == "''" || $cleanValue == '') {
                        $cleanValue = $this->fields[$i]->valueOnEmpty($cleanValue);
                    }
                    $aValues[] = "'".$cleanValue."'";

                }

                //$valMysql = $this->fields[$i]->getMysqlValue($fields[$this->fields[$i]->getSQLFLD()]);

                if ($valMysql!="noInsertBecauseFile" && $valMysql!="noInsertBecauseFileEsp") {
                        $aFields[] = $this->fields[$i]->getSQLFLD(1);
                        if (is_array($fields[$this->fields[$i]->getSQLFLD()])) {
                            // El valor devuelto es un array... eso es error, fuera
                            return $fields[$this->fields[$i]->getSQLFLD()];
                        }
                        if (!(i::detectUTF8($fields[$this->fields[$i]->getSQLFLD()]))) {
                            $fields[$this->fields[$i]->getSQLFLD()] = utf8_encode($fields[$this->fields[$i]->getSQLFLD()]);
                        }
                        if (empty($fields[$this->fields[$i]->getSQLFLD()])
                           || $fields[$this->fields[$i]->getSQLFLD()] == "''"
                           || $fields[$this->fields[$i]->getSQLFLD()] == '') {
                            $fields[$this->fields[$i]->getSQLFLD()] = $this->fields[$i]->valueOnEmpty($fields[$this->fields[$i]->getSQLFLD()]);
                        }
                        $aValues[] = $fields[$this->fields[$i]->getSQLFLD()];
                }
                if ($valMysql=="noInsertBecauseFileEsp") {
                    $aIdxEsp[] = $i;
                }
                if (method_exists($this->fields[$i], "afterupdate")) {
                    $after[] = $i;
                }

                for ($j=0;$j<$this->fields[$i]->sizeofsubFields;$j++) {

                    if ((isset($fields[$this->fields[$i]->subFields[$j]->getSQLFLD()])) && (!empty($fields[$this->fields[$i]->subFields[$j]->getSQLFLD()]))) {
                        $aFields[] = $this->fields[$i]->subFields[$j]->getSQLFLD();
                        if (empty($fields[$this->fields[$i]->getSQLFLD()]) || $fields[$this->fields[$i]->getSQLFLD()] == "''" || $fields[$this->fields[$i]->getSQLFLD()] == '') {
                            $fields[$this->fields[$i]->getSQLFLD()] = $this->fields[$i]->valueOnEmpty($fields[$this->fields[$i]->getSQLFLD()]);
                        }
                        $aValues[] = $fields[$this->fields[$i]->subFields[$j]->getSQLFLD()];

                    }

                }
                if ($this->fields[$i]->getTrigger()!==false && $valMysql!="noInsertBecauseFileEsp") {
                    $ejecutarTrigger[] = $i;
                }
            }
        }

        $aSQL = array();

        foreach ($aFields as $k=>$field) {
            $aSQL[] = " {$field} = {$aValues[$k]} ";
        }

        $sql = 'update '.$this->_tab.' set '.implode(',', $aSQL)." ".$conds ;

        $con = new con($sql);
        if (sizeof($aErrores)>0) {
            return $aErrores;
        }

        /* TODO - controlar SQL error*/
        if ($con->error()) {
            return array($con->getErrorNumber(), $con->getError());
        }
        $idDev = $con->getId();

        return $idDev;
    }


}
