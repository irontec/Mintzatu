<?php
/**
 * Fichero principal de la clase krm_menu,
 * encargada de ibujar y gestionar el menu.
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class krm_menu
{
    protected $_menu;
    protected static $_instance = null;
    protected $_karmaRegistry;
    protected $_selectedSection = null;
    protected $_selectedSubsection = null;

    public $submenu = array();
    public $links = array();

    public $selectedConf=false;
    public $selectedURL = "";
    public $l;
    public $aPrincJs = array();
    public $aPrincCss = array();
    public $externalModules = array();
    public $appendCss;

    function __construct($autoLoad = true)
    {

        $this->_karmaRegistry = KarmaRegistry::getInstance();
        $filePath = dirname(__FILE__)."/../../configuracion/karma.cfg";
        $this->_menu = parse_ini_file($filePath, true);


        $this->l = new k_literal();
        $this->_initLang();


        $this->_karmaRegistry->set('translator', new translator());


        if (!is_writeable(dirname(__FILE__)."/../../cache")) {
            iError::warn(
                "El directorio cache no tienes permisos de escritura.<br /> "
                ." Puede que algunas funcionalidades no estén habilitadas."
            );
        }

        /* Si alguien ve esto alguna vez, y sabe para que sirve,
         * porfis comentarlo */
        if (isset($_GET['undev'])) {
            unset($_SESSION['dev']);
        }
        if (isset($_GET['dev'])||isset($_SESSION['dev'])) {
            $_SESSION['dev'] = true;
            $this->_menu['Karma'] = array('desc'=>'dev');
            $this->_menu['Karma::.plt Creator'] = array('desc'=>'dev','file'=>'../karma/dev/plt.cfg');
        }
        $this->_menu['&nbsp;'] = true;
        if ($autoLoad) {
            $this->parseMenu(false);
        }
    }

    public function __get($varName)
    {
        switch ($varName) {
            case 'menu':
                iError::ok(
                    "La obtención del parametro '" . $varName . "' de "
                    . "krm_menu está deprecated usar getMenu o getMenuSection "
                    . "en su defecto"
                );
                return $this->$varName;
                break;
        }
    }

    public function getMenu()
    {
        return $this->_menu;
    }

    public function getMenuSection($section, $subsection = null)
    {
        if (!is_null($subsection)) {
            return isset($this->_menu[$section][$subsection])? $this->_menu[$section][$subsection] : false;
        }
        return $this->_menu[$section];
    }

    public function menuSectionExists($section, $subsection = null)
    {
        if (!is_null($subsection)) {
            return isset($this->_menu[$section][$subsection]);
        }
        return isset($this->_menu[$section]);
    }

    public function parseMenu($oLogin, $url = null)
    {

        $selectedOption = null;
        if (!is_null($url)) {
            if (isset($url['op'])) {
                $selectedOption = $url['op'];
            }
        } else {
            if (isset($_GET['op'])) {
                $selectedOption = $_GET['op'];
            }
        }

        if (($oLogin === false) || ($oLogin->isAdminLogged($this->getadminID()))) {
            $ologinCheck = false;
        } else {
            $ologinCheck = true;
        }

        foreach ($this->_menu as $menuOption => $menuContent) {
            if ($menuOption == "main") continue;

            if (isset($menuContent['autoClass'])) {
                $nProp = $menuContent['autoClass'];
                $this->externalModules[$nProp] = new $nProp($this);
                $this->_menu[$menuOption]['nodraw'] = true;
                continue;
            }

            foreach ($this->externalModules as $n => $foo) {
                if (strpos($n, $menuOption) !== false) {
                    $this->_menu[$menuOption]['nodraw'] = true;
                    break;
                }
            }

            if (isset($this->_menu[$menuOption]['nodraw'])) continue;


            /* Fin de opciones para hdkarma */

            if (preg_match('/(.*)::(.*)/', $menuOption, $matches)) {
                list($menuOption, $section, $subsection) = $matches;

                if ($ologinCheck) {
                    if (!isset($oLogin->aMenu[$this->_menu[$menuOption]['file']])) {
                        $this->_menu[$menuOption]['nodraw'] = true;
                        continue;
                    }
                }

                if ( (!isset($this->submenu[$section])) || (!is_array($this->submenu[$section]))) {
                    $this->submenu[$section] = array();
                }

                $link = i::clean($subsection);
                $contador = 1;
                while (isset($this->links[$link])) {
                    $link = i::clean($subsection) . $contador;
                    $contador++;
                }
                $this->links[$link] = true;


                if (!is_null($selectedOption)) {
                    $currentOp = $selectedOption;
                } else {
                    $currentOp = false;
                }

                $tmpex = explode("::", $currentOp, 2);
                if (sizeof($tmpex) == 2) {
                    $currentOp = $tmpex[0];
                }

                if ($link === $currentOp) {
                    $this->_selectedSection = $section;
                    $this->_selectedSubsection = $subsection;
                    $this->selectedURL = $link;
                }

                if (isset($menuContent['gotoop']) && $menuContent['gotoop']) {
                    $link .= (empty($currentOp))? $menuContent['gotoop'] : "&amp;" . $menuContent['gotoop'];
                }

                $this->submenu[$section][$subsection] = $menuContent;
                $this->submenu[$section][$subsection]['__uniqueLink'] = $link;

                unset($this->_menu[$menuOption]);
            }
        }

        foreach ($this->externalModules as $mod) {
            if (method_exists($mod, "updateMenuPermission")) {
                $mod->updateMenuPermission($oLogin->aMenu);

            }
        }

        $this->loadSelected();

        return $this;
    }

    public function parseMenuAjax($oLogin, $_url)
    {
        return $this->parseMenu($oLogin, $_url);
    }

    public function getYear()
    {
        return $this->_menu['main']['año'];
    }

    public function isloadJs()
    {
        return isset($this->_menu['main']['loadJs']);
    }

    /**
     * Inicializa el idioma del usuario, si no está seteado, lo setea
     * Preferencia:
     *  - $_SESSION['user_lang']
     *  - Sección 'main'->'lang' del Menu
     *  - 'es'
     */
    protected function _initLang()
    {
        if (!$this->_karmaRegistry->isDefined('lang')) {
            if (isset($_SESSION['user_lang'])) {
                $this->setLang($_SESSION['user_lang']);
            } elseif ($this->menuSectionExists('main', 'lang')) {
                $this->setLang($this->getMenuSection('main', 'lang'));
            } else {
                $this->setLang('es');
            }
        } else {
            $this->setLang($this->_karmaRegistry->get("lang"));
        }
    }

    public function getLang()
    {
        return $this->_karmaRegistry->get('lang');
    }


    /**
     * Setea el idioma tanto en la sesión como en el registro
     * @param $lang
     */
    public function setLang($lang)
    {

        $this->l->setLang($lang);

        $_SESSION['user_lang'] = $lang;
        $this->_karmaRegistry->set('lang', $lang);
    }

    public function getLogo()
    {
        if (!isset($this->_menu['main']['logo'])) return false;
        return './icons/'.$this->_menu['main']['logo'];
    }

    public function getTitle()
    {
        return $this->getMLval($this->_menu['main']['tit'], $this->_menu['main'], 'tit');
    }

    public function getDesc()
    {
        if(isset($this->_menu['main']['desc'])) {
            return $this->getMLval($this->_menu['main']['desc'], $this->_menu['main'], 'desc');
        }
    }

    public function getVersion()
    {
        return $this->_menu['main']['version'];
    }

    public function getGeneralJSLibs()
    {
        $jsLibs = array(
            "jquery-1.6/jquery-1.6.4.min.js",
            "ui-1.8/jquery-ui.min.js",
            "jquery/jq.tooltip.js",
            "jquery/jq.impromptu.js",
            "jquery/jq.cornez.js",
            "jquery/jquery.cookie.pack.js",
            "karma_lang.js",
            "karma.js"
        );
        return $jsLibs;
    }

    public function getLegacyGeneralJSLibs()
    {
        $jsLibs = array(
            "jquery-1.4.2/jquery-1.4.2.min.js",
            "jquery/jquery-ui-karma-personalized.min.js",
            "jquery/jq.easydrag.js",
            "jquery/jq.bgiframe.js",
            "jquery/jq.tooltip.js",
            "jquery/jq.impromptu.js",
            "jquery/jq.cornez.js",
            "jquery/jquery.cookie.pack.js",
            "karma_lang.js",
            "karma.js"
        );
        return $jsLibs;
    }

    public function getJS()
    {
        if (!isset($this->_menu['main']['legacyJS'])) {
            $parsedJs = $this->getGeneralJSLibs();
        } else {
            $generalJSLibs = $this->getLegacyGeneralJSLibs();
            $parsedJs = array();
            if (isset($this->_menu['main']['js'])) {
                $parsedJs = explode(",", $this->_menu['main']['js']);
                if (  isset($parsedJs) &&  is_array($parsedJs) ) {
                    /*
                     * ñapilla para no tener que quitar en todos lo proyectos
                     * el js de karma.cfg
                     */
                    $deprecated = array('jquery/jquery.js');
                    foreach ($parsedJs as $i=>$src) {
                        if (in_array($src, $deprecated)) {
                            unset($parsedJs[$i]);
                        }
                    }
                }
            }
            $parsedJs = array_merge($generalJSLibs, $parsedJs);
        }

        if (isset($this->selectedConf['main']['js'])) {
            $parsedcfgJs = explode(",", $this->selectedConf['main']['js']);
        }
        if (isset($parsedcfgJs) && is_array($parsedcfgJs)) {
            $parsedJs = array_merge($parsedJs, $parsedcfgJs);
        }
        if (is_array($this->aPrincJs)) {
            $parsedJs = array_merge($parsedJs, $this->aPrincJs);
        }
        return $parsedJs;
    }

    public function getCSS()
    {
        $parsedCss = explode(",", $this->_menu['main']['css']);
        if (isset($this->selectedConf['main']['css']) ) {
            $parsedcfgCss = explode(",", $this->selectedConf['main']['css']);
        }
        if ( isset($parsedcfgCss) && is_array($parsedcfgCss) ) {
            $parsedCss = array_merge($parsedCss, $parsedcfgCss);
        }
        if ( is_array($this->aPrincCss) ) {
            $parsedCss = array_merge($parsedCss, $this->aPrincCss);
        }
        return $parsedCss;

    }

    public function appendCss($css)
    {
        if (!is_array($this->appendCss)) {
            $this->appendCss=array();
        }
        $this->appendCss[] = $css;
    }

    public function get_appendCss()
    {
        return $this->appendCss;
    }

    public function getLoginClass()
    {
        if (isset($this->_menu['main']['login'])) {
            return $this->_menu['main']['login'];
        }
        return false;
    }

    public function getLoginType()
    {
        if (isset($this->_menu['main']['loginType'])) {
            return $this->_menu['main']['loginType'];
        }
        return false;
    }

    public function isKarmaBarLogoDisabled()
    {
        if (isset($this->_menu['main']['disableKarmaBarLogo'])) {
            return $this->_menu['main']['disableKarmaBarLogo'];
        }
        return false;
    }

    public function isTinyMECmediaDisabled()
    {
        if (isset($this->_menu['main']['disableTinyMCEmedia'])) {
            return $this->_menu['main']['disableTinyMCEmedia'];
        }
        return false;
    }





    public function getadminID()
    {
        if (isset($this->_menu['main']['adminID'])) {
            return $this->_menu['main']['adminID'];
        }
        return false;
    }

    public function getadminRol()
    {
        if (isset($this->_menu['main']['adminROLID'])) {
            return $this->_menu['main']['adminROLID'];
        }
        return false;
    }

    public function isLoginCfile()
    {
        if ((isset($this->_menu['main']['login']))&&(isset($this->_menu['main']['loginfile']))) {
            return $this->_menu['main']['loginfile'];
        } else {
            return false;
        }

    }
    public function getHdObj()
    {
        return $this->hdKarma;
    }

    public function getMLval($key, $arr, $q="alias")
    {
        return KarmaRegistry::getInstance()->get('translator')->translate($key, $arr, $q);
    }

    public function drawNewMenu($menuId = "")
    {
        echo '<div class="menuopts">
            <span class="change_menu" title="'.$this->l->l('maximizar/minimizar').'"></span>
        </div>';
        echo '<ul id="'.$menuId.'newMenuUl">';
        foreach ($this->_menu as $m=>$cont) {
            if ($m=="&nbsp;") {
                continue;
            }
            if ($m=="main") continue;
            if ( (isset($cont['nodraw'])) && ($cont['nodraw']) ) continue;
            if ( (!isset($this->submenu[$m])) || (sizeof($this->submenu[$m]) == 0) ) continue;

            echo '<li><img src="./icons/more.png" class="more" alt="Más"/>';
            echo '<p class="head"';
            if (isset($cont['desc'])) {
                echo ' title="' . $this->getMLval($cont['desc'], $cont, 'desc') . '"';
            } else {
                echo ' title="'.$this->getMLval($m, $cont).'"';
            }
            echo '>'.$this->getMLval($m, $cont).'</p>';
            if (isset($this->submenu[$m])) {
                echo '<ul>';
                foreach ($this->submenu[$m] as $sm=>$cont) {
                    if (isset($cont['execute']) && $cont['execute']=="true") {
                        $showField = $cont['showField'];
                        $section =  $cont['section'];
                        $d = dirname(__FILE__)."/../../configuracion/";
                        $f = $d.$cont['file'];
                        if (file_exists($f)) {
                            $tmpFileConf = parse_ini_file($f, true);
                            $o = new $tmpFileConf['main']['class']($tmpFileConf);
                            if ((isset($o)) && (is_object($o))) {
                                if (method_exists($o, "executeMenu")) {
                                    $more = $o->executeMenu($section);
                                    foreach ($more as $o) {
                                        echo '<li><a ';
                                        if (isset($cont['desc'])) echo 'title="'.$cont['desc'].'" ';
                                        $lin = i::clean($o[$showField]);
                                        if ( isset($_GET['op'])) {
                                            $ge = explode("::", $_GET['op'], 2);
                                            if (sizeof($ge)>1) {
                                                if ($ge[1] == $lin) {
                                                    echo ' class="selected" ';
                                                }
                                            }
                                        }
                                        echo 'href="?op='.$cont['__uniqueLink'].'::'.$lin.$o['__execurl'].'&amp;__nb__"';
                                        echo '>';
                                        if (isset($cont['img'])) {
                                            echo '<img src="./icons/'.$cont['img'].'" alt="'.$sm.'" />';
                                        }
                                        echo $o[$showField];
                                        echo '</a></li>';
                                    }
                                }
                            }
                        }
                        continue;
                    }

                    echo '<li><a ';
                    if (isset($cont['desc'])) {
                        echo 'title="' . $this->getMLval($cont['desc'], $cont, 'desc') . '" ';
                    } else {
                        echo 'title="' . $this->getMLval($sm, $cont) . '" ';
                    }
                    echo 'href="?op='.$cont['__uniqueLink'].'"';
                    if (isset($_GET['op']) && $cont['__uniqueLink']==$_GET['op']) echo ' class="selected" ';
                    echo '>';
                    if (isset($cont['img'])) echo '<img src="./icons/'.$cont['img'].'" alt="'.$sm.'" />';
                    echo $this->getMLval($sm, $cont);
                    if (isset($cont['sql'])) {
                        $r = new con($cont['sql']);
                        if (!$r->getError()) {
                            $r->setResultType('NUM');
                            if ($retValue = $r->getResult()) {
                                echo " [{$retValue[0]}]";
                            }
                        }
                    }
                    echo '</a></li>';
                }
                echo '</ul>';
            }
            echo '</li>';
        }
        echo '</ul>';
    }

    public function drawQuickMenu($menuId = "")
    {
        echo '<div class="menuopts">
            <span class="change_menu" title="'.$this->l->l('maximizar/minimizar').'"></span>
        </div>';
        echo '<ul id="'.$menuId.'quickMenuUl">';
        foreach ($this->_menu as $m=>$cont) {
            if ($m=="&nbsp;") {
                continue;
            }

            if ($m=="main") continue;

            if ( (isset($cont['nodraw'])) && ($cont['nodraw']) ) continue;
            if ( (!isset($this->submenu[$m])) || (sizeof($this->submenu[$m]) == 0) ) continue;

            echo '<li><img src="./icons/more.png" class="more" alt="Más"/>';
            echo '<p class="head"';
            echo ' title="'.$this->getMLval($m, $cont).'"';
            echo '>&nbsp;</p>';
            if (isset($this->submenu[$m])) {
                echo '<ul>';
                foreach ($this->submenu[$m] as $sm=>$cont) {
                    //if (preg_match("/execute\((.*)\)/", $sm, $res)) {
                    if (isset($cont['execute']) && $cont['execute']=="true") {
                        $showField = $cont['showField'];
                        $section =  $cont['section'];
                        $d = dirname(__FILE__)."/../../configuracion/";
                        $f = $d.$cont['file'];
                        if (file_exists($f)) {
                            $tmpFileConf = parse_ini_file($f, true);
                            $o = new $tmpFileConf['main']['class']($tmpFileConf);
                            if ((isset($o)) && (is_object($o))) {
                                if (method_exists($o, "executeMenu")) {
                                    $more = $o->executeMenu($section);
                                    foreach ($more as $o) {
                                        echo '<li><a ';
                                        if (isset($cont['desc'])) echo 'title="'.$cont['desc'].'" ';
                                        $lin = i::clean($o[$showField]);
                                        if ( isset($_GET['op'])) {
                                            $ge = explode("::", $_GET['op'], 2);
                                            if (sizeof($ge)>1) {
                                                if ($ge[1] == $lin) {
                                                    echo ' class="selected" ';
                                                }
                                            }
                                        }
                                        echo 'href="?op='.$cont['__uniqueLink'].'::'.$lin.$o['__execurl'].'&amp;__nb__"'; //.'::'.$lin
                                        echo '>';
                                        if (isset($cont['img'])) echo '<img src="./icons/'.$cont['img'].'" alt="'.$sm.'" />';
                                        echo $o[$showField];
                                        echo '</a></li>';
                                    }
                                }
                            }
                        }
                        continue;
                    }

                    echo '<li><a ';
                    echo 'title="'.$this->getMLval($sm, $cont).'" ';
                    echo 'href="?op='.$cont['__uniqueLink'].'"';
                    if (isset($_GET['op']) && $cont['__uniqueLink']==$_GET['op']) echo ' class="selected" ';
                    echo ' >';
                    if (isset($cont['img'])) echo '<img src="./icons/'.$cont['img'].'" alt="'.$sm.'" />';
                   // echo $sm;
                    if (isset($cont['sql'])) {
                        $r = new con($cont['sql']);
                        if (!$r->getError()) {
                            $r->setResultType('NUM');
                            if ($retValue = $r->getResult()) {
                                echo " [{$retValue[0]}]";
                            }
                        }
                    }
                    echo '</a></li>';
                }
                echo '</ul>';
            }
            echo '</li>';
        }
        echo '</ul>';
    }

    public function loadSelected()
    {
        $d = dirname(__FILE__)."/../../configuracion/";
        if (is_null($this->_selectedSubsection)) {
            $f = $d . "default.cfg";
        } else {
            if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['file'])) {
                $f = $d . $this->submenu[$this->_selectedSection][$this->_selectedSubsection]['file'];
            } else {
                return false;
            }
        }

        $this->selectedConf = parse_ini_file($f, true);
        $zz = array_keys($this->selectedConf);
        if (isset($zz[1])) {
            $men = $zz[1];
            if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['js'])) {
                $this->selectedConf['main']['js'] = $this->submenu[$this->_selectedSection][$this->_selectedSubsection]['js'];
            }
            if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['tinyConf'])) {
                $this->selectedConf['main']['tinyConf'] = $this->submenu[$this->_selectedSection][$this->_selectedSubsection]['tinyConf'];
            }
            if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['title'])) {
                $this->selectedConf['main']['tit'] = $this->submenu[$this->_selectedSection][$this->_selectedSubsection]['title'];
            }
            if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['cond'])) {
                $this->selectedConf[$men]['pltCond'] = $this->submenu[$this->_selectedSection][$this->_selectedSubsection]['cond'];
                if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['ops'])) {
                    $this->selectedConf[$men]['ops'] = $this->submenu[$this->_selectedSection][$this->_selectedSubsection]['ops'];
                }

            }
            if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['del'])) {
                $this->selectedConf[$men]['del'] = $this->submenu[$this->_selectedSection][$this->_selectedSubsection]['del'];
                if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['delreconfirm'])) {
                    $this->selectedConf[$men]['delreconfirm'] = $this->submenu[$this->_selectedSection][$this->_selectedSubsection]['delreconfirm'];
                }

            }

            if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['new'])) {
                $this->selectedConf[$men]['new'] = $this->submenu[$this->_selectedSection][$this->_selectedSubsection]['new'];
                if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['newFrom'])) {
                    $this->selectedConf[$men]['newFrom'] = $this->submenu[$this->_selectedSection][$this->_selectedSubsection]['newFrom'];
                    if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['newFromUnique'])) {
                        $this->selectedConf[$men]['newFromUnique'] = $this->submenu[$this->_selectedSection][$this->_selectedSubsection]['newFromUnique'];
                    }
                }
            }

            if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['nodel']) && isset($this->selectedConf[$men]['del'])) {
                unset($this->selectedConf[$men]['del']);
            }

            if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['nodelreconfirm']) && isset($this->selectedConf[$men]['delreconfirm'])) {
                unset($this->selectedConf[$men]['delreconfirm']);
            }

            if (isset($this->submenu[$this->_selectedSection][$this->_selectedSubsection]['nonew']) && isset($this->selectedConf[$men]['new'])) {
                unset($this->selectedConf[$men]['new']);
            }
        }
    }

    public function getSelTitle()
    {
        $tit = $this->getMLval($this->selectedConf['main']['tit'], $this->selectedConf['main'], 'tit');
        return (($tit)? $this->getMLval($this->selectedConf['main']['tit'], $this->selectedConf['main'], 'tit') : false);
    }

    public function getSelDesc()
    {
		if (isset($this->selectedConf['main']['desc'])) {
			$tit = $this->getMLval($this->selectedConf['main']['desc'], $this->selectedConf['main'], 'desc');
			if (isset($tit)) {
				return $tit;
			}
		}
		return false;
    }

    public function getSelClass()
    {
        if (isset($this->selectedConf['main']['class'])) return $this->selectedConf['main']['class'];
        if (isset($this->selectedConf['0']['class'])) return $this->selectedConf['0']['class'];
        return false;
    }

    public static function getURL()
    {
        if (isset($_GET['op'])) {
            $ret = "./?op=".$_GET['op']."&amp;";
            return $ret;
        }
    }

    /**
     * Devuelve el valor para la activación del FullPage basándose en los valores del cfg
     * Valores válidos en la configuración:
     *  - auto|2
     *  - manual|1
     *  - off|0
     *
     * @param object $o Objeto del tipo "Clase del módulo (tablon, tablon_edit, etc...)"
     * @param mixed $fpMAIN Sobreescribe el valor de la configuración (¿?¿?¿)
     * @return mixed string con auto o manual. Si se desactiva FullPage, devuelve false
     */
    public static function getFullPage($o, $fpMain = false)
    {
        $x = "manual";

        if ($fpMain !== false) {
            $x = $fpMain;
        } else if (isset($o->selectedConf['fullPage'])) {
            $x = $o->selectedConf['fullPage'];
        }

        switch ($x) {
            case "2":
            case "auto":
                return "auto";
                break;
            case "1":
            case "manual":
                return "manual";
                break;
            case "0":
            case "off":
                return false;
                break;
            default:
                return "manual";
                break;
        }
    }

    /* Kaian. This shouldn't be here. This belongs to Karma and this is a iVoz::NG hack =( */
    public function getEmpresaListBox(){
    	if ($this->get_karma_isMT() && $_SESSION['__ISGOD']){
    		$con = new con('select * from shared_empresa');
    		$html = '<ul class="karmaBarSelectorOpts"><li class="karmaBarSelectorOpts">Empresa: <select id="select_grupo_vinculado" >';
    		while($r = $con->getResult()){
    			$selected = ($_SESSION['__GRUPO_VINCULADO'] == $r['id_empresa'])?'selected="selected"':'';
    			$html .= '<option value='.$r['id_empresa'].' '.$selected.'>'.$r['nombre'].'</option>';
    		}
    		$html .= '</select></li></ul>';
    		return $html;
    	}
    }

    public function get_karma_timezone()
    {
        return (isset($this->_menu['main']['timezone']))? $this->_menu['main']['timezone']:false;
    }
    
    public function get_karma_isMT()
    {
        return (isset($this->_menu['main']['mt']))? $this->_menu['main']['mt']:false;
    }

    public function get_karma_mysql_timezone()
    {
        return (isset($this->_menu['main']['mysql_timezone']))? $this->_menu['main']['mysql_timezone']:false;
    }

    public function get_karma_date_lang()
    {
        return ((isset($this->_menu['main']['date_lang']))? $this->_menu['main']['date_lang']:((isset($this->_menu['main']['lang']))? $this->_menu['main']['lang']:false));
    }

    public function get_karma_date_separator()
    {
        return (isset($this->_menu['main']['date_separator']))? $this->_menu['main']['date_separator']:'/';
    }

    public function is_enabled_autoClass($prop)
    {
        return isset($this->externalModules[$prop]);
    }

    public function get_autoClass($prop)
    {
        return $this->externalModules[$prop];
    }

    public function register_autoClass($o)
    {
        foreach ($this->externalModules as $mod) {
            if (method_exists($mod, "enableContentObjectMethod")) {
                $method = $mod->enableContentObjectMethod();

                if (method_exists($o, $method)) {
                    /*
                     * call_user_method está deprecated...
                     */
                    //call_user_method($method, $o);
                    call_user_func(array($o, $method));
                }
            }
        }
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

}
