<?php

abstract class login {
    public $user = false;
    public $pass = false;
    public $tmppass = false;
    public $userID;
    public $aUserROLID = array();

    protected $_error=false;

    public $kMenu;

    /**
     * Array con las secciones a las que puede acceder el usuario logueado
     * @var array
     */
    public $aMenu;

    protected $_loginType = 'HTTP';

    public $l;

    public function __construct($kMenu = false)
    {

        $this->kMenu = $kMenu;
        if ($kMenu->getLoginType()) {
            $this->_loginType = $kMenu->getLoginType();
        }
        $menu = $this->kMenu->getMenu();
        $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if (isset($menu['main']['loginLANGUAGE']) && $menu['main']['loginLANGUAGE']!='auto' ) {
            $language = $menu['main']['loginLANGUAGE'];
        }
         

        $this->l = new k_literal($language);

    }
    /**
     * Método abstracto que se encargará de comprobar que el usuario y contraseña son válidos
     * y setear los permisos de acceso en el array $this->aMenu
     */
    abstract protected function _checkUser();

    /*
     * Cada vez que algo hace que esto se llame, dios mata un mogwai
     */
    public function __get($name)
    {
        switch($name) {
            case 'u':
                return $this->user;
                break;
            case 'p':
                return $this->pass;
                break;

        }
    }

    /*
     * Cada vez que algo hace que esto se llame, dios mata un tarsero
     */
    public function __set($name, $value)
    {
        switch($name) {
            case 'u':
                $this->user = $value;
                break;
            case 'p':
                $this->pass = $value;
                break;

        }
    }


    protected function _getAuthMethodVariables()
    {
        if ($this->_loginType == "htmlForm") {
            return array(
                'variable' => $_POST,
                'user' => 'user',
                'pw' => 'pwd',
            );
        } else {
            return array(
                'variable' => $_SERVER,
                'user' => 'PHP_AUTH_USER',
                'pw' => 'PHP_AUTH_PW',
            );
        }

    }

    public function doLogin()
    {
        $methodVariables = $this->_getAuthMethodVariables();

        if (isset($_GET['logout'])) {
            $methodVariables['variable'][$methodVariables['user']] = 'logout';
            $methodVariables['variable'][$methodVariables['pw']] = 'logout';
            $_SESSION['__USER'] = false;
            $_SESSION['__PW'] = false;
        }


        if (isset($_SESSION['__USER']) && isset($_SESSION['__PW'])) {
            $this->user = $_SESSION['__USER'];
            $this->tmppass = $_SESSION['__PW'];
        }


        if (isset($methodVariables['variable'][$methodVariables['user']])
        && isset($methodVariables['variable'][$methodVariables['pw']])
        && !empty($methodVariables['variable'][$methodVariables['user']])
        && !empty($methodVariables['variable'][$methodVariables['pw']])) {
            $this->user = $methodVariables['variable'][$methodVariables['user']];
            $this->pass = $methodVariables['variable'][$methodVariables['pw']];
        }

        if (
        (isset($methodVariables['variable'][$methodVariables['user']])
        ||
        isset($methodVariables['variable'][$methodVariables['pw']])
        )
        && !isset($_GET['logout']))
        {
            $this->_error = true;
        }


        if ($this->user === 'logout' && $_COOKIE['logout'] === 'in') {
            header('HTTP/1.x 401 Unauthorized');
            header('WWW-Authenticate: Bogus');
            setcookie("logout", "out");
            echo $this->getLoginHtml($this->l);
            exit();

        }



        if (!$this->_checkUser()) {
            $this->autentificar();
            setcookie("logout", "in");
        }
    }

    protected function autentificar() {

        if ($this->_loginType == "htmlForm") {
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Cookie realm="Acme" form-action="'. i::rewrite_current(true, array('logout')).'"');
            header('Content-Type: text/html');
            echo $this->getLoginHtml($this->l);
        } else {
            header('WWW-Authenticate: Basic realm="'.$this->l->l('Acceso restringido, este sitio requiere autenticacion').'"');
            header('HTTP/1.0 401 Unauthorized');
            echo $this->getLoginHtml($this->l);
        }
        exit();
    }

    public function isAdminLogged($id) {
            return $this->userID == $id;
    }

    public function loginInfo() {
        return $this->l->l('Welcome')
               . ' <span id="karmaBarUser" title="session id => ' . (isset($_SESSION["__ID"])? $_SESSION['__ID'] : '') . '" >'
               . $this->user
               . '</span>'
               . " | "
               . '<a href="' . i::base_url() . '?logout" class="tooltip" title="' . $this->l->l('desconectar') . '">'
               . $this->l->l('desconectar')
               . '</a>';
    }






    public function getLoginHtml($l)
    {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $menu = $this->kMenu->getMenu();
        $html =
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es">
        <head>
        <title>'.$this->kMenu->getTitle().' - Karma 2.0 - Intranet Management System</title>
        <link rel="shortcut icon" href="./icons/iconKarma.ico" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="title" content="Irontec :: Internet y Sistemas sobre GNU / Linux" />
        <meta name="description" content="Irontec :: Internet y Sistemas sobre GNU / Linux" />
        <meta name="keywords" content="Irontec :: Internet y Sistemas sobre GNU / Linux" />
        <meta name="robots" content="all" />
        <meta name="author" content="Irontec"/>
        <meta name="Copyright" content="Irontec" />
        <meta http-equiv="X-UA-Compatible" content="chrome=1" />
        <link rel="stylesheet" href="./css/outestilo.css" type="text/css" media="all" />
        ' . ((isset($menu['main']['loginCSS']))? '<link rel="stylesheet" href="'. i::base_url() . $menu['main']['loginCSS'].'" type="text/css" media="all" />':'' ).'
        </head>
        <body>
        <div id="karmaBar"><div id="karmaBarOpts"><div id="karmaBarGeneralOpts"></div></div>
        <div id="karmaBarLogo">';
        if (!$this->kMenu->isKarmaBarLogoDisabled()) {
            $html.= '<a href="'.i::base_url().'" ><img src="icons/karmaLogo.png" alt="Karma 2.0 Irontec Cutting Edge development framework"/></a>';
        }
        $html.='
        </div>
        </div>
        <div id="main">
        <h1>'.$this->kMenu->getTitle().'</h1>
        ';
        if ($logo = $this->kMenu->getLogo()) {
            $html.=' <a href="' . i::base_url() . '" ><img src="' . $logo . '" class="logoc" alt="' . $this->kMenu->getTitle().'" /></a>';
        }
        $html.='


        <h3>'.$l->l('Acceso restringido, este sitio requiere autenticacion').'</h3>
        ';

        if ($this->_loginType == "htmlForm") {

            if ($this->_error) {
                $html.= $l->l('Usuario y/o contraseña incorrectos, por favor compruebe sus datos de acceso.');
            }

            $html.='
            <form action="'. i::rewrite_current(true, array('logout')).'" method="post">
            <fieldset>
                <p><label>'.$l->l('Username').':</label> <br /><input name="user" type="text" /></p>
                <p><label>'.$l->l('Password').':</label> <br /><input name="pwd" type="password" /></p>
                <p><button type="submit">'.$l->l('Sign in').'</button></p>
            </fieldset>
            </form>';
        } else {
            $html.='<a href="http://'.$_SERVER['HTTP_HOST'] . i::rewrite_current(true, array('logout')).'">'.$l->l('Click aquí para iniciar sesión').'</a>';
        }

        $html.= '</div>
        <div class="Clearer"></div>
        <div id="pieDeKarma">Irontec &copy; 2011 -- karma v2.0<br/><span id="s"><img src="./icons/karma2.0.logo.png" alt="Karma 2.0" /></span></div>
        </body>
        </html>';
        return $html;
    }

}
