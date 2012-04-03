<?php


class modules_ng
{
    // Setea constantes para idioma / __TIPO / etc. además de las variables del objeto
    const LEGACYMODE = true;

    protected $_jsPath = "scripts/";
    protected $_cssPath = "css/";
    protected $_cssPrintPath = "css/";
    protected $_alternatecssPath = "css/";
    protected $_jsallinonePath = "scripts/";


    public $css = array();
    public $js = array();
    public $cssPrint = array();
    public $alternatecss = array();
    public $jsallinone = array();

    public $dinjs= array();


    public $title = "";
    public $titleSeparator = " - ";
    public $meta = array();

    public static $precarga,$cabecera,$pie,$exec,$trat;

    public static $consts = array();

    public static $user = false;

    protected $_aConf;
    public static $data;

    public static $instance = null;
    public $accessLevel = 0;
    public $modulo = "index";
    public $niv = 0;
    protected $_dynMod = null;
    public $dynModID = null, $dynModIden = null;

    public $ajax = false;


    public static $defLang,$lang,$aLangs;

    // se carga llamando al método loadMLCalendar desde ./libs/ml.calendars.php
    public $calendars = array();


    function __construct($iniFile)
    {
        if ($iniFile === false) {
            $iniFile = dirname(__FILE__) .'/../../modules.ini.php';
        }
        if (!$this->_aConf = parse_ini_file($iniFile, true)) {
            return false;
        }
    }

    /**
     * Inicializa la sesión con el handler correspondiente,
     * si el handler no existe se usara el handler por defecto (files)
     *
     * Valores válidos para session_handler en el modules.ini:
     *     - memcache
     *
     * Otros posibles valores de configuración:
     *     - session_handler.host
     *     - session_handler.port
     *
     * @param string $name Nombre que queremos darle a la sesión
     */
    function startSession($name = NULL)
    {
        if (isset($this->_aConf[0]['session_handler'])) {
            switch($this->_aConf[0]['session_handler']) {
                case 'memcache':
                    $host = "localhost";
                    $port = 11211;
                    $lifetime = 1440;

                    if (isset($this->_aConf[0]['session_handler.host'])) {
                        $host = $this->_aConf[0]['session_handler.host'];
                        MemcacheSessionHandler::$host = $host;
                    }
                    if (isset($this->_aConf[0]['session_handler.port'])) {
                        $port = $this->_aConf[0]['session_handler.port'];
                        MemcacheSessionHandler::$port = $port;
                    }

                    $memcache = new Memcache();
                    $memcache->addServer($host, $port);
                    if (@$memcache->connect($host, $port)) {
                        MemcacheSessionHandler::$memcache = $memcache;
                        session_set_save_handler(
                            array("MemcacheSessionHandler", "open"),
                            array("MemcacheSessionHandler", "close"),
                            array("MemcacheSessionHandler", "read"),
                            array("MemcacheSessionHandler", "write"),
                            array("MemcacheSessionHandler", "destroy"),
                            array("MemcacheSessionHandler", "gc")
                        );
                    }
                    break;
            }
        }

        if (isset($name)) {
            session_name($name);
        }

        if ((isset($_SERVER['HTTP_USER_AGENT']))
            && (strpos($_SERVER['HTTP_USER_AGENT'], "Flash") !== false)
            && (isset($_GET['arg2']))) {
            session_id(preg_replace("/[^0-9a-zA-Z]+/", "", $_GET['arg2']));
        }

        session_start();
        $this->_manageGenerate();
    }

    /**
     * _manageGenerate
     *
     * @access private
     * @return boolean
     *
     * Usar esta función tras session_start.
     * Regenera cada cierto tiempo (60) el id de $_SESSION
     */
    private function _manageGenerate()
    {
        if (!isset($_SESSION['generated']) || $_SESSION['generated'] < (time() - 60)) {
            session_regenerate_id();
            $_SESSION['generated'] = time();
            return true;
        }
        return false;
    }

    /**
     *
     * @param string $iniFile Ruta al fichero de configuración
     * @return modules_ng
     */
    static public function getInstance($iniFile = false)
    {
        if (self::$instance === NULL) self::$instance = new modules_ng($iniFile);
        return self::$instance;
    }

    /**
     *
     * @param unknown_type $m
     * @return modules_ng
     */
    public function setModule($m)
    {
        $module = preg_replace("/[^0-9a-zA-Z_]+/", "", $m);
        if ($module != "") {
            $this->modulo =  $module;
        }

        if (self::LEGACYMODE) {
            define("__MODULO", $this->modulo);
        }

        if ( (isset($this->_aConf[0]['autoAjax'])) && ((bool)$this->_aConf[0]['autoAjax'])) {
                if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']))
                     && ($_SERVER['HTTP_X_REQUESTED_WITH']=="XMLHttpRequest") ) {
                    $this->setAjax();
                }
        }
        return $this;
    }

    /**
     * Método encargado de checkear el login
     * Si el login es correcto setea las propiedades $user y $accessLevel
     *
     * @return modules_ng
     */
    public function checkLogin()
    {
        if (isset($this->_aConf['login'])) {
            switch (true) {
                case ((isset($this->_aConf['login']['file']))
                     && (file_exists($this->_aConf['login']['file']))):
                    require($this->_aConf['login']['file']);
                    break;
                case ((isset($this->_aConf['login']['class']))
                     && (isset($this->_aConf['login']['method']))):
                    $estructura = array(
                       $this->_aConf['login']['class'],
                       $this->_aConf['login']['method']
                    );
                    if (is_callable($estructura)) {
                        $ret = call_user_func($estructura);
                        if (is_array($ret)) {
                            list(self::$user, $this->accessLevel) = $ret;
                        }
                    }
                    break;
            }
        }

        if (self::LEGACYMODE) {
            define("__TIPO", $this->accessLevel);
        }
        return $this;
    }


    /**
     *
     * @return modules_ng
     */
    public function setMeta()
    {

        if (isset($this->_aConf[0]['page_title_' . self::$lang])) {
            $vlrParam = $this->_aConf[0]['page_title_' . self::$lang];
        } else {
            $vlrParam = $this->_aConf[0]['page_title'];
        }
        $this->meta['title'] = i::set($vlrParam);

        if (isset($this->_aConf[0]['meta_titulo_' . self::$lang])) {
            $vlrParam = $this->_aConf[0]['meta_titulo_' . self::$lang];
        } else {
            $vlrParam = $this->_aConf[0]['meta_titulo'];
        }
        $this->meta['meta_titulo'] = i::set($vlrParam);

        if (isset($this->_aConf[0]['meta_desc_' . self::$lang])) {
            $vlrParam = $this->_aConf[0]['meta_desc_' . self::$lang];
        } else {
            $vlrParam = $this->_aConf[0]['meta_desc'];
        }
        $this->meta['meta_desc'] = i::set($vlrParam);

        if (isset($this->_aConf[0]['meta_keys_' . self::$lang])) {
            $vlrParam = $this->_aConf[0]['meta_keys_' . self::$lang];
        } else {
            $vlrParam = $this->_aConf[0]['meta_keys'];
        }
        $this->meta['meta_keys'] = i::set($vlrParam);

        $vlrParam = $this->_aConf[0]['meta_language'];
        $this->meta['meta_language'] = i::set($vlrParam);

        if (isset($this->_aConf[0]['meta_url_' . self::$lang])) {
            $vlrParam = $this->_aConf[0]['meta_url_' . self::$lang];
        } else {
            $vlrParam = $this->_aConf[0]['meta_url'];
        }
        $this->meta['meta_url'] = i::set($vlrParam);

        $this->meta['meta_autor'] = i::set($this->_aConf[0]['meta_autor']);
        $this->meta['meta_copy'] = i::set($this->_aConf[0]['meta_copy']);
        $this->meta['pag_inicio'] = i::set($this->_aConf[0]['meta_pag_inicio']);

        return $this;
    }

    // Esta es por seccion
    public function checkMetaRefresh()
    {
        $vlrPos = $this->niv . '::' . $this->modulo;
        if (isset($this->_aConf[$vlrPos]['meta_refresh'])) {
            $vlrParam = $this->_aConf[$vlrPos]['meta_refresh'];
            $this->meta['meta_refresh'] = i::set($vlrParam);
        }
    }

    protected function _checkPermisos()
    {
        $i = 0;
        while (isset($this->_aConf[$i])) {
            $vlrPos = $i . "::" . $this->modulo;
            // Si encontramos la sección en un permiso superior al accessLevel
            if ((isset($this->_aConf[$vlrPos])) && ($i > $this->accessLevel)) {
                if ($this->_aConf[$i]['nopermiso']) {
                    $vlrPos = $this->accessLevel . '::' . $this->_aConf[$i]['nopermiso'];
                    if (isset($this->_aConf[$vlrPos])) {
                       return array(
                           $this->accessLevel,
                           $this->accessLevel.'::'.$this->_aConf[$i]['nopermiso'],
                           'noperm'
                       );
                    }
                }
            }
            $i++;
        }
        return false;
    }


    public function checkPermisos()
    {
        return $this->_checkPermisos();
    }


    // NOT TESTED!
    protected function _checkDinamicSection($niv)
    {
        $dynamicSection = $niv . "::%";
        switch (true) {
            case (isset($this->_aConf[$dynamicSection]['function'])):
                return $this->_checkDinamicSectionFunction($niv);
                break;
            // Supongo que se podrían implementar más funcionalidades
            // para secciones dinámicas...
            default:
                return false;
                break;
        }
    }


    // NOT TESTED!
    protected function _checkDinamicSectionFunction($niv)
    {
        $dynamicSection = $niv . "::%";
        $nombreFuncion = $this->_aConf[$dynamicSection]['function'];

        if (strpos("$nombreFuncion", "::")) { // Método estático
            $estructura = explode("::", $nombreFuncion, 2);
        } else {
            $estructura= $nombreFuncion;
        }

        if (!is_callable($estructura)) {
            return false;
        }
        if (!$aDynSecs = call_user_func($estructura, $this->modulo)) {
            return false;
        }



        if (is_array($aDynSecs) && in_array($this->modulo, $aDynSecs['mods'])
           && ((int)$this->accessLevel >= (int)$aDynSecs['NIVEL'])) {

            $seccion = $niv."::%";

            if (isset($aDynSecs['css'])) {
                $tmpNiv = $niv."::".$this->modulo;
                $this->_aConf[$tmpNiv]['css'] = $aDynSecs['css'];
            }
            if (isset($aDynSecs['js'])) {
                $tmpNiv = $niv."::".$this->modulo;
                $this->_aConf[$tmpNiv]['js'] = $aDynSecs['js'];
            }
            // !!!!! WARNING NOT TESTED!!!
            /*if (isset($aDynSecs['request_lang'])) {
                $autoLang = $aDynSecs['request_lang'];
            }
            if (isset($aDynSecs['IDSEC'])) {
                $_IDSEC = $aDynSecs['IDSEC'];
            }
            if (isset($aDynSecs['IDENSEC'])) {
                $_IDENSEC = $aDynSecs['IDENSEC'];
            }
             */

            if (isset($aDynSecs['ajax'])) {
                $this->_aConf[$seccion]['ajax'] = "1";
            }

            if (isset($aDynSecs['trat'])) {
                $this->_aConf[$seccion]['trat'] = $aDynSecs['trat'];
            }

            $this->_dynMod = $this->modulo;
            if (isset($aDynSecs['IDSEC'])) {
                $this->dynModID = $aDynSecs['IDSEC'];
            }
            if (isset($aDynSecs['IDENSEC'])) {
                $this->dynModIden = $aDynSecs['IDENSEC'];
            }
            return array($niv, $seccion);

        } else {
            if ( !((int)$this->accessLevel >= (int)$aDynSecs['NIVEL']) ) {
                for ($i=0;$i=$aDynSecs['NIVEL'];$i++) {
                    if (isset($this->_aConf[$i]['nopermiso'])) {
                        return array($i, $this->_aConf[$i]['nopermiso']);
                    }
                }
            }
        }
        return false;
    }

    /**
     *
     * @return modules_ng
     */
    public function loadModule()
    {
        $niv = $this->accessLevel;
        do {
            $seccion = $niv . "::" . $this->modulo;
            $niv--;
        } while ((!isset($this->_aConf[$seccion])) && ($niv >= 0));

        // recuperamos el último decrimento de niv
        // si éste es menor que cero
        // el modulo será del nivel 0 (no hay -1)
        ($niv++)&&($niv<0)&&($seccion = $niv."::".$modulo);

        // Si no existe el módulo para el nivel de permisos en el que estemos:
        if (!isset($this->_aConf[$seccion])) {
            if ((!$ret = $this->_checkPermisos()) &&
               (!$ret = $this->_checkDinamicSection($niv))) {
                // No se ha cumplido ninguna de las dos opciones
                $this->notFound();
            }
            list($niv, $seccion) = $ret;
        }

        $this->niv = $niv;

        if (!isset($this->_aConf[$seccion]['no_css'])) {
            $this->setBaseSection("css");
            $this->setBaseSection("cssPrint");
            $this->setBaseSection("alternatecss");
            $this->setSection("css");
            $this->setSection("cssPrint");
        }

        if (isset($this->_aConf[$seccion]['ajax'])) {
            $this->setAjax();
        }

        $this->setBaseSection("js");
        $this->setBaseSection("jsallinone");
        $this->setDynamicJS();
        $this->setSection("js");

        $this->setSectionConstants();

        $this->checkMetaRefresh();

        if (!$this->_setVar("precarga")) {
            self::$precarga = false;
        }
        if (!$this->_setVar("cabecera")) {
            die("NO HAY CABECERA DEFINIDA");
        }
        if (!$this->_setVar("pie")) {
            die("NO HAY PIE DEFINIDO");
        }

        if (!isset($this->_aConf[$seccion]['f'])) {
            die("NO HAY FICHERO DE EJECUCION DEFINIDO");
        }
        self::$exec = $this->_aConf[$seccion]['f'];
        if (isset($this->_aConf[$seccion]['trat'])) {
            self::$trat = $this->_aConf[$seccion]['trat'];
        }

        if (isset($this->_aConf[$seccion]['title'])) {
            $this->appendTitle($this->_aConf[$seccion]['title']);
        }

        if (self::LEGACYMODE) {
            $this->registerCONST(
                array(
                    "cabecera"=>true,
                    "pie"=>true,
                    "FICH"=>"exec",
                    "PG_TITLE"=>"title",
                    "TRAT"=>"trat",
                    "USER"=>"user"
                )
            );
        }
        return $this;
    }

    protected function setSectionConstants()
    {
        if (!isset($this->_aConf[$this->niv . '::' .$this->modulo]['const0'])) {
            return false;
        }

        $vlrPos = $this->niv . '::' . $this->modulo;
        for ($i=0; isset($this->_aConf[$vlrPos]['const' . $i]); $i++) {
            list($attr, $val) = explode("=", $this->_aConf[$vlrPos]['const' . $i]);
            self::$consts[$attr] = $val;
        }
        return;
    }

    protected function registerCONST($a)
    {
        foreach ($a as $idx => $vlr) {
            if (isset($this->$idx)) {
                $_idx= $this->$idx;
            } elseif (isset(self::$$idx)) {
                $_idx= self::$$idx;
            }

            if ($vlr!==true) {
                if (isset($this->$vlr)) {
                    $_vlr= $this->$vlr;
                } elseif (isset(self::$$vlr)) {
                    $_vlr= self::$$vlr;
                }
            } else {
                $_vlr = $vlr;
            }
            define("__" . strtoupper($idx), (($vlr === true)?$_idx:$_vlr));
        }
    }

    public static function makePathForFiles(&$vlr, $clave, $path)
    {
        $vlr = $path . $vlr;
    }

    protected function _addArrCSS($c, $var)
    {
        $this->$var= array_keys(array_flip($this->$var) + array_flip($c));
    }

    public function addCustom($o, $var)
    {
        if (is_array($o)) {
            $this->_addCustomArr($o, $var);
        } else {
            $this->_addCustomArr(array($o), $var);
        }
    }

    private function _addCustomArr(array $c, $var)
    {
        $this->$var = array_keys(array_flip($this->$var) + array_flip($c));
    }

    public function addCSS($c)
    {
        $this->addCustom($c, "css");
    }

    public function addJS($j)
    {
        $this->addCustom($j, "js");
    }

    protected function _setSeccion($s, $indice)
    {
        if (!isset($this->_aConf[$indice][$s])) {
            return false;
        }
        $current = explode(",", $this->_aConf[$indice][$s]);

        $dfltpathVar = '_' . $s . 'Path';
        if (isset($this->_aConf[$indice][$s . '_path'])) {
            $thePath = $this->_aConf[$curNiv][$s . '_path'];
        } else {
            $thePath = $this->$dfltpathVar;
        }

        array_walk($current, array($this, 'makePathForFiles'), $thePath);
        $this->$s = array_keys(array_flip($this->$s) + array_flip($current));
        return true;
    }

    protected function setSection($s)
    {
        $seccion = $this->niv . '::' . $this->modulo;
        $this->_setSeccion($s, $seccion);
    }

    public function addSection($s)
    {
        $seccion = $this->niv . '::' . $this->modulo;
        $this->_setSeccion($s, $seccion);
    }

    protected function setBaseSection($s)
    {
        $n = $this->niv;
        while ($n>=0) {
            if (!$this->_setSeccion($s, $n)) {
                $n--;
                continue;
            }
            $n--;
        }
    }

    protected function setDynamicJS()
    {
        $niv = $this->niv;
        if (isset($this->_aConf[$niv]['dinamicJS'])) {
            $this->dinjs = explode(",", $this->_aConf[$niv]['dinamicJS']);
        }
    }

    protected function _setVar($variable)
    {
        $n = $this->niv;

        if (!isset($aConf[$n . '::' . $this->modulo][$variable])) {
                do {
                    if (isset($this->_aConf[$n][$variable])) {
                        self::$$variable = $this->_aConf[$n][$variable];
                        return true;
                    } else $n--;
                } while ($n>=0);
        } else {
            self::$$variable = $aConf[$n . '::' . $this->modulo][$variable];
            return true;
        }
        return false;
    }

    /**
     *
     * @return modules_ng
     */
    public function setPageTitle()
    {
        if (isset($this->_aConf[0]['title_' . self::$lang])) {
            $this->title = $this->_aConf[0]['title_' . self::$lang];
        } else {
            $this->title = $this->_aConf[0]['title'];
        }
        return $this;
    }

    /**
     *
     * @return modules_ng
     */
    public function appendTitle($t)
    {
        $this->title = $t . $this->titleSeparator . $this->title;
        return $this;
    }

    public function prependTitle($t)
    {
        $this->title .= $this->titleSeparator . $t;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function loadMLCalendar()
    {
        $this->$calendars  = require(dirname(__FILE__) . '/../libs/ml.calendars.php');
    }

    public function getMeta($m,$echo = false)
    {
        if (isset($this->meta[$m . '_' . self::$lang])) {
            if ($echo !== false) {
                echo "\t" . str_replace('%m', $this->meta[$m . '_' . self::$lang], $echo) . "\n";
                return;
            }
            return $this->meta[$m . '_' . self::$lang];
        } elseif (isset($this->meta[$m])) {
            if ($echo !== false) {
                echo "\t" . str_replace('%m', $this->meta[$m], $echo) . "\n";
                return;
            }
            return $this->meta[$m];
        }
        return false;
    }

    public function _checkDinamicLanguage()
    {
        if (isset($this->_aConf[0]['dynLang']) && !empty($this->_aConf[0]['dynLang'])) {
            $nombreFuncion = $this->_aConf[0]['dynLang'];

            if (strpos("$nombreFuncion", "::")) { // Método estático
                $estructura = explode("::", $nombreFuncion, 2);
            } else {
                $estructura= $nombreFuncion;
            }

            if (!is_callable($estructura)) {
                return false;
            }
            if (!$aDynLang = call_user_func($estructura)) {
                return false;
            }
            self::$lang = $aDynLang;
            $_SESSION['lang'] = self::$lang;
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param string $defLang Idioma por defecto
     * @return modules_ng
     */
    public function setLanguage($defLang=false)
    {

        self::$aLangs = explode(",", $this->_aConf[0]['langs']);

        if ($defLang !== false) {
            self::$defLang = $defLang;
        } else {
            if (isset($this->_aConf[0]['defLang'])) {
                self::$defLang = $this->_aConf[0]['defLang'];
            } else {
                self::$defLang = self::$aLangs[0];
            }
        }

        if ($ret = $this->_checkDinamicLanguage()) {
           return $this;
        }

        if ((isset($_GET['lang'])) && (in_array($_GET['lang'], self::$aLangs))) {
            self::$lang = $_GET['lang'];
            $_SESSION['lang'] = self::$lang;
            return $this;
        }

        if ((isset($_SESSION['lang'])) && (in_array($_SESSION['lang'], self::$aLangs))) {
            self::$lang = $_SESSION['lang'];
            return $this;
        }


        self::$lang = self::$defLang;

        return $this;
    }

    public function isAjax()
    {


        return $this->ajax;
    }

    public function setAjax($a = true)
    {
        $this->ajax = $a;
    }

    public function isTrat()
    {
        if (!isset(self::$trat)) {
            return false;
        }
        if (!isset($_POST)) {
            return false;
        }
        if (sizeof($_POST)<1) {
            return false;
        }
        return true;
    }

    public static function setAliveVar($name,$val)
    {
        self::$data[$name] = $val;
    }

    public static function g($name)
    {
        return self::$data[$name];
    }

    public function notFound()
    {
        i::_404();
    }
}

//ALIAS!! :)
class mng extends modules_ng
{
}
